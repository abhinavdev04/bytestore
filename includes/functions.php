<?php
/**
 * ByteStore Core Helper Functions
 * Security, formatting, and reusable query helpers
 */

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function getBasePath() {
    $dir = basename(dirname($_SERVER['PHP_SELF']));
    if (in_array($dir, ['customer', 'employee', 'pages', 'api'], true)) {
        return '../';
    }
    return '';
}

function assetUrl($path) {
    return getBasePath() . ltrim($path, '/');
}

function productImageUrl($path) {
    return resolveProductImage($path);
}

function getProjectRoot() {
    return dirname(__DIR__);
}

function resolveProductImagePath($path) {
    if (empty($path)) return null;

    $root = getProjectRoot();
    $relative = ltrim(str_replace('\\', '/', $path), '/');
    $full = $root . '/' . $relative;

    if (file_exists($full)) {
        return $relative;
    }

    $dir = dirname($full);
    $base = pathinfo($full, PATHINFO_FILENAME);
    $extensions = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg'];
    if (is_dir($dir)) {
        foreach ($extensions as $ext) {
            $candidate = $dir . '/' . $base . '.' . $ext;
            if (file_exists($candidate)) {
                return str_replace('\\', '/', substr($candidate, strlen($root) + 1));
            }
        }
        foreach (scandir($dir) ?: [] as $file) {
            if (stripos($file, $base) !== false && preg_match('/\.(webp|jpe?g|png|gif|svg)$/i', $file)) {
                return dirname($relative) . '/' . $file;
            }
        }
    }

    return null;
}

function resolveProductImage($path) {
    $resolved = resolveProductImagePath($path);
    if ($resolved) {
        return assetUrl($resolved);
    }
    return assetUrl('assets/images/placeholder.svg');
}

function variantImageUrl($variantPath, $productPath) {
    $variantResolved = resolveProductImagePath($variantPath);
    if ($variantResolved) {
        return assetUrl($variantResolved);
    }
    return resolveProductImage($productPath);
}

function formatPrice($amount) {
    return 'Rs. ' . number_format((float)$amount, 2);
}

function renderStars($rating, $size = 'sm') {
    $rating = max(0, min(5, (float)$rating));
    $full = (int)floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '<span class="star-rating star-rating--' . e($size) . '" aria-label="' . e($rating) . ' out of 5 stars">';
    for ($i = 0; $i < $full; $i++) $html .= '<i class="fa-solid fa-star star star--full"></i>';
    if ($half) $html .= '<i class="fa-solid fa-star-half-stroke star star--half"></i>';
    for ($i = 0; $i < $empty; $i++) $html .= '<i class="fa-regular fa-star star star--empty"></i>';
    $html .= '</span>';
    return $html;
}

function sanitizeInt($val, $default = 0) {
    return filter_var($val, FILTER_VALIDATE_INT) !== false ? (int)$val : $default;
}

function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // Nepal mobile numbers: 10 digits starting with 9 (e.g. 9800000000)
    // Also accepts landline formats like 01-4XXXXXX
    $cleaned = preg_replace('/[\s\-()]/', '', $phone);
    return preg_match('/^(\+977)?[9][0-9]{9}$/', $cleaned)
        || preg_match('/^0[1-9][0-9]{6,7}$/', $cleaned);
}

function validatePassword($password) {
    // Min 8 chars, at least one uppercase, one lowercase, one digit
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    return true;
}

function validateName($name) {
    $name = trim($name);
    // Min 3, max 100, only letters, spaces, dots, apostrophes, hyphens
    return strlen($name) >= 3
        && strlen($name) <= 100
        && preg_match('/^[a-zA-Z\s.\'\-]+$/', $name);
}

function validateProductName($name) {
    $name = trim($name);
    return strlen($name) >= 3 && strlen($name) <= 200;
}

function validateProductPrice($price) {
    $price = (float)$price;
    return $price >= 0.01 && $price <= 9999999.99;
}

function validateProductStock($stock) {
    return is_numeric($stock) && (int)$stock >= 0 && (int)$stock <= 99999;
}

function validateAddress($address) {
    return strlen(trim($address)) >= 10 && strlen(trim($address)) <= 500;
}

function flashMessage($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function getCartCount($conn, $customer_id) {
    $id = (int)$customer_id;
    $res = mysqli_query($conn, "SELECT COALESCE(SUM(quantity), 0) as cnt FROM cart WHERE customer_id = $id");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        return (int)$row['cnt'];
    }
    return 0;
}

function getWishlistCount($conn, $customer_id) {
    $id = (int)$customer_id;
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM wishlist WHERE customer_id = $id");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        return (int)$row['cnt'];
    }
    return 0;
}

function isInWishlist($conn, $customer_id, $product_id) {
    $cid = (int)$customer_id;
    $pid = (int)$product_id;
    $res = mysqli_query($conn, "SELECT wishlist_id FROM wishlist WHERE customer_id = $cid AND product_id = $pid LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function trackRecentlyViewed($conn, $customer_id, $product_id) {
    $cid = (int)$customer_id;
    $pid = (int)$product_id;
    mysqli_query($conn, "INSERT INTO recently_viewed (customer_id, product_id) VALUES ($cid, $pid)
        ON DUPLICATE KEY UPDATE viewed_at = CURRENT_TIMESTAMP");
    mysqli_query($conn, "DELETE FROM recently_viewed WHERE customer_id = $cid AND view_id NOT IN (
        SELECT view_id FROM (SELECT view_id FROM recently_viewed WHERE customer_id = $cid ORDER BY viewed_at DESC LIMIT 20) t
    )");
}

function updateProductRatings($conn, $product_id) {
    $pid = (int)$product_id;
    mysqli_query($conn, "UPDATE product SET 
        rating_avg = COALESCE((SELECT ROUND(AVG(rating),2) FROM product_review WHERE product_id = $pid), 0),
        rating_count = COALESCE((SELECT COUNT(*) FROM product_review WHERE product_id = $pid), 0)
        WHERE product_id = $pid");
}

function getProductEffectivePrice($product, $variant = null) {
    if ($variant && isset($variant['variant_price'])) {
        return (float)$variant['variant_price'];
    }
    return (float)$product['product_price'];
}

function getProductEffectiveStock($product, $variant = null) {
    if ($variant && isset($variant['variant_stock'])) {
        return (int)$variant['variant_stock'];
    }
    return (int)$product['product_stock'];
}

function getDiscountPercent($price, $original) {
    if (!$original || $original <= $price) return 0;
    return (int)round((($original - $price) / $original) * 100);
}

function expireProductSales($conn) {
    static $ran = false;
    if ($ran || !$conn) return;
    $ran = true;
    @mysqli_query($conn, "UPDATE product SET is_sale_active = 0
        WHERE is_sale_active = 1 AND sale_end_date IS NOT NULL AND sale_end_date < NOW()");
}

function isSaleCurrentlyActive($product) {
    if (empty($product['is_sale_active'])) return false;
    $pct = (float)($product['discount_percent'] ?? 0);
    if ($pct <= 0) return false;
    $now = time();
    if (!empty($product['sale_start_date']) && strtotime($product['sale_start_date']) > $now) return false;
    if (!empty($product['sale_end_date']) && strtotime($product['sale_end_date']) < $now) return false;
    return true;
}

function getProductPricing($product) {
    $base = (float)($product['product_price'] ?? 0);
    $list = (float)($product['original_price'] ?? 0);
    if ($list <= 0) $list = $base;

    if (isSaleCurrentlyActive($product)) {
        $pct = (float)$product['discount_percent'];
        $sale = round($list * (1 - $pct / 100), 2);
        return [
            'price' => $sale,
            'original' => $list,
            'discount_percent' => (int)round($pct),
            'on_sale' => true,
            'sale_ends' => $product['sale_end_date'] ?? null,
        ];
    }

    if ($list > $base) {
        return [
            'price' => $base,
            'original' => $list,
            'discount_percent' => getDiscountPercent($base, $list),
            'on_sale' => false,
            'sale_ends' => null,
        ];
    }

    return [
        'price' => $base,
        'original' => 0,
        'discount_percent' => 0,
        'on_sale' => false,
        'sale_ends' => null,
    ];
}

function renderSaleCountdown($saleEnds) {
    if (!$saleEnds) return '';
    $ts = strtotime($saleEnds);
    if (!$ts || $ts <= time()) return '';
    return '<div class="sale-countdown" data-sale-end="' . (int)$ts . '"><i class="fa-solid fa-clock"></i> <span class="sale-countdown__label">Sale ends in</span> <span class="sale-countdown__timer">--:--:--</span></div>';
}

function renderProductPricingHtml($pricing, $size = 'card') {
    $html = '<div class="product-pricing product-pricing--' . e($size) . '">';
    $html .= '<span class="product-pricing__current">' . formatPrice($pricing['price']) . '</span>';
    if (!empty($pricing['original']) && $pricing['original'] > $pricing['price']) {
        $html .= '<span class="product-pricing__original">' . formatPrice($pricing['original']) . '</span>';
    }
    if (!empty($pricing['discount_percent'])) {
        $html .= '<span class="product-pricing__badge">' . (int)$pricing['discount_percent'] . '% OFF</span>';
    }
    $html .= '</div>';
    return $html;
}

function getCategories($conn) {
    $result = mysqli_query($conn, "SELECT c.*, COUNT(p.product_id) as product_count 
        FROM category c LEFT JOIN product p ON c.category_id = p.category_id 
        GROUP BY c.category_id HAVING product_count > 0 ORDER BY c.category_name");
    $cats = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cats[] = $row;
        }
    }
    return $cats;
}

function getCategoryIcon($name) {
    $icons = [
        'Laptops' => 'fa-laptop', 'Desktops' => 'fa-desktop', 'Gaming' => 'fa-gamepad',
        'Phones' => 'fa-mobile-screen', 'Tablets' => 'fa-tablet-screen-button', 'Wearables' => 'fa-clock',
        'Audio' => 'fa-headphones', 'Monitors' => 'fa-display', 'Storage' => 'fa-hard-drive',
        'Processors' => 'fa-microchip', 'Graphics' => 'fa-puzzle-piece', 'Motherboards' => 'fa-sitemap',
        'Memory' => 'fa-memory', 'Keyboards' => 'fa-keyboard', 'Networking' => 'fa-wifi',
        'Printers' => 'fa-print', 'Software' => 'fa-compact-disc', 'Cameras' => 'fa-camera',
        'Cooling' => 'fa-fan', 'Power' => 'fa-plug', 'Accessories' => 'fa-plug',
        'Servers' => 'fa-server', 'Webcams' => 'fa-video', 'PC Cases' => 'fa-box',
    ];
    foreach ($icons as $key => $icon) {
        if (stripos($name, $key) !== false) return $icon;
    }
    return 'fa-box';
}

function renderCategoryIcon($name) {
    return '<i class="fa-solid ' . e(getCategoryIcon($name)) . '" aria-hidden="true"></i>';
}

function renderBreadcrumbs($items) {
    if (empty($items)) return '';
    $html = '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>';
    $last = count($items) - 1;
    foreach ($items as $i => $item) {
        $html .= '<li>';
        if ($i < $last && !empty($item['url'])) {
            $html .= '<a href="' . e($item['url']) . '">' . e($item['label']) . '</a>';
        } else {
            $html .= '<span aria-current="page">' . e($item['label']) . '</span>';
        }
        $html .= '</li>';
    }
    $html .= '</ol></nav>';
    return $html;
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function getCompareList() {
    if (!isset($_SESSION['compare'])) {
        $_SESSION['compare'] = [];
    }
    return array_slice(array_unique(array_map('intval', $_SESSION['compare'])), 0, 4);
}

function addToCompare($product_id) {
    if (!isset($_SESSION['compare'])) $_SESSION['compare'] = [];
    $id = (int)$product_id;
    if (!in_array($id, $_SESSION['compare'], true)) {
        if (count($_SESSION['compare']) >= 4) {
            array_shift($_SESSION['compare']);
        }
        $_SESSION['compare'][] = $id;
    }
}

function removeFromCompare($product_id) {
    if (!isset($_SESSION['compare'])) return;
    $_SESSION['compare'] = array_values(array_filter($_SESSION['compare'], fn($id) => (int)$id !== (int)$product_id));
}

function hasPurchasedProduct($conn, $customer_id, $product_id) {
    $cid = (int)$customer_id;
    $pid = (int)$product_id;
    $res = mysqli_query($conn, "SELECT oi.order_item_id FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.customer_id = $cid AND oi.product_id = $pid
        AND o.order_status NOT IN ('Cancelled') LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function hasReviewedProduct($conn, $customer_id, $product_id) {
    $cid = (int)$customer_id;
    $pid = (int)$product_id;
    $res = mysqli_query($conn, "SELECT review_id FROM product_review WHERE customer_id = $cid AND product_id = $pid LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function customerCanReview($conn, $customer_id, $product_id) {
    return hasPurchasedProduct($conn, $customer_id, $product_id) && !hasReviewedProduct($conn, $customer_id, $product_id);
}

function getNotificationCount($conn, $customer_id) {
    $cid = (int)$customer_id;
    $res = @mysqli_query($conn, "SELECT COUNT(*) as cnt FROM customer_notification WHERE customer_id = $cid AND is_read = 0");
    if ($res && $row = mysqli_fetch_assoc($res)) return (int)$row['cnt'];
    return 0;
}

function checkWishlistStockAlerts($conn, $customer_id) {
    $cid = (int)$customer_id;
    $sql = "SELECT w.product_id, p.product_name FROM wishlist w
        JOIN product p ON w.product_id = p.product_id
        WHERE w.customer_id = $cid AND p.product_stock > 0
        AND NOT EXISTS (
            SELECT 1 FROM customer_notification n
            WHERE n.customer_id = $cid AND n.product_id = w.product_id AND n.type = 'wishlist_stock'
        )";
    $res = @mysqli_query($conn, $sql);
    if (!$res) return;
    while ($row = mysqli_fetch_assoc($res)) {
        $pid = (int)$row['product_id'];
        $msg = mysqli_real_escape_string($conn, $row['product_name'] . ' is back in stock!');
        @mysqli_query($conn, "INSERT INTO customer_notification (customer_id, product_id, type, message) VALUES ($cid, $pid, 'wishlist_stock', '$msg')");
    }
}

function normalizeProductMatchKey($str) {
    $str = strtolower((string)$str);
    $str = preg_replace('/[^a-z0-9]+/', '', $str);
    return $str;
}

function findProductIdByMediaName($conn, $filename) {
    if (!$conn) return 0;
    $key = normalizeProductMatchKey(pathinfo($filename, PATHINFO_FILENAME));
    if ($key === '') return 0;
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $res = mysqli_query($conn, "SELECT product_id, product_name, brand FROM product");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                foreach ([$row['product_name'], $row['brand'] ?? ''] as $part) {
                    $k = normalizeProductMatchKey($part);
                    if ($k !== '') $cache[$k] = (int)$row['product_id'];
                }
            }
        }
    }
    if (isset($cache[$key])) return $cache[$key];
    foreach ($cache as $k => $pid) {
        if (strlen($k) >= 4 && (strpos($key, $k) !== false || strpos($k, $key) !== false)) {
            return $pid;
        }
    }
    return 0;
}

function getGifShowcaseItems($conn = null) {
    $items = [];
    $dirs = [getProjectRoot() . '/assets/gifImages', getProjectRoot() . '/assets/gif'];
    $allowed = ['gif', 'webp', 'jpg', 'jpeg', 'png'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        foreach (scandir($dir) ?: [] as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;
            $rel = (strpos($dir, 'gifImages') !== false ? 'assets/gifImages/' : 'assets/gif/') . $file;
            $sort = $ext === 'gif' ? 0 : ($ext === 'webp' ? 1 : 2);
            $product_id = $conn ? findProductIdByMediaName($conn, $file) : 0;
            $items[] = [
                'path' => $rel,
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'ext' => $ext,
                'sort' => $sort,
                'product_id' => $product_id,
                'is_animated' => in_array($ext, ['gif', 'webp'], true),
            ];
        }
    }
    usort($items, function ($a, $b) {
        if ($a['sort'] !== $b['sort']) return $a['sort'] <=> $b['sort'];
        return strcasecmp($a['name'], $b['name']);
    });
    return $items;
}

function renderQtyStepper($name, $value = 1, $min = 1, $max = 99, $attrs = '') {
    $value = (int)$value;
    $min = (int)$min;
    $max = (int)$max;
    return '<div class="qty-stepper">
        <button type="button" class="qty-stepper__btn qty-stepper__minus" aria-label="Decrease quantity"><i class="fa-solid fa-minus"></i></button>
        <input type="number" class="qty-stepper__input shop-quantity-input" name="' . e($name) . '" value="' . $value . '" min="' . $min . '" max="' . $max . '" ' . $attrs . '>
        <button type="button" class="qty-stepper__btn qty-stepper__plus" aria-label="Increase quantity"><i class="fa-solid fa-plus"></i></button>
    </div>';
}
