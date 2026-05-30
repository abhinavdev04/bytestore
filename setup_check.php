<?php
/**
 * ByteStore Setup Verification
 * Open in browser: http://localhost/bytestore/setup_check.php
 */
header('Content-Type: text/html; charset=utf-8');
$root = __DIR__;
$checks = [];

function addCheck(&$checks, $label, $ok, $detail = '') {
    $checks[] = ['label' => $label, 'ok' => $ok, 'detail' => $detail];
}

addCheck($checks, 'PHP version >= 8.0', version_compare(PHP_VERSION, '8.0.0', '>='), PHP_VERSION);

$configPath = $root . '/config/config.php';
if (file_exists($configPath)) {
    require $configPath;
    addCheck($checks, 'Database connection', isset($conn) && $conn && !mysqli_connect_errno(), mysqli_connect_error() ?: 'Connected');
} else {
    addCheck($checks, 'config/config.php exists', false);
}

$dirs = ['assets/uploads', 'assets/uploads/products', 'assets/uploads/variants', 'assets/uploads/profiles'];
foreach ($dirs as $d) {
    $path = $root . '/' . $d;
    $writable = is_dir($path) ? is_writable($path) : (@mkdir($path, 0755, true) && is_writable($path));
    addCheck($checks, "Writable: $d", $writable);
}

$requiredTables = ['customer','employee','product','category','orders','order_items','cart','product_variant','wishlist','product_review','customer_notification','support_ticket','support_reply'];
if (isset($conn) && $conn) {
    foreach ($requiredTables as $t) {
        $r = mysqli_query($conn, "SHOW TABLES LIKE '$t'");
        addCheck($checks, "Table: $t", $r && mysqli_num_rows($r) > 0);
    }
    $counts = ['product' => 100, 'customer' => 100, 'orders' => 500, 'product_review' => 300];
    foreach ($counts as $t => $min) {
        $r = mysqli_query($conn, "SELECT COUNT(*) c FROM `$t`");
        $c = $r ? (int)mysqli_fetch_assoc($r)['c'] : 0;
        addCheck($checks, "Demo data: $t (>= $min)", $c >= $min, "Found $c");
    }
}

addCheck($checks, 'database/bytestore_complete.sql', file_exists($root . '/database/bytestore_complete.sql'));
addCheck($checks, 'assets/images/favicon.svg', file_exists($root . '/assets/images/favicon.svg'));

$allOk = !in_array(false, array_column($checks, 'ok'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ByteStore Setup Check</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        h1 { color: #6366f1; }
        .ok { color: #16a34a; } .fail { color: #dc2626; }
        li { margin: 0.5rem 0; }
        .summary { padding: 1rem; border-radius: 8px; margin: 1.5rem 0; background: <?php echo $allOk ? '#dcfce7' : '#fee2e2'; ?>; }
    </style>
</head>
<body>
    <h1>ByteStore Setup Check</h1>
    <div class="summary"><strong><?php echo $allOk ? 'All checks passed' : 'Some checks failed'; ?></strong></div>
    <ul>
        <?php foreach ($checks as $c): ?>
            <li class="<?php echo $c['ok'] ? 'ok' : 'fail'; ?>">
                <?php echo $c['ok'] ? '✓' : '✗'; ?> <?php echo htmlspecialchars($c['label']); ?>
                <?php if ($c['detail']): ?><small>(<?php echo htmlspecialchars($c['detail']); ?>)</small><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
