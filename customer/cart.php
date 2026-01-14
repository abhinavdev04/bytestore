<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

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

// Get cart items
$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.product_price, p.product_image_path, p.product_stock
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.customer_id = $customer_id";
$result = mysqli_query($conn, $sql);

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
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
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

