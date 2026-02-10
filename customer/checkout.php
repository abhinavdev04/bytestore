<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];
$error = '';
$success = '';

$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer WHERE customer_id=$customer_id"));
$cart_result = mysqli_query($conn, "SELECT c.quantity, p.product_id, p.product_name, p.product_price, p.product_stock FROM cart c JOIN product p ON c.product_id = p.product_id WHERE c.customer_id = $customer_id");

$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $item_total = $item['product_price'] * $item['quantity'];
    $total += $item_total;
    $cart_items[] = $item;
}

if (isset($_POST['place_order'])) {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $customer_phone_escaped = mysqli_real_escape_string($conn, $customer_phone);
    
    if (empty($customer_phone)) {
        $error = "Please enter your phone number.";
    } elseif (empty($cart_items)) {
        $error = "Your cart is empty!";
    } else {
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['product_stock']) {
                $error = "Insufficient stock for " . $item['product_name'];
                break;
            }
        }
        
        if (!$error) {
            // Save latest phone number to customer profile
            mysqli_query($conn, "UPDATE customer SET customer_phone='$customer_phone_escaped' WHERE customer_id=$customer_id");

            if (mysqli_query($conn, "INSERT INTO orders (customer_id, total_amount, shipping_address, customer_phone, order_status, payment_status) VALUES ($customer_id, $total, '$shipping_address', '$customer_phone_escaped', 'Pending', 'Pending')")) {
                $order_id = mysqli_insert_id($conn);
                
                foreach ($cart_items as $item) {
                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['product_price']})");
                    mysqli_query($conn, "UPDATE product SET product_stock=" . ($item['product_stock'] - $item['quantity']) . " WHERE product_id={$item['product_id']}");
                }
                
                mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");
                $success = "Order placed successfully! Order ID: #$order_id";
            } else {
                $error = "Failed to place order. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>Checkout</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        <?php else: ?>
            <?php if (!empty($cart_items)): ?>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div>
                        <h3>Order Summary</h3>
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
                                <?php foreach ($cart_items as $item): 
                                    $item_total = $item['product_price'] * $item['quantity'];
                                ?>
                                    <tr>
                                        <td><?php echo $item['product_name']; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>Rs. <?php echo number_format($item['product_price'], 2); ?></td>
                                        <td>Rs. <?php echo number_format($item_total, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                                    <td style="font-weight: bold;">Rs. <?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div>
                        <h3>Shipping Information</h3>
                        <form method="POST">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input
                                    type="text"
                                    name="customer_phone"
                                    value="<?php echo htmlspecialchars($customer['customer_phone'] ?? ''); ?>"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label>Shipping Address</label>
                                <textarea name="shipping_address" rows="5" required><?php echo $customer['customer_address']; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                    <option>Cash on Delivery</option>
                                    <option disabled>Credit Card (Coming Soon)</option>
                                    <option disabled>Esewa (Coming Soon)</option>
                                    <option disabled>Khalti (Coming Soon)</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="place_order" class="btn btn-success" style="width: 100%;">Place Order</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p>Your cart is empty. <a href="shop.php">Start shopping!</a></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

