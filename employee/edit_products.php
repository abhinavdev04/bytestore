<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkEmployeeLogin();

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_price = floatval($_POST['product_price']);
    $product_stock = intval($_POST['product_stock']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
    $original_price = isset($_POST['original_price']) && $_POST['original_price'] !== '' ? floatval($_POST['original_price']) : 'NULL';
    $discount_percent = isset($_POST['discount_percent']) && $_POST['discount_percent'] !== '' ? floatval($_POST['discount_percent']) : 'NULL';
    $is_sale_active = isset($_POST['is_sale_active']) ? 1 : 0;
    $sale_start = !empty($_POST['sale_start_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['sale_start_date']) . "'" : 'NULL';
    $sale_end = !empty($_POST['sale_end_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['sale_end_date']) . "'" : 'NULL';
    $orig_sql = $original_price === 'NULL' ? 'NULL' : $original_price;
    $disc_sql = $discount_percent === 'NULL' ? 'NULL' : $discount_percent;
    
    $sql = "UPDATE product SET 
            product_name = '$product_name',
            product_description = '$product_description',
            product_price = $product_price,
            original_price = $orig_sql,
            discount_percent = $disc_sql,
            is_sale_active = $is_sale_active,
            sale_start_date = $sale_start,
            sale_end_date = $sale_end,
            product_stock = $product_stock,
            category_id = " . ($category_id ? $category_id : 'NULL') . "
            WHERE product_id = $product_id";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Product updated successfully!";
    } else {
        $error = "Error updating product: " . mysqli_error($conn);
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $sql = "DELETE FROM product WHERE product_id = $product_id";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Product deleted successfully!";
    } else {
        $error = "Error deleting product: " . mysqli_error($conn);
    }
}

// Fetch categories for dropdown
$categories_sql = "SELECT * FROM category ORDER BY category_name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat;
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_category = isset($_GET['category']) ? intval($_GET['category']) : 0;

$where_clauses = [];
if ($search) {
    $where_clauses[] = "(p.product_name LIKE '%$search%' OR p.product_description LIKE '%$search%' OR p.brand LIKE '%$search%')";
}
if ($filter_category > 0) {
    $where_clauses[] = "p.category_id = $filter_category";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

$count_sql = "SELECT COUNT(*) as total FROM product p $where_sql";
$count_result = mysqli_query($conn, $count_sql);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $per_page);

$sql = "SELECT p.*, c.category_name
        FROM product p
        LEFT JOIN category c ON p.category_id = c.category_id
        $where_sql
        ORDER BY p.product_id DESC
        LIMIT $offset, $per_page";

$products_result = mysqli_query($conn, $sql);
$page_title = 'Edit Products';
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/employee-panel.css">
<div class="employee-page">
    
    <div class="card">
            <div class="card__header">
                <h1 class="card__title"><i class="fa-solid fa-pen"></i> Edit Products</h1>
                <a href="dashboard.php" class="btn btn--outline btn--sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="employee-stats">
                <div class="employee-stat"><strong><?php echo $total_products; ?></strong><span class="text-muted">Products</span></div>
                <div class="employee-stat"><strong><?php echo count($categories); ?></strong><span class="text-muted">Categories</span></div>
            </div>

            <div class="employee-toolbar">
                <div class="admin-search-bar">
                    <i class="fa-solid fa-search"></i>
                    <input type="search" id="admin-product-search" placeholder="Live search by name, brand, or category...">
                </div>
                <form method="GET" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="search" placeholder="Filter..." value="<?php echo e($search); ?>">
                    <select name="category">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['category_id']; ?>" <?php echo $filter_category == $cat['category_id'] ? 'selected' : ''; ?>><?php echo e($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn--primary btn--sm">Filter</button>
                    <a href="edit_products.php" class="btn btn--outline btn--sm">Clear</a>
                </form>
            </div>

            <div class="employee-product-list">
                <?php if ($products_result && mysqli_num_rows($products_result) > 0): ?>
                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <div class="employee-product-card" data-admin-product-row
                             data-name="<?php echo e($product['product_name']); ?>"
                             data-brand="<?php echo e($product['brand'] ?? ''); ?>"
                             data-category="<?php echo e($product['category_name'] ?? ''); ?>">
                            <!-- Product Image -->
                            <div class="employee-product-card__media">
                                <img src="<?php echo e(productImageUrl($product['product_image_path'])); ?>" 
                                     alt="<?php echo e($product['product_name']); ?>"
                                     onerror="this.src='../assets/images/placeholder.svg'">
                                <?php if ($product['category_name']): ?>
                                    <div style="margin-top: 10px;">
                                        <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Form -->
                            <form method="POST" class="employee-product-card__form" id="product-form-<?php echo (int)$product['product_id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                
                                <div class="employee-form-row">
                                    <label>Name:</label>
                                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>
                                
                                <div class="employee-form-row">
                                    <label>Description:</label>
                                    <textarea name="product_description" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                </div>
                                
                                <div class="employee-form-row">
                                    <label>Price (Rs.):</label>
                                    <input type="number" name="product_price" step="0.01" value="<?php echo $product['product_price']; ?>" required>
                                </div>

                                <div class="employee-form-row">
                                    <label>List Price (Rs.):</label>
                                    <input type="number" name="original_price" step="0.01" value="<?php echo e($product['original_price'] ?? ''); ?>" placeholder="MSRP for discounts">
                                </div>

                                <div class="employee-form-row">
                                    <label>Discount %:</label>
                                    <input type="number" name="discount_percent" step="0.01" min="0" max="90" value="<?php echo e($product['discount_percent'] ?? ''); ?>">
                                </div>

                                <div class="employee-form-row">
                                    <label>Sale Active:</label>
                                    <input type="checkbox" name="is_sale_active" value="1" <?php echo !empty($product['is_sale_active']) ? 'checked' : ''; ?>>
                                </div>

                                <div class="employee-form-row">
                                    <label>Sale Start:</label>
                                    <input type="datetime-local" name="sale_start_date" value="<?php echo !empty($product['sale_start_date']) ? date('Y-m-d\TH:i', strtotime($product['sale_start_date'])) : ''; ?>">
                                </div>

                                <div class="employee-form-row">
                                    <label>Sale End:</label>
                                    <input type="datetime-local" name="sale_end_date" value="<?php echo !empty($product['sale_end_date']) ? date('Y-m-d\TH:i', strtotime($product['sale_end_date'])) : ''; ?>">
                                </div>
                                
                                <div class="employee-form-row">
                                    <label>Stock:</label>
                                    <input type="number" name="product_stock" value="<?php echo $product['product_stock']; ?>" required>
                                </div>
                                
                                <div class="employee-form-row">
                                    <label>Category:</label>
                                    <select name="category_id">
                                        <option value="">No Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" 
                                                    <?php echo $product['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="employee-form-row">
                                    <label></label>
                                    <button type="submit" name="update_product" class="btn btn--success btn--sm"><i class="fa-solid fa-save"></i> Save Changes</button>
                                </div>
                            </form>

                            <div class="employee-product-card__actions">
                                <a href="manage_variants.php?product_id=<?php echo (int)$product['product_id']; ?>" class="btn btn--primary btn--sm"><i class="fa-solid fa-layer-group"></i> Variants</a>
                                <a href="?delete=<?php echo (int)$product['product_id']; ?>" class="btn btn--danger btn--sm" data-confirm="Delete this product permanently?" data-confirm-title="Delete Product"><i class="fa-solid fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; padding: 40px; color: #666;">No products found.</p>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>">Â« Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>">Next Â»</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

