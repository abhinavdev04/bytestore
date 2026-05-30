<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$category_id = sanitizeInt($_GET['category'] ?? 0);
$brand_filter = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';
$min_price = sanitizeInt($_GET['min_price'] ?? 0);
$max_price = sanitizeInt($_GET['max_price'] ?? 0);
$min_rating = sanitizeInt($_GET['min_rating'] ?? 0);
$in_stock = isset($_GET['in_stock']);
$sort = $_GET['sort'] ?? 'newest';

$conditions = [];
if ($search !== '') $conditions[] = "(p.product_name LIKE '%$search%' OR p.product_description LIKE '%$search%' OR p.brand LIKE '%$search%')";
if ($category_id > 0) $conditions[] = "p.category_id = $category_id";
if ($brand_filter !== '') $conditions[] = "p.brand = '$brand_filter'";
if ($min_price > 0) $conditions[] = "p.product_price >= $min_price";
if ($max_price > 0) $conditions[] = "p.product_price <= $max_price";
if ($min_rating > 0) $conditions[] = "p.rating_avg >= $min_rating";
if ($in_stock) $conditions[] = "p.product_stock > 0";

$order_by = 'p.created_at DESC';
switch ($sort) {
    case 'price_low': $order_by = 'p.product_price ASC'; break;
    case 'price_high': $order_by = 'p.product_price DESC'; break;
    case 'rating': $order_by = 'p.rating_avg DESC'; break;
    case 'bestseller': $order_by = 'total_sold DESC'; break;
    case 'newest': $order_by = 'p.created_at DESC'; break;
}

$join_orders = ($sort === 'bestseller') ? " LEFT JOIN order_items oi ON p.product_id = oi.product_id" : "";
$group_by = ($sort === 'bestseller') ? " GROUP BY p.product_id" : "";
$select_extra = ($sort === 'bestseller') ? ", COALESCE(SUM(oi.quantity),0) as total_sold" : "";

$sql = "SELECT p.* $select_extra FROM product p $join_orders";
if (!empty($conditions)) $sql .= " WHERE " . implode(' AND ', $conditions);
$sql .= $group_by . " ORDER BY $order_by";

$result = mysqli_query($conn, $sql);

$category_name = '';
if ($category_id > 0) {
    $cat_res = mysqli_query($conn, "SELECT category_name FROM category WHERE category_id = $category_id LIMIT 1");
    if ($cat_res && $row = mysqli_fetch_assoc($cat_res)) $category_name = $row['category_name'];
}

$categories = getCategories($conn);
$brands_res = mysqli_query($conn, "SELECT DISTINCT brand FROM product WHERE brand IS NOT NULL AND brand != '' ORDER BY brand");
$brands = [];
if ($brands_res) while ($b = mysqli_fetch_assoc($brands_res)) $brands[] = $b['brand'];

$page_title = $category_name ? $category_name : 'Shop';
include '../includes/header.php';
$breadcrumbs = [['label' => 'Home', 'url' => '../index.php'], ['label' => 'Shop', 'url' => 'shop.php']];
if ($category_name) $breadcrumbs[] = ['label' => $category_name, 'url' => ''];
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="shop-layout">
    <aside class="shop-filters">
        <h3 style="margin-bottom:1.25rem;">Filters</h3>
        <form method="GET" id="filter-form">
            <?php if ($search): ?><input type="hidden" name="search" value="<?php echo e($search); ?>"><?php endif; ?>

            <div class="filter-group">
                <div class="filter-group__title">Category</div>
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo (int)$cat['category_id']; ?>" <?php echo $category_id == $cat['category_id'] ? 'selected' : ''; ?>><?php echo e($cat['category_name']); ?> (<?php echo (int)$cat['product_count']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($brands)): ?>
            <div class="filter-group">
                <div class="filter-group__title">Brand</div>
                <?php foreach ($brands as $brand): ?>
                    <label class="filter-option">
                        <input type="radio" name="brand" value="<?php echo e($brand); ?>" <?php echo $brand_filter === $brand ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <?php echo e($brand); ?>
                    </label>
                <?php endforeach; ?>
                <?php if ($brand_filter): ?><a href="?" style="font-size:0.8rem;">Clear brand</a><?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="filter-group">
                <div class="filter-group__title">Price Range (Rs.)</div>
                <div class="form-row" style="grid-template-columns:1fr 1fr;">
                    <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price ?: ''; ?>" class="form-input">
                    <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price ?: ''; ?>" class="form-input">
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-group__title">Minimum Rating</div>
                <?php for ($r = 4; $r >= 1; $r--): ?>
                    <label class="filter-option">
                        <input type="radio" name="min_rating" value="<?php echo $r; ?>" <?php echo $min_rating == $r ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <?php echo renderStars($r); ?> & up
                    </label>
                <?php endfor; ?>
            </div>

            <div class="filter-group">
                <label class="filter-option">
                    <input type="checkbox" name="in_stock" <?php echo $in_stock ? 'checked' : ''; ?> onchange="this.form.submit()">
                    In Stock Only
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Apply Filters</button>
            <a href="shop.php" class="btn btn--ghost btn--block" style="margin-top:0.5rem;">Clear All</a>
        </form>
    </aside>

    <div>
        <div class="card" style="padding:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <h1 style="font-size:1.5rem;"><?php echo $category_name ? e($category_name) : 'All Products'; ?></h1>
                    <p class="text-muted"><?php echo $result ? mysqli_num_rows($result) : 0; ?> products found</p>
                </div>
                <div style="display:flex;gap:0.75rem;align-items:center;">
                    <form method="GET" style="display:flex;gap:0.5rem;">
                        <?php foreach ($_GET as $k => $v): if ($k !== 'search' && $k !== 'sort'): ?>
                            <input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
                        <?php endif; endforeach; ?>
                        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search..." class="form-input" style="width:200px;">
                        <button type="submit" class="btn btn--primary btn--sm">Search</button>
                    </form>
                    <select onchange="location.href=this.value" class="form-select" style="width:auto;">
                        <?php
                        $sorts = ['newest'=>'Newest','price_low'=>'Price: Low to High','price_high'=>'Price: High to Low','rating'=>'Top Rated','bestseller'=>'Best Sellers'];
                        $qs = $_GET; unset($qs['sort']);
                        $base_qs = http_build_query($qs);
                        foreach ($sorts as $val => $label):
                            $url = 'shop.php?' . ($base_qs ? $base_qs . '&' : '') . 'sort=' . $val;
                        ?>
                            <option value="<?php echo e($url); ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

    <div class="shop-products-area">
        <div class="card" style="padding:1rem;margin-bottom:1rem;">
            <div class="form-group" style="margin:0;">
                <label for="shop-live-search"><i class="fa-solid fa-magnifying-glass"></i> Live Search</label>
                <input type="search" id="shop-live-search" class="form-input" placeholder="Type to filter products instantly..." value="<?php echo e($search); ?>">
            </div>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <div class="product-grid shop-live-results" id="shop-product-grid">
                <?php while ($product = mysqli_fetch_assoc($result)): include '../includes/product_card.php'; endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3 class="empty-state__title">No products found</h3>
                <p class="empty-state__desc">Try adjusting your filters or search terms.</p>
                <a href="shop.php" class="btn btn--primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
