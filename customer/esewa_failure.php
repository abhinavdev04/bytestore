<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];
$reason      = $_GET['reason'] ?? 'unknown';

// DEBUG - log everything
$debug_log  = "=== eSewa Failure Debug ===\n";
$debug_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
$debug_log .= "Full URL: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
$debug_log .= "GET: " . print_r($_GET, true) . "\n";
$debug_log .= "POST: " . print_r($_POST, true) . "\n";
$log_path = dirname(__DIR__) . '/esewa_debug.log';
file_put_contents($log_path, $debug_log, FILE_APPEND);

// Cancel pending order
if (isset($_SESSION['esewa_order_id'])) {
    $order_id = (int)$_SESSION['esewa_order_id'];
    $check = mysqli_query($conn, "SELECT order_id FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Failed' WHERE order_id=$order_id");
    }
    unset($_SESSION['esewa_order_id'], $_SESSION['esewa_amount'], $_SESSION['esewa_cart_items'], $_SESSION['esewa_txn_uuid'], $_SESSION['esewa_total_amount']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .fail-card { text-align: center; padding: 40px; max-width: 560px; margin: 50px auto; }
        .debug-box { background: #f5f5f5; border: 1px solid #ccc; border-radius: 8px; padding: 14px; margin: 20px 0; text-align: left; font-family: monospace; font-size: 12px; white-space: pre-wrap; word-break: break-all; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="card fail-card">
        <h2 style="color:#c62828;">❌ Payment Failed</h2>

        <div class="debug-box"><?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
echo "FULL URL:\n$url\n\n";
echo "GET params:\n" . json_encode($_GET, JSON_PRETTY_PRINT) . "\n\n";
echo "POST params:\n" . json_encode($_POST, JSON_PRETTY_PRINT) . "\n\n";
if (isset($_GET['data'])) {
    $decoded = base64_decode($_GET['data']);
    echo "Decoded data:\n" . ($decoded ?: '(decode failed)') . "\n";
} else {
    echo "No ?data= in URL\n";
}
        ?></div>

        <p>Share the debug box above so we can fix this.</p>
        <a href="checkout.php" class="btn btn-success">Try Again</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>