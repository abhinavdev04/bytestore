<?php
session_start();
require 'config/config.php';
require 'includes/functions.php';

$page_title = 'Premium Tech Store Nepal';

// Hero products (featured)
$hero_sql = "SELECT * FROM product WHERE is_featured = 1 ORDER BY rating_avg DESC LIMIT 5";
$hero_result = mysqli_query($conn, $hero_sql);
$hero_products = [];
if ($hero_result) {
    while ($row = mysqli_fetch_assoc($hero_result)) $hero_products[] = $row;
}
if (empty($hero_products)) {
    $fallback = mysqli_query($conn, "SELECT * FROM product ORDER BY product_price DESC LIMIT 5");
    if ($fallback) while ($row = mysqli_fetch_assoc($fallback)) $hero_products[] = $row;
}

// Best sellers
$bestsellers = mysqli_query($conn, "SELECT p.*, COALESCE(SUM(oi.quantity),0) as total_sold 
    FROM product p LEFT JOIN order_items oi ON p.product_id = oi.product_id 
    GROUP BY p.product_id ORDER BY total_sold DESC, p.rating_avg DESC LIMIT 8");

// Trending
$trending = mysqli_query($conn, "SELECT * FROM product WHERE is_trending = 1 ORDER BY created_at DESC LIMIT 8");
if (!$trending || mysqli_num_rows($trending) === 0) {
    $trending = mysqli_query($conn, "SELECT * FROM product ORDER BY RAND() LIMIT 8");
}

// New arrivals
$new_arrivals = mysqli_query($conn, "SELECT * FROM product ORDER BY created_at DESC LIMIT 8");

// Featured
$featured = mysqli_query($conn, "SELECT * FROM product WHERE is_featured = 1 ORDER BY rating_avg DESC LIMIT 8");

// Categories
$categories = getCategories($conn);

// Brands
$brands = mysqli_query($conn, "SELECT DISTINCT brand FROM product WHERE brand IS NOT NULL AND brand != '' ORDER BY brand LIMIT 12");

// GIF showcase items
$gif_items = getGifShowcaseItems($conn);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" aria-label="Featured products">
    <?php foreach ($hero_products as $i => $hp):
        $link = isset($_SESSION['customer_id']) ? 'customer/product.php?id=' . $hp['product_id'] : 'customer/login.php';
        $hero_pricing = getProductPricing($hp);
    ?>
    <div class="hero__slide <?php echo $i === 0 ? 'active' : ''; ?>">
        <div class="hero__content">
            <?php if (!empty($hp['brand'])): ?>
                <span class="hero__badge"><?php echo e($hp['brand']); ?></span>
            <?php else: ?>
                <span class="hero__badge">New Arrival</span>
            <?php endif; ?>
            <h1 class="hero__title"><?php echo e($hp['product_name']); ?></h1>
            <p class="hero__subtitle">Experience premium technology at unbeatable prices. Official warranty, free delivery in Kathmandu Valley on orders above Rs. 50,000.</p>
            <div class="hero__actions">
                <a href="<?php echo e($link); ?>" class="btn btn--primary btn--lg">Shop Now — <?php echo formatPrice($hero_pricing['price']); ?></a>
                <a href="customer/shop.php" class="btn btn--secondary btn--lg" style="color:white;border-color:rgba(255,255,255,0.3);background:rgba(255,255,255,0.1);">Browse All</a>
            </div>
        </div>
        <div class="hero__image">
            <img src="<?php echo e(productImageUrl($hp['product_image_path'])); ?>" alt="<?php echo e($hp['product_name']); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>" onerror="this.src='assets/images/placeholder.svg'">
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (count($hero_products) > 1): ?>
    <div class="hero__nav">
        <?php foreach ($hero_products as $i => $hp): ?>
            <button class="hero__dot <?php echo $i === 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php if (!empty($gif_items)): ?>
<section class="gif-showcase" id="gifShowcase" aria-label="Product showcase">
    <?php foreach ($gif_items as $i => $gif):
        $gif_link = !empty($gif['product_id'])
            ? (isset($_SESSION['customer_id']) ? 'customer/product.php?id=' . (int)$gif['product_id'] : 'customer/login.php')
            : 'customer/shop.php';
    ?>
    <a href="<?php echo e($gif_link); ?>" class="gif-showcase__slide <?php echo $i === 0 ? 'active' : ''; ?>" aria-label="View <?php echo e($gif['name']); ?>">
        <img src="<?php echo e(assetUrl($gif['path'])); ?>" alt="<?php echo e($gif['name']); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
        <div class="gif-showcase__caption"><strong><?php echo e($gif['name']); ?></strong> <i class="fa-solid fa-arrow-right"></i></div>
    </a>
    <?php endforeach; ?>
    <?php if (count($gif_items) > 1): ?>
    <button type="button" class="gif-showcase__nav gif-showcase__nav--prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
    <button type="button" class="gif-showcase__nav gif-showcase__nav--next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
    <div class="gif-showcase__dots">
        <?php foreach ($gif_items as $i => $gif): ?>
            <button type="button" class="gif-showcase__dot <?php echo $i === 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<script>
(function(){
    const showcase = document.getElementById('gifShowcase');
    if (!showcase) return;
    const slides = showcase.querySelectorAll('.gif-showcase__slide');
    const dots = showcase.querySelectorAll('.gif-showcase__dot');
    if (slides.length <= 1) return;
    let idx = 0, timer;
    function go(n){
        slides[idx].classList.remove('active');
        if(dots[idx]) dots[idx].classList.remove('active');
        idx=(n+slides.length)%slides.length;
        slides[idx].classList.add('active');
        if(dots[idx]) dots[idx].classList.add('active');
    }
    showcase.querySelectorAll('.gif-showcase__nav, .gif-showcase__dot').forEach(el => {
        el.addEventListener('click', e => e.stopPropagation());
    });
    function next(){ go(idx+1); }
    function prev(){ go(idx-1); }
    function reset(){ clearInterval(timer); timer=setInterval(next,5000); }
    showcase.querySelector('.gif-showcase__nav--next')?.addEventListener('click', ()=>{ next(); reset(); });
    showcase.querySelector('.gif-showcase__nav--prev')?.addEventListener('click', ()=>{ prev(); reset(); });
    dots.forEach((d,i)=> d.addEventListener('click', ()=>{ go(i); reset(); }));
    reset();
})();
</script>
<?php endif; ?>

<!-- Trust Badges -->
<div class="trust-badges">
    <div class="trust-badge">
        <div class="trust-badge__icon"><i class="fa-solid fa-truck-fast"></i></div>
        <div><div class="trust-badge__title">Fast Delivery</div><div class="trust-badge__desc">1-2 days in Kathmandu Valley</div></div>
    </div>
    <div class="trust-badge">
        <div class="trust-badge__icon"><i class="fa-solid fa-shield-halved"></i></div>
        <div><div class="trust-badge__title">Official Warranty</div><div class="trust-badge__desc">Genuine products with warranty</div></div>
    </div>
    <div class="trust-badge">
        <div class="trust-badge__icon"><i class="fa-solid fa-credit-card"></i></div>
        <div><div class="trust-badge__title">Secure Payment</div><div class="trust-badge__desc">eSewa & Cash on Delivery</div></div>
    </div>
    <div class="trust-badge">
        <div class="trust-badge__icon"><i class="fa-solid fa-rotate-left"></i></div>
        <div><div class="trust-badge__title">Easy Returns</div><div class="trust-badge__desc">7-day return policy</div></div>
    </div>
</div>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<section class="section">
    <div class="section__header">
        <div><h2 class="section__title">Shop by Category</h2><p class="section__subtitle">Find exactly what you need</p></div>
        <a href="customer/shop.php" class="section__link">View all →</a>
    </div>
    <div class="categories-scroll-wrap">
        <div class="categories-scroll-fade categories-scroll-fade--left" aria-hidden="true"></div>
        <div class="categories-scroll-fade categories-scroll-fade--right" aria-hidden="true"></div>
        <div class="categories-scroll" id="categoriesScroll">
            <?php foreach (array_slice($categories, 0, 12) as $cat): ?>
                <a href="customer/shop.php?category=<?php echo (int)$cat['category_id']; ?>" class="category-card">
                    <div class="category-card__icon"><?php echo renderCategoryIcon($cat['category_name']); ?></div>
                    <span class="category-card__name"><?php echo e($cat['category_name']); ?></span>
                    <span class="category-card__count"><?php echo (int)$cat['product_count']; ?> items</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<?php if ($featured && mysqli_num_rows($featured) > 0): ?>
<section class="section">
    <div class="section__header">
        <div><h2 class="section__title">Featured Products</h2><p class="section__subtitle">Hand-picked premium selections</p></div>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($featured)): include 'includes/product_card.php'; endwhile; ?>
    </div>
</section>
<?php endif; ?>

<!-- Promo Banners -->
<div class="promo-banner">
    <div class="promo-card promo-card--blue">
        <h3 class="promo-card__title">Gaming Season Sale</h3>
        <p class="promo-card__desc">Up to 15% off on gaming laptops and accessories</p>
        <a href="customer/shop.php?category=3" class="btn btn--promo" style="width:fit-content;">Shop Gaming →</a>
    </div>
    <div class="promo-card promo-card--purple">
        <h3 class="promo-card__title">Apple Collection</h3>
        <p class="promo-card__desc">MacBooks, iPhones, iPads with official warranty</p>
        <a href="customer/shop.php?search=Apple" class="btn btn--promo" style="width:fit-content;">Explore Apple →</a>
    </div>
</div>

<!-- Best Sellers -->
<?php if ($bestsellers && mysqli_num_rows($bestsellers) > 0): ?>
<section class="section scroll-row">
    <div class="section__header">
        <div><h2 class="section__title">Best Sellers</h2><p class="section__subtitle">Most popular with our customers</p></div>
        <a href="customer/shop.php?sort=bestseller" class="section__link">View all →</a>
    </div>
    <button class="scroll-row__nav scroll-row__nav--prev" onclick="scrollRow('bestsellers-row','left')" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
    <div class="scroll-row__track" id="bestsellers-row">
        <?php while ($product = mysqli_fetch_assoc($bestsellers)): include 'includes/product_card.php'; endwhile; ?>
    </div>
    <button class="scroll-row__nav scroll-row__nav--next" onclick="scrollRow('bestsellers-row','right')" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
</section>
<?php endif; ?>

<!-- Trending -->
<?php if ($trending && mysqli_num_rows($trending) > 0): ?>
<section class="section">
    <div class="section__header">
        <div><h2 class="section__title">Trending Now</h2><p class="section__subtitle">What's hot in tech right now</p></div>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($trending)): include 'includes/product_card.php'; endwhile; ?>
    </div>
</section>
<?php endif; ?>

<!-- Brand Showcase -->
<?php if ($brands && mysqli_num_rows($brands) > 0): ?>
<section class="section">
    <div class="section__header"><h2 class="section__title">Trusted Brands</h2></div>
    <div class="brands-row">
        <?php while ($b = mysqli_fetch_assoc($brands)): ?>
            <span class="brand-item"><?php echo e($b['brand']); ?></span>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<!-- New Arrivals -->
<?php if ($new_arrivals && mysqli_num_rows($new_arrivals) > 0): ?>
<section class="section scroll-row">
    <div class="section__header">
        <div><h2 class="section__title">New Arrivals</h2><p class="section__subtitle">Latest additions to our catalog</p></div>
        <a href="customer/shop.php?sort=newest" class="section__link">View all →</a>
    </div>
    <button class="scroll-row__nav scroll-row__nav--prev" onclick="scrollRow('newarrivals-row','left')" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
    <div class="scroll-row__track" id="newarrivals-row">
        <?php while ($product = mysqli_fetch_assoc($new_arrivals)): include 'includes/product_card.php'; endwhile; ?>
    </div>
    <button class="scroll-row__nav scroll-row__nav--next" onclick="scrollRow('newarrivals-row','right')" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
</section>
<?php endif; ?>

<!-- Testimonials -->
<section class="section">
    <div class="section__header"><h2 class="section__title">What Our Customers Say</h2></div>
    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="testimonial-card__text">"Excellent service and genuine products. My MacBook Air arrived next day in Kathmandu. ByteStore is my go-to for all tech purchases."</div>
            <?php echo renderStars(5); ?>
            <div class="testimonial-card__author">Akraj Customer</div>
            <div class="testimonial-card__role">Verified Buyer — MacBook Air M2</div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-card__text">"Competitive prices and knowledgeable staff. The gaming laptop I bought performs exactly as advertised. Highly recommend for gamers in Nepal."</div>
            <?php echo renderStars(5); ?>
            <div class="testimonial-card__author">Arpan Shopper</div>
            <div class="testimonial-card__role">Verified Buyer — ROG Strix</div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-card__text">"Smooth checkout with eSewa payment. Order tracking kept me updated throughout. Will definitely shop here again for my next phone upgrade."</div>
            <?php echo renderStars(4); ?>
            <div class="testimonial-card__author">Pramisha Shopper</div>
            <div class="testimonial-card__role">Verified Buyer — Samsung Galaxy S24</div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <h2 class="newsletter__title">Stay Updated</h2>
    <p class="newsletter__desc">Get exclusive deals, new arrivals, and tech news delivered to your inbox.</p>
    <form id="newsletter-form" class="newsletter__form">
        <input type="email" name="email" class="newsletter__input" placeholder="Enter your email" required aria-label="Email for newsletter">
        <button type="submit" class="newsletter__btn">Subscribe</button>
    </form>
</section>

<?php if (!isset($_SESSION['customer_id'])): ?>
<div class="card card--glass text-center" style="padding:3rem;">
    <h2 style="margin-bottom:0.5rem;">Ready to upgrade your tech?</h2>
    <p class="text-muted" style="margin-bottom:1.5rem;">Join thousands of satisfied customers across Nepal</p>
    <a href="customer/register.php" class="btn btn--primary btn--lg">Create Free Account</a>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
