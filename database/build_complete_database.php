<?php
/**
 * Builds database/bytestore_complete.sql with full schema + realistic demo data.
 * Run: php database/build_complete_database.php
 */
$root = dirname(__DIR__);
$outFile = __DIR__ . '/bytestore_complete.sql';
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'bytestore_build_' . time();

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die("MySQL connect failed: " . $mysqli->connect_error . "\n");
}

$mysqli->query("DROP DATABASE IF EXISTS `$dbName`");
$mysqli->query("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$mysqli->select_db($dbName);
$mysqli->set_charset('utf8mb4');

function runSql(mysqli $db, string $sql): void {
    if (!$db->multi_query($sql)) {
        throw new RuntimeException($db->error);
    }
    while ($db->more_results() && $db->next_result()) {
        /* flush */
    }
}

$schema = file_get_contents(__DIR__ . '/schema_base.sql');
if (!$schema) {
    die("Missing schema_base.sql — run from project with schema file present.\n");
}
runSql($mysqli, $schema);

$passwordHash = password_hash('Password@123', PASSWORD_DEFAULT);
$passwordHashEsc = $mysqli->real_escape_string($passwordHash);

// Employees
$mysqli->query("INSERT INTO employee (employee_name, employee_password, employee_email, employee_phone, shop_name) VALUES
('Super Admin', '$passwordHashEsc', 'Superadmin1@bytestore.com', '9801000001', 'ByteStore HQ'),
('Staff Manager', '$passwordHashEsc', 'Employee1@bytestore.com', '9801000002', 'ByteStore HQ')");
$mysqli->query("UPDATE employee SET employee_email='Superadmin1@bytestore.com' WHERE employee_id=1 LIMIT 1");

// Categories (24)
$categories = ['Laptops','Desktops','Gaming','Monitors','Processors','Graphics Cards','Motherboards','Memory (RAM)','Storage (SSD/HDD)','Power Supplies','PC Cases','Cooling','Keyboards & Mice','Audio (Headphones/Speakers)','Webcams','Accessories','Phones','Tablets','Wearables (Watches)','Networking (Routers/WiFi)','Printers & Scanners','Servers','Software & Services','Cameras'];
$catIds = [];
foreach ($categories as $i => $name) {
    $n = $mysqli->real_escape_string($name);
    $mysqli->query("INSERT INTO category (category_name) VALUES ('$n')");
    $catIds[] = $mysqli->insert_id;
}

$brands = ['Apple','Samsung','Dell','HP','Lenovo','Asus','Acer','MSI','Sony','Logitech','Razer','Canon','TP-Link','Google','OnePlus','Xiaomi','LG','JBL','Corsair','Kingston','Western Digital','NVIDIA','AMD','Intel'];
$productNames = [
    'Pro Laptop','UltraBook','Gaming Rig','Office Desktop','Wireless Earbuds','Smart Watch','4K Monitor','Mechanical Keyboard',
    'Gaming Mouse','USB-C Hub','Portable SSD','WiFi Router','Bluetooth Speaker','Action Camera','Tablet Pro','Budget Phone',
    'Flagship Phone','Graphics Card','Processor Kit','RAM Module','NVMe SSD','Power Supply','CPU Cooler','Webcam HD',
];

// 120 products
for ($i = 1; $i <= 120; $i++) {
    $name = $productNames[($i - 1) % count($productNames)] . ' ' . $i;
    $brand = $brands[$i % count($brands)];
    $cat = $catIds[$i % count($catIds)];
    $price = round(5000 + ($i * 3175) % 350000, 2);
    $orig = round($price * (1 + (($i % 5) + 10) / 100), 2);
    $stock = ($i % 17) + 3;
    $disc = ($i % 4 === 0) ? 10 + ($i % 15) : 'NULL';
    $saleActive = ($disc !== 'NULL') ? 1 : 0;
    $saleEnd = ($saleActive) ? "DATE_ADD(NOW(), INTERVAL " . (7 + ($i % 20)) . " DAY)" : 'NULL';
    $img = 'assets/uploads/products/laptop1.jpg';
    $featured = ($i % 8 === 0) ? 1 : 0;
    $trending = ($i % 6 === 0) ? 1 : 0;
    $rating = round(3.5 + ($i % 15) / 10, 2);
    $rCount = 2 + ($i % 12);
    $ne = $mysqli->real_escape_string($name);
    $be = $mysqli->real_escape_string($brand);
    $de = $mysqli->real_escape_string("Premium $brand $name with official warranty and fast delivery across Nepal.");
    $mysqli->query("INSERT INTO product (product_name, product_description, product_price, original_price, discount_percent, is_sale_active, sale_start_date, sale_end_date, product_image_path, product_stock, category_id, brand, is_featured, is_trending, rating_avg, rating_count)
        VALUES ('$ne', '$de', $price, $orig, $disc, $saleActive, NOW(), $saleEnd, '$img', $stock, $cat, '$be', $featured, $trending, $rating, $rCount)");
}

// Customers 120 (+ legacy demo account)
$mysqli->query("INSERT INTO customer (customer_name, customer_password, customer_email, customer_phone, customer_address) VALUES
('Akraj Customer', '$passwordHashEsc', 'Akraj2024@bytestore.com', '9803563451', 'Bhaktapur,Nepal')");

for ($i = 1; $i <= 120; $i++) {
    $email = "customer{$i}@bytestore.com";
    $name = "Customer User $i";
    $phone = '980' . str_pad((string)(3500000 + $i), 7, '0', STR_PAD_LEFT);
    $addr = "Address Line $i, Kathmandu, Nepal";
    $ne = $mysqli->real_escape_string($name);
    $mysqli->query("INSERT INTO customer (customer_name, customer_password, customer_email, customer_phone, customer_address, created_at)
        VALUES ('$ne', '$passwordHashEsc', '$email', '$phone', '$addr', DATE_SUB(NOW(), INTERVAL " . ($i % 365) . " DAY))");
}

// Reviews 350+ (unique customer+product pairs)
$reviewTexts = ['Excellent product, fast delivery.', 'Great value for money.', 'Works perfectly, highly recommend.', 'Good build quality.', 'Average experience but acceptable.', 'Outstanding performance!', 'Exactly as described.', 'Would buy again.'];
$reviewPairs = [];
for ($r = 0; $r < 350; $r++) {
    $pid = 1 + (($r * 11 + 3) % 120);
    $cid = 1 + (($r * 13 + 5) % 121);
    $key = "$cid-$pid";
    if (isset($reviewPairs[$key])) continue;
    $reviewPairs[$key] = true;
    $rating = 3 + ($r % 3);
    $text = $mysqli->real_escape_string($reviewTexts[$r % count($reviewTexts)] . ' Review #' . ($r + 1));
    $title = $mysqli->real_escape_string('Review ' . ($r + 1));
    $mysqli->query("INSERT INTO product_review (product_id, customer_id, rating, review_title, review_text, is_verified, created_at)
        VALUES ($pid, $cid, $rating, '$title', '$text', 1, DATE_SUB(NOW(), INTERVAL " . ($r % 180) . " DAY))");
}

// Orders 550 across 12 months
for ($o = 1; $o <= 550; $o++) {
    $cid = 1 + ($o % 120);
    $monthsAgo = $o % 12;
    $total = round(5000 + ($o * 891) % 280000, 2);
    $statuses = ['Delivered','Delivered','Delivered','Shipped','Processing','Pending','Cancelled'];
    $status = $statuses[$o % count($statuses)];
    $pay = ($status === 'Cancelled') ? 'Pending' : 'Paid';
    $mysqli->query("INSERT INTO orders (customer_id, total_amount, shipping_address, customer_phone, order_status, payment_status, order_date)
        VALUES ($cid, $total, 'Kathmandu,Nepal', '9800000000', '$status', '$pay', DATE_SUB(NOW(), INTERVAL $monthsAgo MONTH) - INTERVAL " . ($o % 28) . " DAY)");
    $oid = $mysqli->insert_id;
    $items = 1 + ($o % 3);
    for ($it = 0; $it < $items; $it++) {
        $pid = 1 + (($o + $it) % 120);
        $qty = 1 + ($it % 2);
        $pr = mysqli_fetch_assoc($mysqli->query("SELECT product_price FROM product WHERE product_id=$pid"));
        $price = $pr['product_price'] ?? 10000;
        $mysqli->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($oid, $pid, $qty, $price)");
    }
}

// Wishlist 320 unique pairs
$wlPairs = [];
for ($w = 0; count($wlPairs) < 320 && $w < 2000; $w++) {
    $cid = 1 + (($w * 9 + 1) % 121);
    $pid = 1 + (($w * 17 + 2) % 120);
    $key = "$cid-$pid";
    if (isset($wlPairs[$key])) continue;
    $wlPairs[$key] = true;
    $mysqli->query("INSERT INTO wishlist (customer_id, product_id) VALUES ($cid, $pid)");
}

// Notifications 120
for ($n = 0; $n < 120; $n++) {
    $cid = 1 + ($n % 120);
    $pid = 1 + ($n % 120);
    $msg = $mysqli->real_escape_string('Product #' . $pid . ' is back in stock!');
    $mysqli->query("INSERT INTO customer_notification (customer_id, product_id, type, message, is_read, created_at)
        VALUES ($cid, $pid, 'wishlist_stock', '$msg', " . ($n % 3) . ", DATE_SUB(NOW(), INTERVAL " . ($n % 30) . " DAY))");
}

// Support tickets 40 demo
$ticketSubjects = ['Order not received', 'Wrong item delivered', 'Refund request', 'Product defect', 'Payment failed', 'Warranty claim'];
for ($t = 0; $t < 40; $t++) {
    $cid = 1 + ($t % 50);
    $sub = $mysqli->real_escape_string($ticketSubjects[$t % count($ticketSubjects)] . ' #' . ($t + 1));
    $msg = $mysqli->real_escape_string('I need help with my recent order. Please assist.');
    $statuses = ['Open','Pending','Resolved','Closed'];
    $st = $statuses[$t % 4];
    $cat = ['Order','Product','Payment','Technical'][$t % 4];
    $mysqli->query("INSERT INTO support_ticket (customer_id, category, subject, message, status, created_at, updated_at)
        VALUES ($cid, '$cat', '$sub', '$msg', '$st', DATE_SUB(NOW(), INTERVAL " . ($t % 20) . " DAY), NOW())");
    $tid = $mysqli->insert_id;
    if ($t % 2 === 0) {
        $reply = $mysqli->real_escape_string('Thank you for contacting ByteStore. We are looking into this.');
        $mysqli->query("INSERT INTO support_reply (ticket_id, sender_type, sender_id, message) VALUES ($tid, 'employee', 1, '$reply')");
    }
}

// Variants for first 15 products
$variantTypes = ['Color' => ['Black','Silver','Blue'], 'Storage' => ['128GB','256GB','512GB'], 'RAM' => ['8GB','16GB','32GB']];
for ($pid = 1; $pid <= 15; $pid++) {
    $base = mysqli_fetch_assoc($mysqli->query("SELECT product_price FROM product WHERE product_id=$pid"));
    $bp = (float)($base['product_price'] ?? 50000);
    foreach ($variantTypes as $type => $opts) {
        foreach ($opts as $j => $opt) {
            $vn = $mysqli->real_escape_string("$opt");
            $te = $mysqli->real_escape_string($type);
            $vp = $bp + ($j * 5000);
            $mysqli->query("INSERT INTO product_variant (product_id, variant_name, variant_type, variant_price, variant_stock, variant_sku, variant_image_path)
                VALUES ($pid, '$vn', '$te', $vp, " . (5 + $j) . ", 'SKU-$pid-$j', NULL)");
        }
    }
}

// Recently viewed
for ($rv = 0; $rv < 200; $rv++) {
    $cid = 1 + ($rv % 120);
    $pid = 1 + ($rv % 120);
    $mysqli->query("INSERT INTO recently_viewed (customer_id, product_id, viewed_at) VALUES ($cid, $pid, DATE_SUB(NOW(), INTERVAL " . ($rv % 60) . " DAY))
        ON DUPLICATE KEY UPDATE viewed_at = VALUES(viewed_at)");
}

// Newsletter
for ($nl = 0; $nl < 50; $nl++) {
    $em = "subscriber{$nl}@example.com";
    $mysqli->query("INSERT IGNORE INTO newsletter (email) VALUES ('$em')");
}

echo "Seeded database `$dbName`\n";

// Dump
$dumpCmd = sprintf(
    '"%s" -u%s %s --routines --triggers --single-transaction %s 2>&1',
    'c:\\xampp\\mysql\\bin\\mysqldump.exe',
    $user,
    $pass !== '' ? '-p' . escapeshellarg($pass) : '',
    escapeshellarg($dbName)
);

$header = <<<HDR
-- =============================================================================
-- ByteStore Complete Database
-- Single-file installation — import only this file
-- Command: mysql -u root < database/bytestore_complete.sql
-- Generated: 
HDR;
$header .= date('Y-m-d H:i:s') . "\n";
$header .= "-- Demo login: Superadmin1@bytestore.com / Password@123\n";
$header .= "-- Demo customer: customer1@bytestore.com / Password@123\n";
$header .= "-- =============================================================================\n\n";
$header .= "DROP DATABASE IF EXISTS `bytestore`;\n";
$header .= "CREATE DATABASE `bytestore` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
$header .= "USE `bytestore`;\n\n";

$output = shell_exec($dumpCmd);
if (!$output) {
    die("mysqldump failed\n");
}
$output = preg_replace('/^CREATE DATABASE.*$/m', '', $output);
$output = preg_replace('/^USE `.*?`;$/m', '', $output);
file_put_contents($outFile, $header . $output);

$mysqli->query("DROP DATABASE `$dbName`");
echo "Written: $outFile (" . number_format(filesize($outFile)) . " bytes)\n";
