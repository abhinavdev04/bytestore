<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;

// Build dynamic WHERE conditions for search and category filtering
$conditions = [];

if ($search !== '') {
    $conditions[] = "(product_name LIKE '%$search%' OR product_description LIKE '%$search%')";
}

if ($category_id > 0) {
    $conditions[] = "category_id = $category_id";
}

$sql = "SELECT * FROM product";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}
$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

// Optional: fetch category name for heading when filtered
$category_name = '';
if ($category_id > 0) {
    $cat_res = mysqli_query($conn, "SELECT category_name FROM category WHERE category_id = $category_id LIMIT 1");
    if ($cat_res && mysqli_num_rows($cat_res) === 1) {
        $row = mysqli_fetch_assoc($cat_res);
        $category_name = $row['category_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>
            Browse Products
            <?php if ($category_name): ?>
                - <?php echo htmlspecialchars($category_name); ?>
            <?php endif; ?>
        </h2>
        
        <!-- Search Form -->
        <form method="GET" class="shop-search-form">
            <?php if ($category_id > 0): ?>
                <input type="hidden" name="category" value="<?php echo $category_id; ?>">
            <?php endif; ?>
            <div class="form-group">
                <input
                    type="text"
                    name="search"
                    placeholder="Search products..."
                    value="<?php echo $search; ?>"
                    class="shop-search-input"
                >
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="shop.php" class="btn btn-warning">Clear</a>
            </div>
        </form>
        
        <div class="product-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $product['product_id']; ?>" class="product-card-link">
                            <img src="../<?php echo $product['product_image_path']; ?>" alt="<?php echo $product['product_name']; ?>" onerror="this.src='../assets/images/placeholder.jpg'">
                        </a>
                        <div class="product-card-body">
                            <h3 style="min-height: 3em;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="product-card-title-link">
                                    <?php echo $product['product_name']; ?>
                                </a>
                            </h3>
                            <p class="text-muted" style="margin: 10px 0;min-height: 3em;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;">
                                <?php echo substr($product['product_description'], 0, 100); ?>...
                            </p>
                            <p class="price">Rs. <?php echo number_format($product['product_price'], 2); ?></p>
                            <p class="stock">Stock: <?php echo $product['product_stock']; ?></p>
                            
                            <?php if ($product['product_stock'] > 0): ?>
                                <form method="POST" action="add_to_cart.php" style="margin-top: 10px;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <div class="form-group">
                                        <label>Quantity:</label>
                                        <input
                                            type="number"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="<?php echo $product['product_stock']; ?>"
                                            class="shop-quantity-input"
                                        >
                                    </div>
                                    <button type="submit" class="btn btn-success shop-add-to-cart-button">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-danger shop-add-to-cart-button" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
