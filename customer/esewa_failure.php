<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];
$reason      = $_GET['reason'] ?? 'unknown';

// If an order_id is stored in session, cancel/delete it since payment failed
// so stock was never deducted and the order stays unpaid.
// We mark it Cancelled so the employee can see the attempt.
if (isset($_SESSION['esewa_order_id'])) {
    $order_id = (int)$_SESSION['esewa_order_id'];

    // Verify the order belongs to this customer before touching it
    $check = mysqli_query($conn, "SELECT order_id FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='Failed' WHERE order_id=$order_id");
    }

    // Clean up session
    unset($_SESSION['esewa_order_id'], $_SESSION['esewa_amount'], $_SESSION['esewa_cart_items'], $_SESSION['esewa_txn_uuid']);
}

// User-friendly reason messages
$reason_messages = [
    'no_data'           => 'No payment data was received from eSewa.',
    'decode_failed'     => 'Could not read the payment response from eSewa.',
    'invalid_signature' => 'Payment verification failed (invalid signature). Please contact support.',
    'not_complete'      => 'Payment was not completed. You may have cancelled the payment.',
    'invalid_order'     => 'Invalid order reference.',
    'order_not_found'   => 'Order not found or already processed.',
    'api_verify_failed' => 'Payment could not be verified with eSewa. Please contact support.',
    'unknown'           => 'An unexpected error occurred.',
];

$display_reason = $reason_messages[$reason] ?? $reason_messages['unknown'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .fail-card { text-align: center; padding: 40px; max-width: 520px; margin: 50px auto; }
        .fail-icon { font-size: 64px; margin-bottom: 10px; }
        .reason-box { background: #fff4f4; border: 1px solid #f5c6c6; border-radius: 10px; padding: 16px 22px; margin: 20px 0; color: #c62828; }
        .info-box { background: #fffde7; border: 1px solid #ffe082; border-radius: 10px; padding: 14px 20px; margin: 16px 0; font-size: 14px; color: #555; text-align: left; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="card fail-card">
        <div class="fail-icon">❌</div>
        <h2 style="color: #c62828;">Payment Failed</h2>
        <p>Unfortunately, your eSewa payment could not be completed.</p>

        <div class="reason-box">
            <?php echo htmlspecialchars($display_reason); ?>
        </div>

        <div class="info-box">
            <strong>What happened to my order?</strong><br>
            Your order has been cancelled and <strong>no amount has been deducted</strong> from your cart. Your cart items are still saved — just try checking out again.
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px; flex-wrap: wrap;">
            <a href="checkout.php" class="btn btn-success">Try Again</a>
            <a href="cart.php" class="btn btn-primary">Back to Cart</a>
            <a href="shop.php" class="btn">Continue Shopping</a>
        </div>

        <p style="margin-top: 24px; color: #aaa; font-size: 13px;">
            If money was deducted from your eSewa wallet but you see this page, please contact us at
            <strong>support@bytestore.com.np</strong> with your eSewa transaction ID.
        </p>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>