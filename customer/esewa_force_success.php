<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

// order_id may be passed for testing, otherwise use session-stored esewa_order_id
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (int)($_SESSION['esewa_order_id'] ?? 0);

if (!$order_id) {
    echo "Missing order_id. Provide ?order_id= or set session via checkout.";
    exit();
}

$order_check = mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
if (!$order_check || mysqli_num_rows($order_check) === 0) {
    echo "Order not found or not pending.";
    exit();
}
$order = mysqli_fetch_assoc($order_check);

// Mark order as Paid
mysqli_query($conn, "UPDATE orders SET payment_status='Paid', order_status='Processing' WHERE order_id=$order_id");

// Deduct stock
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

// Clear cart and session eSewa data
mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");
unset(
    $_SESSION['esewa_order_id'],
    $_SESSION['esewa_amount'],
    $_SESSION['esewa_cart_items'],
    $_SESSION['esewa_txn_uuid'],
    $_SESSION['esewa_total_amount']
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>eSewa Force Success - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="card" style="max-width:600px;margin:40px auto;text-align:center;">
        <h2>Payment Marked Successful (Test)</h2>
        <p>Order #<?php echo htmlspecialchars($order_id); ?> has been marked as <strong>Paid</strong> for customer ID <?php echo htmlspecialchars($customer_id); ?>.</p>
        <p><a class="btn btn-primary" href="shop.php">Continue Shopping</a></p>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
