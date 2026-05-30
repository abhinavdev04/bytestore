<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$product_id = sanitizeInt($_GET['id'] ?? 0);
if ($product_id <= 0) { header('Location: shop.php'); exit; }

$customer_id = (int)$_SESSION['customer_id'];

// Track recently viewed
trackRecentlyViewed($conn, $customer_id, $product_id);

// Handle review submission — verified purchase only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!hasPurchasedProduct($conn, $customer_id, $product_id)) {
        flashMessage('error', 'You must purchase this product before leaving a review.');
    } elseif (hasReviewedProduct($conn, $customer_id, $product_id)) {
        flashMessage('error', 'You have already reviewed this product.');
    } else {
        $rating = max(1, min(5, (int)$_POST['rating']));
        $title = mysqli_real_escape_string($conn, trim($_POST['review_title'] ?? ''));
        $text = mysqli_real_escape_string($conn, trim($_POST['review_text']));
        if (strlen($text) >= 10) {
            mysqli_query($conn, "INSERT INTO product_review (product_id, customer_id, rating, review_title, review_text, is_verified) VALUES ($product_id, $customer_id, $rating, '$title', '$text', 1)");
            updateProductRatings($conn, $product_id);
            flashMessage('success', 'Thank you for your verified review!');
        }
    }
    header("Location: product.php?id=$product_id#reviews"); exit;
}

$sql = "SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id WHERE p.product_id = $product_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$product = $result && mysqli_num_rows($result) === 1 ? mysqli_fetch_assoc($result) : null;

if (!$product) { header('Location: shop.php'); exit; }

// Variants grouped by type
$variants = [];
$variant_types = [];
$vres = mysqli_query($conn, "SELECT * FROM product_variant WHERE product_id = $product_id ORDER BY variant_price ASC");
if ($vres) {
    while ($v = mysqli_fetch_assoc($vres)) {
        $variants[] = $v;
        $type = $v['variant_type'] ?? 'Configuration';
        if (!isset($variant_types[$type])) $variant_types[$type] = [];
        $variant_types[$type][] = $v;
    }
}

// Additional images
$images = [$product['product_image_path']];
$img_res = mysqli_query($conn, "SELECT image_path FROM product_image WHERE product_id = $product_id ORDER BY sort_order");
if ($img_res) while ($img = mysqli_fetch_assoc($img_res)) {
    if (!in_array($img['image_path'], $images)) $images[] = $img['image_path'];
}

// Specs
$specs = [];
$spec_res = mysqli_query($conn, "SELECT spec_key, spec_value FROM product_spec WHERE product_id = $product_id");
if ($spec_res) while ($s = mysqli_fetch_assoc($spec_res)) $specs[] = $s;

// Reviews
$reviews = mysqli_query($conn, "SELECT r.*, c.customer_name FROM product_review r JOIN customer c ON r.customer_id = c.customer_id WHERE r.product_id = $product_id ORDER BY r.created_at DESC LIMIT 20");

// Related products
$related = mysqli_query($conn, "SELECT * FROM product WHERE category_id = " . (int)$product['category_id'] . " AND product_id != $product_id ORDER BY rating_avg DESC LIMIT 4");

// Frequently bought together (same category, different products)
$fbt = mysqli_query($conn, "SELECT * FROM product WHERE category_id = " . (int)$product['category_id'] . " AND product_id != $product_id ORDER BY RAND() LIMIT 3");

$pricing = getProductPricing($product);
$discount = $pricing['discount_percent'];
$default_image_url = productImageUrl($product['product_image_path']);
$default_image_js = json_encode($default_image_url);
$in_wishlist = isInWishlist($conn, $customer_id, $product_id);
$can_review = customerCanReview($conn, $customer_id, $product_id);
$has_purchased = hasPurchasedProduct($conn, $customer_id, $product_id);

$page_title = $product['product_name'];
include '../includes/header.php';
echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '../index.php'],
    ['label' => 'Shop', 'url' => 'shop.php'],
    ['label' => $product['product_name'], 'url' => '']
]);
?>

<?php if ($msg = flashMessage('success')): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>
<?php if ($err = flashMessage('error')): ?><div class="alert alert-error"><?php echo e($err); ?></div><?php endif; ?>

<div class="card">
    <div class="product-detail">
        <!-- Gallery -->
        <div class="product-gallery">
            <div class="product-gallery__main" id="gallery-main">
                <img id="main-image" src="<?php echo e(productImageUrl($images[0])); ?>" alt="<?php echo e($product['product_name']); ?>" onerror="this.src='../assets/images/placeholder.svg'">
            </div>
            <?php if (count($images) > 1): ?>
            <div class="product-gallery__thumbs">
                <?php foreach ($images as $i => $img): ?>
                    <div class="product-gallery__thumb <?php echo $i === 0 ? 'active' : ''; ?>" onclick="setMainImage('<?php echo e(productImageUrl($img)); ?>', this)">
                        <img src="<?php echo e(productImageUrl($img)); ?>" alt="Thumbnail" onerror="this.src='../assets/images/placeholder.svg'">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <?php if (!empty($product['brand'])): ?>
                <span class="product-info__brand"><?php echo e($product['brand']); ?></span>
            <?php endif; ?>
            <h1 class="product-info__title"><?php echo e($product['product_name']); ?></h1>

            <?php if ($product['rating_count'] > 0): ?>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
                <?php echo renderStars($product['rating_avg'], 'lg'); ?>
                <span class="text-muted"><?php echo number_format($product['rating_avg'], 1); ?> (<?php echo (int)$product['rating_count']; ?> reviews)</span>
            </div>
            <?php endif; ?>

            <div class="product-info__price-row" id="product-pricing-row">
                <?php echo renderProductPricingHtml($pricing, 'detail'); ?>
            </div>
            <?php if ($pricing['on_sale'] && $pricing['sale_ends'] && strtotime($pricing['sale_ends']) > time()): ?>
                <?php echo renderSaleCountdown($pricing['sale_ends']); ?>
            <?php endif; ?>

            <p class="stock-indicator stock-indicator--<?php echo $product['product_stock'] > 0 ? 'in' : 'out'; ?>" id="display-stock">
                <?php if ($product['product_stock'] > 0): ?>
                    <i class="fa-solid fa-circle-check"></i> In Stock (<?php echo (int)$product['product_stock']; ?> available)
                <?php else: ?>
                    <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                <?php endif; ?>
            </p>

            <?php if (!empty($product['category_name'])): ?>
                <p class="text-muted" style="font-size:0.875rem;">Category: <a href="shop.php?category=<?php echo (int)$product['category_id']; ?>"><?php echo e($product['category_name']); ?></a></p>
            <?php endif; ?>

            <form method="POST" action="add_to_cart.php" class="product-detail-form" id="add-cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="variant_id" id="selected-variant-id" value="">

                <?php if (!empty($variants)): ?>
                    <?php foreach ($variant_types as $type => $type_variants): ?>
                    <div class="variant-selector">
                        <label class="variant-selector__label"><?php echo e($type); ?></label>
                        <div class="variant-options">
                            <?php foreach ($type_variants as $v): ?>
                                <?php $variant_img = variantImageUrl($v['variant_image_path'] ?? '', $product['product_image_path']); ?>
                                <button type="button" class="variant-option <?php echo $v === $type_variants[0] ? 'selected' : ''; ?> <?php echo $v['variant_stock'] <= 0 ? 'disabled' : ''; ?>"
                                    data-variant-id="<?php echo (int)$v['variant_id']; ?>"
                                    data-price="<?php echo $v['variant_price']; ?>"
                                    data-stock="<?php echo (int)$v['variant_stock']; ?>"
                                    data-image="<?php echo e($variant_img); ?>"
                                    <?php echo $v['variant_stock'] <= 0 ? 'disabled' : ''; ?>>
                                    <?php echo e($v['variant_name']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="form-group" style="margin:1.5rem 0;">
                    <label for="quantity">Quantity</label>
                    <?php echo renderQtyStepper('quantity', 1, 1, (int)$product['product_stock'], 'id="quantity"'); ?>
                </div>

                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <?php if ($product['product_stock'] > 0 || !empty($variants)): ?>
                        <button type="submit" class="btn btn--success btn--lg" id="add-cart-btn"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
                    <?php else: ?>
                        <button type="button" class="btn btn--danger btn--lg" disabled>Out of Stock</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn--outline wishlist-btn <?php echo $in_wishlist ? 'active' : ''; ?>" data-product-id="<?php echo $product_id; ?>">
                        <i class="fa-solid fa-heart"></i> <?php echo $in_wishlist ? 'Saved' : 'Wishlist'; ?>
                    </button>
                    <button type="button" class="btn btn--outline compare-btn" data-product-id="<?php echo $product_id; ?>"><i class="fa-solid fa-scale-balanced"></i> Compare</button>
                </div>
            </form>

            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                <p class="product-detail-description"><?php echo nl2br(e($product['product_description'])); ?></p>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($specs)): ?>
<div class="card">
    <h2 style="margin-bottom:1rem;">Specifications</h2>
    <table class="product-specs__table">
        <?php foreach ($specs as $spec): ?>
            <tr><td><?php echo e($spec['spec_key']); ?></td><td><?php echo e($spec['spec_value']); ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<div class="card" id="reviews">
    <h2 style="margin-bottom:1rem;">Customer Reviews</h2>
    <?php if ($product['rating_count'] > 0): ?>
    <div class="reviews-summary">
        <div class="reviews-summary__score"><?php echo number_format($product['rating_avg'], 1); ?></div>
        <div><?php echo renderStars($product['rating_avg'], 'lg'); ?><br><span class="text-muted"><?php echo (int)$product['rating_count']; ?> reviews</span></div>
    </div>
    <?php endif; ?>

    <?php if ($can_review): ?>
    <form method="POST" style="margin-bottom:2rem;padding:1.5rem;background:var(--color-bg);border-radius:var(--radius-lg);">
        <h3 style="margin-bottom:1rem;">Write a Verified Review</h3>
        <div class="form-group">
            <label>Rating</label>
            <select name="rating" required>
                <?php for ($r = 5; $r >= 1; $r--): ?><option value="<?php echo $r; ?>"><?php echo $r; ?> Stars</option><?php endfor; ?>
            </select>
        </div>
        <div class="form-group"><label>Title (optional)</label><input type="text" name="review_title" maxlength="200"></div>
        <div class="form-group"><label>Review</label><textarea name="review_text" rows="4" required minlength="10" placeholder="Share your experience with this product..."></textarea></div>
        <button type="submit" name="submit_review" class="btn btn--primary"><i class="fa-solid fa-pen"></i> Submit Review</button>
    </form>
    <?php elseif ($has_purchased): ?>
        <div class="review-notice"><i class="fa-solid fa-circle-check"></i> You have already reviewed this product.</div>
    <?php else: ?>
        <div class="review-notice"><i class="fa-solid fa-circle-info"></i> You must purchase this product before leaving a review.</div>
    <?php endif; ?>

    <?php if ($reviews && mysqli_num_rows($reviews) > 0): ?>
        <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
        <div class="review-item">
            <div class="review-item__header">
                <span class="review-item__author"><?php echo e($review['customer_name']); ?>
                    <?php if (!empty($review['is_verified'])): ?>
                        <span class="verified-badge"><i class="fa-solid fa-certificate"></i> Verified Purchase</span>
                    <?php endif; ?>
                </span>
                <span class="review-item__date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
            </div>
            <?php echo renderStars($review['rating']); ?>
            <?php if ($review['review_title']): ?><strong style="display:block;margin:0.5rem 0;"><?php echo e($review['review_title']); ?></strong><?php endif; ?>
            <p><?php echo e($review['review_text']); ?></p>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
    <?php endif; ?>
</div>

<?php if ($fbt && mysqli_num_rows($fbt) > 0): ?>
<div class="card">
    <h2 style="margin-bottom:1rem;">Frequently Bought Together</h2>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($fbt)): include '../includes/product_card.php'; endwhile; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($related && mysqli_num_rows($related) > 0): ?>
<div class="card">
    <h2 style="margin-bottom:1rem;">Related Products</h2>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($related)): include '../includes/product_card.php'; endwhile; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function () {
    var defaultImage = <?php echo $default_image_js; ?>;

    document.querySelectorAll('.variant-option:not(.disabled)').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var group = this.closest('.variant-selector');
            group.querySelectorAll('.variant-option').forEach(function(b) { b.classList.remove('selected'); });
            this.classList.add('selected');

            document.getElementById('selected-variant-id').value = this.dataset.variantId;
            var priceEl = document.querySelector('#product-pricing-row .product-pricing__current');
            if (priceEl) {
                priceEl.textContent = 'Rs. ' + parseFloat(this.dataset.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            var stock = parseInt(this.dataset.stock, 10);
            var stockEl = document.getElementById('display-stock');
            stockEl.innerHTML = stock > 0
                ? '<i class="fa-solid fa-circle-check"></i> In Stock (' + stock + ' available)'
                : '<i class="fa-solid fa-circle-xmark"></i> Out of Stock';
            stockEl.className = 'stock-indicator stock-indicator--' + (stock > 0 ? 'in' : 'out');
            var qtyInput = document.querySelector('#quantity');
            if (qtyInput) qtyInput.max = stock;
            var addBtn = document.getElementById('add-cart-btn');
            if (addBtn) addBtn.disabled = stock <= 0;

            if (window.setMainImage) {
                var img = this.dataset.image || defaultImage;
                if (img) window.setMainImage(img, null);
            }
        });
    });

    var firstVariant = document.querySelector('.variant-option.selected');
    if (firstVariant) {
        document.getElementById('selected-variant-id').value = firstVariant.dataset.variantId;
        if (firstVariant.dataset.image && window.setMainImage) {
            window.setMainImage(firstVariant.dataset.image, null);
        }
    }
    if (window.initQtySteppers) initQtySteppers();
})();
</script>
<script src="../assets/js/product-gallery.js"></script>

<?php include '../includes/footer.php'; ?>
