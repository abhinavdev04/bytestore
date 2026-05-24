<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

// Get the product ID from query string (e.g. product.php?id=123)
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id <= 0) {
    // If no valid ID was provided, send the user back to the shop
    header('Location: shop.php');
    exit;
}

// Fetch the product from the database
$sql = "SELECT * FROM product WHERE product_id = $product_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$product = $result && mysqli_num_rows($result) === 1 ? mysqli_fetch_assoc($result) : null;
// Fetch variants for this product (if any)
$variants = [];
if ($product) {
    $variants_sql = "SELECT * FROM product_variant WHERE product_id = " . (int)$product_id . " ORDER BY variant_price ASC";
    $variants_result = mysqli_query($conn, $variants_sql);
    if ($variants_result && mysqli_num_rows($variants_result) > 0) {
        while ($v = mysqli_fetch_assoc($variants_result)) {
            $variants[] = $v;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $product ? htmlspecialchars($product['product_name']) . ' - ByteStore' : 'Product not found - ByteStore'; ?>
    </title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="card">
    <?php if ($product): ?>
        <div class="product-detail">
            <div class="product-detail-image">
                <img src="../<?php echo $product['product_image_path']; ?>"
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                     onerror="this.src='../assets/images/placeholder.jpg'">
            </div>
            <div class="product-detail-info">
                <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                <div class="product-detail-header-line"></div>
                <p class="product-detail-price">
                    Rs. <?php echo number_format($product['product_price'], 2); ?>
                </p>
                <p class="product-detail-stock">
                    Stock: <?php echo (int) $product['product_stock']; ?>
                </p>

                <?php if ($product['product_stock'] > 0 || count($variants) > 0): ?>
                    <form method="POST" action="add_to_cart.php" class="product-detail-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <?php if (!empty($variants)): ?>
                            <div class="form-group">
                                <label for="variant">Variant:</label>
                                <select id="variant" name="variant_id" class="shop-variant-select">
                                    <?php foreach ($variants as $v): ?>
                                        <option value="<?php echo $v['variant_id']; ?>" data-price="<?php echo $v['variant_price']; ?>" data-stock="<?php echo (int)$v['variant_stock']; ?>">
                                            <?php echo htmlspecialchars($v['variant_name']); ?> - Rs. <?php echo number_format($v['variant_price'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="1"
                                min="1"
                                max="<?php echo (int) $product['product_stock']; ?>"
                                class="shop-quantity-input"
                            >
                        </div>
                        <button type="submit" class="btn btn-success">
                            Add to Cart
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-danger" disabled>Out of Stock</button>
                <?php endif; ?>

                <div class="product-detail-meta">
                    <a href="shop.php" class="btn btn-warning">Back to Shop</a>
                </div>

                <p class="product-detail-description">
                    <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <p>Sorry, we could not find that product.</p>
        <a href="shop.php" class="btn btn-primary mt-20">Back to Shop</a>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var variantSelect = document.getElementById('variant');
    var priceEl = document.querySelector('.product-detail-price');
    var stockEl = document.querySelector('.product-detail-stock');
    var qtyInput = document.getElementById('quantity');

    function formatPrice(n) {
        return parseFloat(n).toFixed(2);
    }

    if (variantSelect) {
        function onVariantChange() {
            var opt = variantSelect.options[variantSelect.selectedIndex];
            var vPrice = opt.getAttribute('data-price');
            var vStock = opt.getAttribute('data-stock');

            if (priceEl) {
                priceEl.textContent = 'Rs. ' + formatPrice(vPrice);
            }
            if (stockEl) {
                stockEl.textContent = 'Stock: ' + parseInt(vStock);
            }
            if (qtyInput) {
                qtyInput.max = vStock;
                if (parseInt(qtyInput.value) > parseInt(vStock)) {
                    qtyInput.value = vStock > 0 ? vStock : 1;
                }
                var addBtn = document.querySelector('.product-detail-form button[type="submit"]');
                if (addBtn) {
                    addBtn.disabled = parseInt(vStock) === 0;
                    if (addBtn.disabled) addBtn.classList.remove('btn-success');
                }
            }
        }

        variantSelect.addEventListener('change', onVariantChange);
        onVariantChange();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>

