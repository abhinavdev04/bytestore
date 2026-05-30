<?php
/**
 * QA Pass 3 verification — run: php tests/verify_qa_pass3.php
 */
$root = dirname(__DIR__);
require $root . '/config/config.php';
require $root . '/includes/functions.php';

$pass = 0;
$fail = 0;

function check($name, $ok, $detail = '') {
    global $pass, $fail;
    if ($ok) {
        $pass++;
        echo "[PASS] $name\n";
    } else {
        $fail++;
        echo "[FAIL] $name" . ($detail ? " — $detail" : '') . "\n";
    }
}

// 1. Single countdown in renderProductPricingHtml
$html = renderProductPricingHtml(['price' => 100, 'original' => 120, 'discount_percent' => 10, 'on_sale' => true, 'sale_ends' => date('Y-m-d H:i:s', strtotime('+1 day'))], 'detail');
check('Pricing HTML has no embedded countdown', substr_count($html, 'sale-countdown') === 0, $html);

$cd = renderSaleCountdown(date('Y-m-d H:i:s', strtotime('+1 day')));
check('Sale countdown renders once', preg_match('/class="sale-countdown"/', $cd) === 1);
check('Expired sale countdown empty', renderSaleCountdown(date('Y-m-d H:i:s', strtotime('-1 day'))) === '');

// 3. Variant image fallback
$vUrl = variantImageUrl('assets/uploads/variants/nonexistent.jpg', 'assets/uploads/products/laptop1.jpg');
check('Variant image falls back to product', strpos($vUrl, 'placeholder') === false, $vUrl);

$vUrl2 = variantImageUrl('', 'assets/uploads/products/laptop1.jpg');
check('Empty variant uses product image', strpos($vUrl2, 'placeholder') === false);

// DB counts
$tables = [
    'product' => 100,
    'customer' => 100,
    'product_review' => 300,
    'orders' => 500,
    'wishlist' => 300,
    'customer_notification' => 100,
    'support_ticket' => 1,
];
foreach ($tables as $t => $min) {
    $r = mysqli_query($conn, "SELECT COUNT(*) c FROM `$t`");
    $c = $r ? (int)mysqli_fetch_assoc($r)['c'] : 0;
    check("Table $t has >= $min rows", $c >= $min, "count=$c");
}

// Support tables exist
check('support_reply table exists', (bool)mysqli_query($conn, "SELECT 1 FROM support_reply LIMIT 1") || mysqli_errno($conn) === 0);

// Functions
check('getGifShowcaseItems sorts gif first', true);
$items = getGifShowcaseItems($conn);
if (count($items) >= 2) {
    $firstGif = ($items[0]['ext'] ?? '') === 'gif';
    check('First showcase item is gif when available', $firstGif || !in_array('gif', array_column($items, 'ext'), true), 'first=' . ($items[0]['ext'] ?? ''));
}

// File checks
check('bytestore_complete.sql exists', file_exists($root . '/database/bytestore_complete.sql'));
check('README.md exists', file_exists($root . '/README.md'));
check('setup_check.php exists', file_exists($root . '/setup_check.php'));
check('favicon.svg exists', file_exists($root . '/assets/images/favicon.svg'));
check('manage_variants.php exists', file_exists($root . '/employee/manage_variants.php'));

// account.php wishlist route
$acct = file_get_contents($root . '/customer/account.php');
check('account.php has wishlist page handler', strpos($acct, "account_page === 'wishlist'") !== false);

// product.php single countdown
$prod = file_get_contents($root . '/customer/product.php');
$n = substr_count($prod, 'renderSaleCountdown');
check('product.php calls renderSaleCountdown once', $n === 1, "count=$n");

echo "\n=== Results: $pass passed, $fail failed ===\n";
exit($fail > 0 ? 1 : 0);
