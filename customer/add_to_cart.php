<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $customer_id = $_SESSION['customer_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $variant_id = isset($_POST['variant_id']) && $_POST['variant_id'] !== '' ? (int)$_POST['variant_id'] : null;
    
    if ($variant_id) {
        $result = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id=$customer_id AND product_id=$product_id AND variant_id=$variant_id");
    } else {
        $result = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id=$customer_id AND product_id=$product_id AND variant_id IS NULL");
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_query($conn, "UPDATE cart SET quantity=" . ($row['quantity'] + $quantity) . " WHERE cart_id=" . $row['cart_id']);
    } else {
        if ($variant_id) {
            mysqli_query($conn, "INSERT INTO cart (customer_id, product_id, variant_id, quantity) VALUES ($customer_id, $product_id, $variant_id, $quantity)");
        } else {
            mysqli_query($conn, "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)");
        }
    }
    header("Location: cart.php");
    exit();
}
header("Location: shop.php");
exit();
?>
