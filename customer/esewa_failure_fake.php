
<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

if (isset($_SESSION['esewa_order_id'])) {
    $order_id = (int)$_SESSION['esewa_order_id'];

    $order_check = mysqli_query($conn, "SELECT order_id FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'");
    if ($order_check && mysqli_num_rows($order_check) > 0) {
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
    }
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-card { text-align: center; padding: 40px; max-width: 560px; margin: 50px auto; }
        .checkmark { font-size: 64px; margin-bottom: 10px; }
        .order-details {
            background: #f4fdf4;
            border: 1px solid #b2dfb2;
            border-radius: 10px;
            padding: 18px 24px;
            margin: 24px 0;
            text-align: left;
        }
        .order-details p { margin: 8px 0; }
        .txn-code {
            font-family: monospace;
            font-size: 13px;
            color: #555;
            background: #eee;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .esewa-green { color: #60BB46; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="card success-card">
        <div class="checkmark">✅</div>
        <h2 style="color: #2e7d32;">Payment Successful!</h2>
        <p>Your eSewa payment has been verified and your order is confirmed.</p>

       

        <p style="color:#777; font-size:14px;">
            Your order is now being processed. You will receive your items at the shipping address provided.
        </p>

        <div style="display:flex; gap:12px; justify-content:center; margin-top:20px;">
            <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>