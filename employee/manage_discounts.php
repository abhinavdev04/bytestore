<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkEmployeeLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bulk_discount'])) {
        $pct = max(0, min(90, (float)$_POST['bulk_percent']));
        $start = !empty($_POST['bulk_start']) ? "'" . mysqli_real_escape_string($conn, $_POST['bulk_start']) . "'" : 'NULL';
        $end = !empty($_POST['bulk_end']) ? "'" . mysqli_real_escape_string($conn, $_POST['bulk_end']) . "'" : 'NULL';
        $active = isset($_POST['bulk_active']) ? 1 : 0;
        $scope = $_POST['bulk_scope'] ?? 'all';

        $where = '1=1';
        if ($scope === 'category' && !empty($_POST['bulk_category_id'])) {
            $where = 'category_id = ' . (int)$_POST['bulk_category_id'];
        } elseif ($scope === 'selected' && !empty($_POST['product_ids']) && is_array($_POST['product_ids'])) {
            $ids = implode(',', array_map('intval', $_POST['product_ids']));
            $where = "product_id IN ($ids)";
        }

        $sql = "UPDATE product SET
            discount_percent = $pct,
            is_sale_active = $active,
            sale_start_date = $start,
            sale_end_date = $end,
            original_price = CASE WHEN original_price IS NULL OR original_price = 0 THEN ROUND(product_price * 1.15, 2) ELSE original_price END
            WHERE $where";
        if (mysqli_query($conn, $sql)) {
            $success = 'Bulk discount applied successfully.';
        } else {
            $error = 'Bulk update failed: ' . mysqli_error($conn);
        }
    }

    if (isset($_POST['toggle_sale'])) {
        $pid = (int)$_POST['product_id'];
        $active = (int)$_POST['is_active'];
        mysqli_query($conn, "UPDATE product SET is_sale_active = $active WHERE product_id = $pid");
        $success = 'Sale status updated.';
    }
}

expireProductSales($conn);
$products = mysqli_query($conn, "SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id ORDER BY p.product_name");
$categories = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name");

$page_title = 'Manage Discounts';
include '../includes/header.php';
?>

<div class="card">
    <h1><i class="fa-solid fa-tags"></i> Discount & Sale Management</h1>
    <p class="text-muted">Set sale percentages, schedules, and bulk discounts. Expired sales are disabled automatically.</p>
</div>

<?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Bulk Discount</h2>
    <form method="POST" class="discount-bulk-form" id="bulkForm">
        <input type="hidden" name="bulk_discount" value="1">
        <div class="form-group">
            <label>Discount Percentage</label>
            <input type="number" name="bulk_percent" min="1" max="90" step="0.5" value="15" required>
        </div>
        <div class="form-group">
            <label>Apply To</label>
            <select name="bulk_scope" id="bulkScope">
                <option value="all">All Products</option>
                <option value="category">By Category</option>
                <option value="selected">Selected Products Only</option>
            </select>
        </div>
        <div class="form-group" id="bulkCategoryWrap" style="display:none;">
            <label>Category</label>
            <select name="bulk_category_id">
                <?php if ($categories) while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo (int)$c['category_id']; ?>"><?php echo e($c['category_name']); ?></option>
                <?php endwhile; mysqli_data_seek($categories, 0); ?>
            </select>
        </div>
        <div class="form-group">
            <label>Sale Start</label>
            <input type="datetime-local" name="bulk_start">
        </div>
        <div class="form-group">
            <label>Sale End</label>
            <input type="datetime-local" name="bulk_end">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="bulk_active" value="1" checked> Enable sale immediately</label>
        </div>
        <button type="submit" class="btn btn--primary" data-confirm="Apply bulk discount to selected products?" data-confirm-title="Apply Bulk Discount">Apply Bulk Discount</button>
    </form>
</div>

<div class="card">
    <div class="card__header">
        <h2>Active & Scheduled Sales</h2>
        <a href="dashboard.php" class="btn btn--outline btn--sm">Back to Dashboard</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Product</th>
                <th>Category</th>
                <th>List Price</th>
                <th>Sale Price</th>
                <th>Discount</th>
                <th>Status</th>
                <th>Ends</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($products) while ($p = mysqli_fetch_assoc($products)):
            $pr = getProductPricing($p);
        ?>
            <tr>
                <td><input type="checkbox" form="bulkForm" name="product_ids[]" value="<?php echo (int)$p['product_id']; ?>"></td>
                <td><?php echo e($p['product_name']); ?></td>
                <td><?php echo e($p['category_name'] ?? '—'); ?></td>
                <td><?php echo $pr['original'] > 0 ? formatPrice($pr['original']) : '—'; ?></td>
                <td><?php echo formatPrice($pr['price']); ?></td>
                <td><?php echo $pr['discount_percent'] ? $pr['discount_percent'] . '%' : '—'; ?></td>
                <td>
                    <?php if ($pr['on_sale']): ?>
                        <span class="status-badge status-delivered">Active</span>
                    <?php elseif (!empty($p['is_sale_active'])): ?>
                        <span class="status-badge status-pending">Scheduled/Inactive</span>
                    <?php else: ?>
                        <span class="status-badge">None</span>
                    <?php endif; ?>
                </td>
                <td><?php echo !empty($p['sale_end_date']) ? date('M j, Y', strtotime($p['sale_end_date'])) : '—'; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="toggle_sale" value="1">
                        <input type="hidden" name="product_id" value="<?php echo (int)$p['product_id']; ?>">
                        <input type="hidden" name="is_active" value="<?php echo $pr['on_sale'] || !empty($p['is_sale_active']) ? 0 : 1; ?>">
                        <button type="submit" class="btn btn--sm btn--outline"><?php echo ($pr['on_sale'] || !empty($p['is_sale_active'])) ? 'Disable' : 'Enable'; ?></button>
                    </form>
                    <a href="edit_products.php?search=<?php echo urlencode($p['product_name']); ?>" class="btn btn--sm btn--primary">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('bulkScope')?.addEventListener('change', function () {
    document.getElementById('bulkCategoryWrap').style.display = this.value === 'category' ? '' : 'none';
});
</script>

<?php include '../includes/footer.php'; ?>
