<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];
$error = '';
$success = '';

$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer WHERE customer_id=$customer_id"));
$cart_result = mysqli_query($conn, "SELECT c.quantity, c.variant_id, p.*, pv.variant_price, pv.variant_stock, pv.variant_name
    FROM cart c JOIN product p ON c.product_id = p.product_id
    LEFT JOIN product_variant pv ON c.variant_id = pv.variant_id
    WHERE c.customer_id = $customer_id");

$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $pricing = getProductPricing($item);
    $price = $item['variant_price'] ? (float)$item['variant_price'] : $pricing['price'];
    $item['pricing'] = $pricing;
    $stock = $item['variant_stock'] !== null ? (int)$item['variant_stock'] : (int)$item['product_stock'];
    $item['effective_price'] = $price;
    $item['effective_stock'] = $stock;
    $item_total = $price * $item['quantity'];
    $total += $item_total;
    $cart_items[] = $item;
}

// Handle Cash on Delivery
if (isset($_POST['place_order']) && $_POST['payment_method'] === 'cod') {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $customer_phone_escaped = mysqli_real_escape_string($conn, $customer_phone);

    if (empty($customer_phone)) {
        $error = "Please enter your phone number.";
    } elseif (empty($cart_items)) {
        $error = "Your cart is empty!";
    } else {
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['effective_stock']) {
                $error = "Insufficient stock for " . $item['product_name'];
                break;
            }
        }

        if (!$error) {
            mysqli_query($conn, "UPDATE customer SET customer_phone='$customer_phone_escaped' WHERE customer_id=$customer_id");

            if (mysqli_query($conn, "INSERT INTO orders (customer_id, total_amount, shipping_address, customer_phone, order_status, payment_status) VALUES ($customer_id, $total, '$shipping_address', '$customer_phone_escaped', 'Pending', 'Pending')")) {
                $order_id = mysqli_insert_id($conn);

                foreach ($cart_items as $item) {
                    $price = $item['effective_price'];
                    $variant_sql = $item['variant_id'] ? (int)$item['variant_id'] : 'NULL';
                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES ($order_id, {$item['product_id']}, $variant_sql, {$item['quantity']}, $price)");
                    if ($item['variant_id']) {
                        mysqli_query($conn, "UPDATE product_variant SET variant_stock=" . ($item['effective_stock'] - $item['quantity']) . " WHERE variant_id=" . (int)$item['variant_id']);
                    } else {
                        mysqli_query($conn, "UPDATE product SET product_stock=" . ($item['effective_stock'] - $item['quantity']) . " WHERE product_id={$item['product_id']}");
                    }
                }

                mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");
                $success = "Order placed successfully! Order ID: #$order_id";
            } else {
                $error = "Failed to place order. Please try again.";
            }
        }
    }
}

// Handle eSewa Payment - store order in session then redirect
if (isset($_POST['place_order']) && $_POST['payment_method'] === 'esewa') {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $customer_phone_escaped = mysqli_real_escape_string($conn, $customer_phone);

    if (empty($customer_phone)) {
        $error = "Please enter your phone number.";
    } elseif (empty($cart_items)) {
        $error = "Your cart is empty!";
    } else {
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['effective_stock']) {
                $error = "Insufficient stock for " . $item['product_name'];
                break;
            }
        }

        if (!$error) {
            mysqli_query($conn, "UPDATE customer SET customer_phone='$customer_phone_escaped' WHERE customer_id=$customer_id");

            if (mysqli_query($conn, "INSERT INTO orders (customer_id, total_amount, shipping_address, customer_phone, order_status, payment_status) VALUES ($customer_id, $total, '$shipping_address', '$customer_phone_escaped', 'Pending', 'Pending')")) {
                $order_id = mysqli_insert_id($conn);

                foreach ($cart_items as $item) {
                    $price = $item['effective_price'];
                    $variant_sql = $item['variant_id'] ? (int)$item['variant_id'] : 'NULL';
                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES ($order_id, {$item['product_id']}, $variant_sql, {$item['quantity']}, $price)");
                }

                // Store order info in session for esewa_payment.php to use
                $_SESSION['esewa_order_id']    = $order_id;
                $_SESSION['esewa_amount']      = $total;
                $_SESSION['esewa_cart_items']  = $cart_items;

                // Redirect to eSewa payment initiation page
                header("Location: esewa_payment.php");
                exit();
            } else {
                $error = "Failed to create order. Please try again.";
            }
        }
    }
}
$page_title = 'Checkout';
include '../includes/header.php';
echo renderBreadcrumbs([['label' => 'Home', 'url' => '../index.php'], ['label' => 'Cart', 'url' => 'cart.php'], ['label' => 'Checkout', 'url' => '']]);
?>
<style>
        .payment-options { display: flex; flex-direction: column; gap: 10px; margin-top: 5px; }
        .payment-option { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: border-color 0.2s; }
        .payment-option:hover { border-color: #00c6ff; }
        .payment-option input[type="radio"] { accent-color: #00c6ff; width: 18px; height: 18px; }
        .payment-option.esewa-option { border-color: #60BB46; }
        .esewa-badge { background: #60BB46; color: white; font-size: 12px; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        .esewa-logo-text { color: #60BB46; font-weight: bold; font-size: 16px; }
    </style>

    <div class="card checkout-form">
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
                                    $price = $item['effective_price'];
                                    $item_total = $price * $item['quantity'];
                                ?>
                                    <tr>
                                        <td><?php echo e($item['product_name']); ?><?php if (!empty($item['variant_name'])): ?><br><small class="text-muted"><?php echo e($item['variant_name']); ?></small><?php endif; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo formatPrice($price); ?></td>
                                        <td><?php echo formatPrice($item_total); ?></td>
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
                                <input type="text" name="customer_phone"
                                    value="<?php echo htmlspecialchars($customer['customer_phone'] ?? ''); ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Shipping Address</label>
                                <textarea name="shipping_address" rows="4" required><?php echo htmlspecialchars($customer['customer_address']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <div class="payment-options">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <span><i class="fa-solid fa-money-bill-wave"></i> Cash on Delivery</span>
                                    </label>
                                    <label class="payment-option esewa-option">
                                        <input type="radio" name="payment_method" value="esewa">
                                        <span class="esewa-logo-text">eSewa</span>
                                        <span class="esewa-badge">Online Payment</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" name="place_order" class="btn btn-success" style="width: 100%; margin-top: 10px;">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p>Your cart is empty. <a href="shop.php">Start shopping!</a></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>