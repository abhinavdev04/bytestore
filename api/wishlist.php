<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    jsonResponse(['success' => false, 'message' => 'Please login first', 'redirect' => '../customer/login.php'], 401);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? 'toggle';
$product_id = sanitizeInt($input['product_id'] ?? 0);
$customer_id = (int)$_SESSION['customer_id'];

if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
}

$check = mysqli_query($conn, "SELECT wishlist_id FROM wishlist WHERE customer_id = $customer_id AND product_id = $product_id LIMIT 1");
$exists = $check && mysqli_num_rows($check) > 0;

if ($action === 'toggle') {
    if ($exists) {
        mysqli_query($conn, "DELETE FROM wishlist WHERE customer_id = $customer_id AND product_id = $product_id");
        jsonResponse(['success' => true, 'in_wishlist' => false, 'message' => 'Removed from wishlist', 'count' => getWishlistCount($conn, $customer_id)]);
    } else {
        mysqli_query($conn, "INSERT INTO wishlist (customer_id, product_id) VALUES ($customer_id, $product_id)");
        jsonResponse(['success' => true, 'in_wishlist' => true, 'message' => 'Added to wishlist', 'count' => getWishlistCount($conn, $customer_id)]);
    }
} elseif ($action === 'remove') {
    mysqli_query($conn, "DELETE FROM wishlist WHERE customer_id = $customer_id AND product_id = $product_id");
    jsonResponse(['success' => true, 'message' => 'Removed from wishlist', 'count' => getWishlistCount($conn, $customer_id)]);
}

jsonResponse(['success' => false, 'message' => 'Unknown action'], 400);
