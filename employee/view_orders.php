<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkEmployeeLogin();

$result = mysqli_query($conn, "SELECT o.*, c.customer_name, c.customer_email FROM orders o JOIN customer c ON o.customer_id = c.customer_id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>All Orders</h2>
        
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['customer_name']; ?><br><small><?php echo $order['customer_email']; ?></small></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                        <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo $order['order_status']; ?></td>
                        <td><?php echo $order['payment_status']; ?></td>
                        <td>
                            <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-primary">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

