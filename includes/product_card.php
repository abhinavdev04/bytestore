<?php
/**
 * Reusable Product Card Component
 * Required vars: $product, optional: $show_actions (default true)
 */
if (!isset($product)) return;
$base = getBasePath();
$pid = (int)$product['product_id'];
$link = isset($_SESSION['customer_id']) ? $base . 'customer/product.php?id=' . $pid : $base . 'customer/login.php';
$pricing = getProductPricing($product);
$price = $pricing['price'];
$original = $pricing['original'];
$discount = $pricing['discount_percent'];
$rating = isset($product['rating_avg']) ? (float)$product['rating_avg'] : 0;
$ratingCount = isset($product['rating_count']) ? (int)$product['rating_count'] : 0;
$stock = (int)$product['product_stock'];
$inWishlist = isset($_SESSION['customer_id']) && isset($conn) ? isInWishlist($conn, $_SESSION['customer_id'], $pid) : false;
$show_actions = $show_actions ?? true;
?>
<article class="product-card shop-product-item" data-product-id="<?php echo $pid; ?>">
    <div class="product-card__badges">
        <?php if ($discount > 0): ?>
            <span class="badge badge--sale">-<?php echo $discount; ?>%</span>
        <?php endif; ?>
        <?php if (!empty($product['is_featured'])): ?>
            <span class="badge badge--featured">Featured</span>
        <?php endif; ?>
        <?php if ($stock <= 5 && $stock > 0): ?>
            <span class="badge badge--low">Low Stock</span>
        <?php elseif ($stock === 0): ?>
            <span class="badge badge--out">Out of Stock</span>
        <?php endif; ?>
    </div>
    <div class="product-card__image-wrap">
        <a href="<?php echo e($link); ?>" class="product-card__link">
            <img src="<?php echo e(productImageUrl($product['product_image_path'])); ?>"
                 alt="<?php echo e($product['product_name']); ?>"
                 loading="lazy"
                 onerror="this.src='<?php echo e(assetUrl('assets/images/placeholder.svg')); ?>'">
        </a>
        <?php if ($show_actions && isset($_SESSION['customer_id'])): ?>
        <div class="product-card__actions">
            <button type="button" class="product-card__action-btn wishlist-btn <?php echo $inWishlist ? 'active' : ''; ?>"
                    data-product-id="<?php echo $pid; ?>" title="Add to wishlist" aria-label="Toggle wishlist">
                <i class="fa-solid fa-heart"></i>
            </button>
            <button type="button" class="product-card__action-btn compare-btn"
                    data-product-id="<?php echo $pid; ?>" title="Compare" aria-label="Add to compare">
                <i class="fa-solid fa-scale-balanced"></i>
            </button>
            <a href="<?php echo e($link); ?>" class="product-card__action-btn" title="Quick view" aria-label="View product">
                <i class="fa-solid fa-eye"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <div class="product-card__body">
        <?php if (!empty($product['brand'])): ?>
            <span class="product-card__brand"><?php echo e($product['brand']); ?></span>
        <?php endif; ?>
        <h3 class="product-card__title">
            <a href="<?php echo e($link); ?>"><?php echo e($product['product_name']); ?></a>
        </h3>
        <?php if ($rating > 0): ?>
        <div class="product-card__rating">
            <?php echo renderStars($rating); ?>
            <span class="product-card__rating-count">(<?php echo $ratingCount; ?>)</span>
        </div>
        <?php endif; ?>
        <?php echo renderProductPricingHtml($pricing, 'card'); ?>
        <div class="product-card__footer">
            <?php if ($stock > 0): ?>
                <span class="stock-indicator stock-indicator--in">In Stock</span>
            <?php else: ?>
                <span class="stock-indicator stock-indicator--out">Out of Stock</span>
            <?php endif; ?>
            <a href="<?php echo e($link); ?>" class="btn btn--sm btn--primary">View Details</a>
        </div>
    </div>
</article>
