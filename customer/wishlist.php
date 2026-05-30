<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$customer_id = (int)$_SESSION['customer_id'];
$customer_res = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = $customer_id LIMIT 1");
$customer = mysqli_fetch_assoc($customer_res);

$sql = "SELECT w.*, p.* FROM wishlist w JOIN product p ON w.product_id = p.product_id WHERE w.customer_id = $customer_id ORDER BY w.added_at DESC";
$result = mysqli_query($conn, $sql);

$page_title = 'My Wishlist';
include '../includes/header.php';
echo renderBreadcrumbs([['label' => 'Home', 'url' => '../index.php'], ['label' => 'Wishlist', 'url' => '']]);
?>

<div class="card">
    <div class="card__header">
        <h1 class="card__title">My Wishlist</h1>
        <span class="text-muted"><?php echo $result ? mysqli_num_rows($result) : 0; ?> items saved</span>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($result)):
                $product['product_id'] = $product['product_id'];
                include '../includes/product_card.php';
            endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-heart"></i></div>
            <h3 class="empty-state__title">Your wishlist is empty</h3>
            <p class="empty-state__desc">Save products you love by clicking the heart icon on any product.</p>
            <a href="shop.php" class="btn btn--primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
