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

// If eSewa redirected to failure without providing ?data=, attempt server-side verification
$show_success = false;
if (isset($_SESSION['esewa_order_id']) && !empty($_SESSION['esewa_txn_uuid'])) {
    $order_id = (int)$_SESSION['esewa_order_id'];
    $transaction_uuid = $_SESSION['esewa_txn_uuid'];
    $total_amount = $_SESSION['esewa_total_amount'] ?? $_SESSION['esewa_amount'] ?? '';

    // Try eSewa status API
    $product_code = $ESEWA_PRODUCT_CODE ?? 'EPAYTEST';
    $amount_clean = str_replace(',', '', $total_amount);
    $status_url   = ($ESEWA_STATUS_URL ?? 'https://rc.esewa.com.np/api/epay/transaction/status/')
                  . "?product_code=" . urlencode($product_code)
                  . "&transaction_uuid=" . urlencode($transaction_uuid)
                  . "&total_amount=" . urlencode($amount_clean);

    $ch = curl_init($status_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $api_response = curl_exec($ch);
    $curl_error   = curl_error($ch);
    $http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($log_path, "=== eSewa Failure Server Verify ===\nTime: " . date('Y-m-d H:i:s') . "\nStatus URL: $status_url\nHTTP Code: $http_code\nResponse: " . ($api_response ?: '(empty)') . "\nCurl Error: $curl_error\n", FILE_APPEND);

    $api_data   = json_decode($api_response, true);
    $api_status = $api_data['status'] ?? '';

    if ($http_code === 200 && $api_status === 'COMPLETE') {
        // Finalize order as Paid
        $check = mysqli_query($conn, "SELECT order_id FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
        if ($check && mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE orders SET payment_status='Paid', order_status='Processing' WHERE order_id=$order_id");

            $items_result = mysqli_query($conn,
                "SELECT oi.product_id, oi.quantity, p.product_stock
                 FROM order_items oi
                 JOIN product p ON oi.product_id = p.product_id
                 WHERE oi.order_id=$order_id"
            );

            while ($item = mysqli_fetch_assoc($items_result)) {
                $new_stock = max(0, $item['product_stock'] - $item['quantity']);
                mysqli_query($conn, "UPDATE product SET product_stock=$new_stock WHERE product_id={$item['product_id']}");
            }

            mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");

            unset($_SESSION['esewa_order_id'], $_SESSION['esewa_amount'], $_SESSION['esewa_cart_items'], $_SESSION['esewa_txn_uuid'], $_SESSION['esewa_total_amount']);

            $show_success = true;
            $final_order_id = $order_id;
        }
    } else {
        // Not confirmed — cancel the pending order
        $check = mysqli_query($conn, "SELECT order_id FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
        if ($check && mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Failed' WHERE order_id=$order_id");
        }
        unset($_SESSION['esewa_order_id'], $_SESSION['esewa_amount'], $_SESSION['esewa_cart_items'], $_SESSION['esewa_txn_uuid'], $_SESSION['esewa_total_amount']);
    }
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
    <?php if (!empty($show_success)): ?>
    <div class="card fail-card">
        <h2 style="color:#2e7d32;">✅ Payment Verified</h2>
        <div class="debug-box">
            <?php echo "Order #$final_order_id has been marked Paid via server status API.\n"; ?>
        </div>
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    </div>
    <?php else: ?>
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
    <?php endif; ?>
    <?php include '../includes/footer.php'; ?>
</body>
</html>