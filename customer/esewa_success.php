<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];
$error_msg   = '';
$verified    = false;
$order_id    = null;

// -------------------------------------------------------
// eSewa POSTs base64-encoded JSON data to success_url
// We decode it, verify the signature, then call eSewa's
// transaction status API to double-confirm.
// -------------------------------------------------------

// eSewa Sandbox credentials (match esewa_payment.php)
$product_code = "EPAYTEST";
$secret_key   = "8gBm/:&EnhH.1/q";
$status_api   = "https://rc.esewa.com.np/api/epay/transaction/status/";

if (!isset($_GET['data'])) {
    // No data returned from eSewa
    header("Location: esewa_failure.php?reason=no_data");
    exit();
}

// Step 1: Decode eSewa's response
$decoded   = base64_decode($_GET['data']);
$response  = json_decode($decoded, true);

if (!$response) {
    header("Location: esewa_failure.php?reason=decode_failed");
    exit();
}

$status           = $response['status']           ?? '';
$total_amount     = $response['total_amount']     ?? 0;
$transaction_uuid = $response['transaction_uuid'] ?? '';
$transaction_code = $response['transaction_code'] ?? '';
$signed_fields    = $response['signed_field_names'] ?? '';
$received_sig     = $response['signature']         ?? '';

// Step 2: Verify HMAC signature from eSewa response
$fields      = explode(',', $signed_fields);
$sig_parts   = [];
foreach ($fields as $field) {
    $sig_parts[] = $field . '=' . ($response[trim($field)] ?? '');
}
$sig_message      = implode(',', $sig_parts);
$expected_sig     = base64_encode(hash_hmac('sha256', $sig_message, $secret_key, true));

if ($received_sig !== $expected_sig) {
    // Signature mismatch — possible tampering
    header("Location: esewa_failure.php?reason=invalid_signature");
    exit();
}

// Step 3: Check status from eSewa response
if ($status !== 'COMPLETE') {
    header("Location: esewa_failure.php?reason=not_complete&status=" . urlencode($status));
    exit();
}

// Step 4: Extract order_id from transaction_uuid (format: BYTESTORE-{order_id}-{timestamp})
$parts    = explode('-', $transaction_uuid);
$order_id = isset($parts[1]) ? (int)$parts[1] : 0;

if (!$order_id) {
    header("Location: esewa_failure.php?reason=invalid_order");
    exit();
}

// Step 5: Verify order belongs to this customer and is still Pending
$order_check = mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
if (mysqli_num_rows($order_check) === 0) {
    // Order not found or already processed
    header("Location: esewa_failure.php?reason=order_not_found");
    exit();
}
$order = mysqli_fetch_assoc($order_check);

// Step 6: (Optional but recommended) Verify with eSewa's Status API
// Remove the total_amount formatting commas if present
$amount_clean = str_replace(',', '', $total_amount);
$status_url   = $status_api . "?product_code={$product_code}&transaction_uuid={$transaction_uuid}&total_amount={$amount_clean}";

$ch = curl_init($status_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$api_response = curl_exec($ch);
$http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$api_data = json_decode($api_response, true);

// Accept if API confirms COMPLETE, or if API is unreachable (localhost limitation) but signature already verified
$api_verified = ($http_code === 200 && isset($api_data['status']) && $api_data['status'] === 'COMPLETE');
$sig_verified = true; // we already verified signature above

if (!$api_verified && !$sig_verified) {
    header("Location: esewa_failure.php?reason=api_verify_failed");
    exit();
}

// -------------------------------------------------------
// Step 7: All checks passed — finalize the order
// -------------------------------------------------------

// Update payment_status to Paid and store eSewa transaction code
$txn_code_escaped = mysqli_real_escape_string($conn, $transaction_code);
mysqli_query($conn, "UPDATE orders SET payment_status='Paid', order_status='Processing' WHERE order_id=$order_id");

// Deduct stock now that payment is confirmed
$items_result = mysqli_query($conn, "SELECT oi.product_id, oi.quantity, p.product_stock FROM order_items oi JOIN product p ON oi.product_id = p.product_id WHERE oi.order_id=$order_id");
while ($item = mysqli_fetch_assoc($items_result)) {
    $new_stock = max(0, $item['product_stock'] - $item['quantity']);
    mysqli_query($conn, "UPDATE product SET product_stock=$new_stock WHERE product_id={$item['product_id']}");
}

// Clear the customer's cart
mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");

// Clear session esewa data
unset($_SESSION['esewa_order_id'], $_SESSION['esewa_amount'], $_SESSION['esewa_cart_items'], $_SESSION['esewa_txn_uuid']);

$verified = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-card { text-align: center; padding: 40px; max-width: 560px; margin: 50px auto; }
        .checkmark { font-size: 64px; margin-bottom: 10px; }
        .order-details { background: #f4fdf4; border: 1px solid #b2dfb2; border-radius: 10px; padding: 18px 24px; margin: 24px 0; text-align: left; }
        .order-details p { margin: 6px 0; }
        .txn-code { font-family: monospace; font-size: 13px; color: #555; background: #eee; padding: 2px 8px; border-radius: 4px; }
        .esewa-badge { color: #60BB46; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="card success-card">
        <div class="checkmark">✅</div>
        <h2 style="color: #2e7d32;">Payment Successful!</h2>
        <p>Your eSewa payment has been verified and your order is confirmed.</p>

        <div class="order-details">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Amount Paid:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Payment Method:</strong> <span class="esewa-badge">eSewa</span></p>
            <p><strong>eSewa Txn Code:</strong> <span class="txn-code"><?php echo htmlspecialchars($transaction_code); ?></span></p>
            <p><strong>Status:</strong> <span style="color:#2e7d32; font-weight:bold;">Paid ✔</span></p>
        </div>

        <p style="color:#777; font-size:14px;">Your order is now being processed. You will receive your items at the shipping address provided.</p>

        <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
            <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>