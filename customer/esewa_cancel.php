<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

// Prefer explicit order_id param but fall back to session
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (int)($_SESSION['esewa_order_id'] ?? 0);
$txn_uuid = $_SESSION['esewa_txn_uuid'] ?? '';
$total_amount = $_SESSION['esewa_total_amount'] ?? ($_SESSION['esewa_amount'] ?? 0);

if (!$order_id) {
    header('Location: checkout.php');
    exit();
}

$log_path = dirname(__DIR__) . '/esewa_debug.log';
$debug = "=== eSewa Cancel Request ===\n";
$debug .= "Time: " . date('Y-m-d H:i:s') . "\n";
$debug .= "Order: $order_id, Customer: $customer_id, Txn: $txn_uuid, Amount: $total_amount\n";
file_put_contents($log_path, $debug, FILE_APPEND);

// Helper to clear session eSewa data
function clear_esewa_session() {
    unset(
        $_SESSION['esewa_order_id'],
        $_SESSION['esewa_amount'],
        $_SESSION['esewa_cart_items'],
        $_SESSION['esewa_txn_uuid'],
        $_SESSION['esewa_total_amount']
    );
}

// Check order exists and belongs to customer
$order_check = mysqli_query($conn, "SELECT order_id, payment_status FROM orders WHERE order_id={$order_id} AND customer_id={$customer_id}");
if (!$order_check || mysqli_num_rows($order_check) === 0) {
    clear_esewa_session();
    header('Location: shop.php');
    exit();
}
$order = mysqli_fetch_assoc($order_check);

// If there's no txn UUID yet (user cancelled before redirect or txn not created)
if (empty($txn_uuid)) {
    // Cancel pending order and return to checkout/shop
    if ($order['payment_status'] === 'Pending') {
        mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Failed' WHERE order_id={$order_id}");
    }
    mysqli_query($conn, "DELETE FROM cart WHERE customer_id={$customer_id}");
    clear_esewa_session();
    $message = 'Your order has been cancelled. No payment was completed.';
    $show_message = true;
} else {
    // Verify status with eSewa
    $product_code = $ESEWA_PRODUCT_CODE ?? 'EPAYTEST';
    $amount_clean = str_replace(',', '', $total_amount);
    $status_url = ($ESEWA_STATUS_URL ?? 'https://rc.esewa.com.np/api/epay/transaction/status/')
                . "?product_code=" . urlencode($product_code)
                . "&transaction_uuid=" . urlencode($txn_uuid)
                . "&total_amount=" . urlencode($amount_clean);

    $ch = curl_init($status_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $api_response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($log_path, "Status check URL: $status_url\nHTTP Code: $http_code\nResponse: " . ($api_response ?: '(empty)') . "\nCurl Error: $curl_error\n", FILE_APPEND);

    $api_data = json_decode($api_response, true);
    $api_status = $api_data['status'] ?? '';

    if ($http_code === 200 && $api_status === 'COMPLETE') {
        // Payment already completed. Attempt refund if configured.
        if (!empty($ESEWA_REFUND_URL)) {
            $refund_url = $ESEWA_REFUND_URL;
            $secret_key = $ESEWA_SECRET_KEY ?? '';

            // Build refund payload (best-effort; adjust if eSewa expects different format)
            $refund_message = "transaction_uuid={$txn_uuid},total_amount={$amount_clean},product_code={$product_code}";
            $refund_sig = base64_encode(hash_hmac('sha256', $refund_message, $secret_key, true));

            $post_fields = [
                'product_code' => $product_code,
                'transaction_uuid' => $txn_uuid,
                'total_amount' => $amount_clean,
                'signature' => $refund_sig,
            ];

            $ch2 = curl_init($refund_url);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_fields));
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch2, CURLOPT_TIMEOUT, 20);
            $refund_response = curl_exec($ch2);
            $refund_http = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            $refund_err = curl_error($ch2);
            curl_close($ch2);

            file_put_contents($log_path, "Refund attempt URL: $refund_url\nHTTP Code: $refund_http\nResponse: " . ($refund_response ?: '(empty)') . "\nCurl Error: $refund_err\n", FILE_APPEND);

            $refund_ok = ($refund_http === 200 && (stripos($refund_response, 'success') !== false || stripos($refund_response, 'refund') !== false));

            if ($refund_ok) {
                mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Refunded' WHERE order_id={$order_id}");
                mysqli_query($conn, "DELETE FROM cart WHERE customer_id={$customer_id}");
                clear_esewa_session();
                $message = 'Payment refunded and order cancelled. Refund processed by eSewa.';
                $show_message = true;
            } else {
                // Refund failed — advise user/developer
                $message = 'Payment was completed. Automatic refund attempt failed. Please contact support with the debug log.';
                $show_message = true;
            }
        } else {
            // No refund URL configured
            $message = 'Payment was completed. Refund endpoint is not configured on this site. Contact support for a refund.';
            $show_message = true;
        }
    } else {
        // Payment not complete — cancel local order
        if ($order['payment_status'] === 'Pending') {
            mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Failed' WHERE order_id={$order_id}");
        }
        mysqli_query($conn, "DELETE FROM cart WHERE customer_id={$customer_id}");
        clear_esewa_session();
        $message = 'Order cancelled. No successful payment found.';
        $show_message = true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>eSewa Cancel / Refund - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="card" style="max-width:720px;margin:40px auto;text-align:center;">
        <?php if (!empty($show_message)): ?>
            <h2>eSewa Payment Cancelled</h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <p style="font-size:12px;color:#666;margin-top:12px;">Debug log: <em>esewa_debug.log</em> (server side)</p>
            <p><a class="btn btn-primary" href="shop.php">Continue Shopping</a></p>
        <?php else: ?>
            <h2>Processing...</h2>
            <p>Please wait while we check payment status and process cancellation.</p>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
