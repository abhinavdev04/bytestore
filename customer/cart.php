<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

// Initialize messages
$cancel_message = '';
$cancel_error = '';

if (isset($_GET['remove'])) {
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id=" . (int)$_GET['remove'] . " AND customer_id=$customer_id");
    header("Location: cart.php");
    exit();
}

if (isset($_POST['update_quantity'])) {
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity=$quantity WHERE cart_id=" . (int)$_POST['cart_id'] . " AND customer_id=$customer_id");
    }
    header("Location: cart.php");
    exit();
}

// Handle order cancellation
if (isset($_POST['cancel_order'])) {
    $order_id = (int)$_POST['order_id'];
    // Verify the order belongs to this customer and can be cancelled
    $check_sql = "SELECT order_status FROM orders WHERE order_id=$order_id AND customer_id=$customer_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $order = mysqli_fetch_assoc($check_result);
        // Allow cancellation only for Pending and Processing orders
        if (in_array($order['order_status'], ['Pending', 'Processing'])) {
            mysqli_query($conn, "UPDATE orders SET order_status='Cancelled' WHERE order_id=$order_id AND customer_id=$customer_id");
            $_SESSION['cancel_message'] = "Order #$order_id has been cancelled successfully.";
        } else {
            $_SESSION['cancel_error'] = "Order #$order_id cannot be cancelled (Status: {$order['order_status']}).";
        }
    } else {
        $_SESSION['cancel_error'] = "Order not found or you don't have permission to cancel it.";
    }
    header("Location: cart.php");
    exit();
}

// Get messages from session
if (isset($_SESSION['cancel_message'])) {
    $cancel_message = $_SESSION['cancel_message'];
    unset($_SESSION['cancel_message']);
}
if (isset($_SESSION['cancel_error'])) {
    $cancel_error = $_SESSION['cancel_error'];
    unset($_SESSION['cancel_error']);
}

// Get cart items
$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.product_price, p.product_image_path, p.product_stock
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.customer_id = $customer_id";
$result = mysqli_query($conn, $sql);

// Get pending/processing orders
$active_orders_sql = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status, o.payment_status, o.shipping_address
               FROM orders o
               WHERE o.customer_id = $customer_id 
               AND o.order_status IN ('Pending', 'Processing')
               ORDER BY o.order_date DESC";
$active_orders_result = mysqli_query($conn, $active_orders_sql);

// Get shipped orders
$shipped_orders_sql = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status, o.payment_status, o.shipping_address
               FROM orders o
               WHERE o.customer_id = $customer_id 
               AND o.order_status = 'Shipped'
               ORDER BY o.order_date DESC";
$shipped_orders_result = mysqli_query($conn, $shipped_orders_sql);

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-shipped { background-color: #28a745; color: #fff; }
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .order-info-item {
            display: flex;
            flex-direction: column;
        }
        .order-info-item strong {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #007bff;
        }
        .section-title.shipped {
            border-bottom: 3px solid #28a745;
        }
        .no-orders {
            text-align: center;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
            color: #666;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <!-- Success/Error Messages -->
    <?php if (!empty($cancel_message)): ?>
        <div class="card">
            <div class="alert alert-success"><?php echo $cancel_message; ?></div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($cancel_error)): ?>
        <div class="card">
            <div class="alert alert-error"><?php echo $cancel_error; ?></div>
        </div>
    <?php endif; ?>
    
    <!-- Shopping Cart Section -->
    <div class="card">
        <h2>Shopping Cart</h2>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($result)): 
                        $item_total = $item['product_price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                        <tr>
                            <td>
                                <img src="../<?php echo $item['product_image_path']; ?>" alt="<?php echo $item['product_name']; ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; vertical-align: middle;" 
                                     onerror="this.src='../assets/images/placeholder.jpg'">
                                <?php echo $item['product_name']; ?>
                            </td>
                            <td>Rs. <?php echo number_format($item['product_price'], 2); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['product_stock']; ?>" 
                                           style="width: 60px; padding: 5px;">
                                    <button type="submit" name="update_quantity" class="btn btn-warning" style="padding: 5px 10px;">Update</button>
                                </form>
                            </td>
                            <td>Rs. <?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="btn btn-danger" 
                                   onclick="return confirm('Remove this item from cart?')">Remove</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                        <td style="font-weight: bold; font-size: 1.2rem;">Rs. <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div style="margin-top: 20px; text-align: right;">
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="shop.php">Start shopping!</a></p>
        <?php endif; ?>
    </div>

    <!-- Active Orders Section (Pending/Processing) -->
    <div class="card">
        <h2 class="section-title">My Active Orders</h2>
        
        <?php if (mysqli_num_rows($active_orders_result) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($active_orders_result)): 
                // Get order items
                $items_sql = "SELECT oi.*, p.product_name, p.product_image_path 
                             FROM order_items oi 
                             JOIN product p ON oi.product_id = p.product_id 
                             WHERE oi.order_id = {$order['order_id']}";
                $items_result = mysqli_query($conn, $items_sql);
                
                // Determine status badge class
                $status_class = 'status-' . strtolower($order['order_status']);
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3 style="margin: 0;">Order #<?php echo $order['order_id']; ?></h3>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                                Placed on <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                            </p>
                        </div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </div>

                    <div class="order-info">
                        <div class="order-info-item">
                            <strong>Total Amount</strong>
                            <span style="font-size: 1.1rem; font-weight: bold; color: #007bff;">
                                Rs. <?php echo number_format($order['total_amount'], 2); ?>
                            </span>
                        </div>
                        <div class="order-info-item">
                            <strong>Payment Status</strong>
                            <span><?php echo $order['payment_status']; ?></span>
                        </div>
                        <div class="order-info-item">
                            <strong>Shipping Address</strong>
                            <span><?php echo $order['shipping_address']; ?></span>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <table style="margin-top: 15px;">
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
                                             style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; vertical-align: middle;"
                                             onerror="this.src='../assets/images/placeholder.jpg'">
                                        <?php echo $item['product_name']; ?>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td>Rs. <?php echo number_format($item_total, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Order Actions -->
                    <div class="order-actions">
                        <?php if (in_array($order['order_status'], ['Pending', 'Processing'])): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit" name="cancel_order" class="btn btn-danger">
                                    Cancel Order
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <p style="margin: 0; font-size: 1.1rem;">No active orders found.</p>
                <p style="margin: 10px 0 0 0;">Orders that are pending or processing will appear here.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Shipped Orders Section -->
    <div class="card">
        <h2 class="section-title shipped">Shipped Orders</h2>
        
        <?php if (mysqli_num_rows($shipped_orders_result) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($shipped_orders_result)): 
                // Get order items
                $items_sql = "SELECT oi.*, p.product_name, p.product_image_path 
                             FROM order_items oi 
                             JOIN product p ON oi.product_id = p.product_id 
                             WHERE oi.order_id = {$order['order_id']}";
                $items_result = mysqli_query($conn, $items_sql);
                
                // Determine status badge class
                $status_class = 'status-shipped';
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3 style="margin: 0;">Order #<?php echo $order['order_id']; ?></h3>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                                Placed on <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                            </p>
                        </div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </div>

                    <div class="order-info">
                        <div class="order-info-item">
                            <strong>Total Amount</strong>
                            <span style="font-size: 1.1rem; font-weight: bold; color: #007bff;">
                                Rs. <?php echo number_format($order['total_amount'], 2); ?>
                            </span>
                        </div>
                        <div class="order-info-item">
                            <strong>Payment Status</strong>
                            <span><?php echo $order['payment_status']; ?></span>
                        </div>
                        <div class="order-info-item">
                            <strong>Shipping Address</strong>
                            <span><?php echo $order['shipping_address']; ?></span>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <table style="margin-top: 15px;">
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
                                             style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; vertical-align: middle;"
                                             onerror="this.src='../assets/images/placeholder.jpg'">
                                        <?php echo $item['product_name']; ?>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td>Rs. <?php echo number_format($item_total, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Order Actions for Shipped Orders -->
                    <div class="order-actions">
                        <p style="margin: 0; color: #28a745; font-weight: bold;">
                            <i>✓ Your order is shipped!</i>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <p style="margin: 0; font-size: 1.1rem;">No shipped orders found.</p>
                <p style="margin: 10px 0 0 0;">Orders that have been shipped will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>