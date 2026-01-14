<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkEmployeeLogin();

$order_id = (int)$_GET['id'];

// Handle status update form submission
if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $update_sql = "UPDATE orders SET order_status='$new_status' WHERE order_id=$order_id";
    if (mysqli_query($conn, $update_sql)) {
        $status_success = "Order status updated successfully!";
    } else {
        $status_error = "Failed to update order status.";
    }
}

// Fetch order details
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.*, c.customer_name, c.customer_email, c.customer_address FROM orders o JOIN customer c ON o.customer_id = c.customer_id WHERE o.order_id = $order_id"));

if (!$order) {
    header("Location: view_orders.php");
    exit();
}

// Fetch order items
$items_result = mysqli_query($conn, "SELECT oi.*, p.product_name, p.product_image_path FROM order_items oi JOIN product p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");

// Define allowed order statuses
$order_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>Order Details - #<?php echo $order_id; ?></h2>

        <div style="margin-bottom: 20px;">
            <a href="view_orders.php" class="btn btn-primary">Back to Orders</a>
        </div>

        <?php if (!empty($status_success)): ?>
            <div class="alert alert-success"><?php echo $status_success; ?></div>
        <?php endif; ?>
        <?php if (!empty($status_error)): ?>
            <div class="alert alert-error"><?php echo $status_error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                <p><strong>Address:</strong> <?php echo $order['customer_address']; ?></p>
            </div>
            <div>
                <h3>Order Information</h3>
                <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($order['order_date'])); ?></p>
                <p><strong>Status:</strong> <?php echo $order['order_status']; ?></p>
                <p><strong>Payment Status:</strong> <?php echo $order['payment_status']; ?></p>
                <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>

                <!-- Order status update form -->
                <form method="POST" style="margin-top: 15px;">
                    <label for="order_status"><strong>Update Order Status:</strong></label>
                    <select name="order_status" id="order_status" required>
                        <?php foreach ($order_statuses as $status): ?>
                            <option value="<?php echo $status; ?>" <?php echo ($order['order_status'] == $status) ? 'selected' : ''; ?>>
                                <?php echo $status; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                </form>
            </div>
        </div>
        
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($items_result)): 
                    $item_total = $item['price'] * $item['quantity'];
                ?>
                    <tr>
                        <td>
                            <img src="../<?php echo $item['product_image_path']; ?>" 
                                 alt="<?php echo $item['product_name']; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; vertical-align: middle;"
                                 onerror="this.src='../assets/images/placeholder.jpg'">
                            <?php echo $item['product_name']; ?>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item_total, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                    <td style="font-weight: bold; font-size: 1.2rem;">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
