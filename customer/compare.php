<?php
session_start();
require '../config/config.php';
require '../includes/functions.php';

$compare_ids = getCompareList();
$products = [];

if (!empty($compare_ids)) {
    $ids = implode(',', array_map('intval', $compare_ids));
    $result = mysqli_query($conn, "SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id WHERE p.product_id IN ($ids)");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[$row['product_id']] = $row;
        }
    }
    $ordered = [];
    foreach ($compare_ids as $id) {
        if (isset($products[$id])) $ordered[] = $products[$id];
    }
    $products = $ordered;
}

$all_specs = [];
foreach ($compare_ids as $pid) {
    $spec_res = mysqli_query($conn, "SELECT spec_key, spec_value FROM product_spec WHERE product_id = " . (int)$pid);
    $specs = [];
    if ($spec_res) while ($s = mysqli_fetch_assoc($spec_res)) $specs[$s['spec_key']] = $s['spec_value'];
    $all_specs[$pid] = $specs;
}

$spec_keys = [];
foreach ($all_specs as $specs) {
    foreach (array_keys($specs) as $k) {
        if (!in_array($k, $spec_keys)) $spec_keys[] = $k;
    }
}

$page_title = 'Compare Products';
include '../includes/header.php';
echo renderBreadcrumbs([['label' => 'Home', 'url' => '../index.php'], ['label' => 'Compare', 'url' => '']]);
?>

<div class="card">
    <div class="card__header">
        <h1 class="card__title">Compare Products</h1>
        <?php if (count($products) > 0): ?>
            <button type="button" class="btn btn--outline btn--sm" id="clear-compare-btn"><i class="fa-solid fa-trash"></i> Clear All</button>
        <?php endif; ?>
    </div>

    <?php if (count($products) >= 2): ?>
        <div class="compare-table">
            <table>
                <thead>
                    <tr>
                        <th>Feature</th>
                        <?php foreach ($products as $p): ?>
                            <th class="compare-product">
                                <button type="button" class="btn btn--ghost btn--sm compare-remove-btn" data-id="<?php echo (int)$p['product_id']; ?>" style="float:right;"><i class="fa-solid fa-xmark"></i></button>
                                <img src="<?php echo e(productImageUrl($p['product_image_path'])); ?>" alt="<?php echo e($p['product_name']); ?>" onerror="this.src='../assets/images/placeholder.svg'">
                                <strong><?php echo e($p['product_name']); ?></strong>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Price</td><?php foreach ($products as $p): $cp = getProductPricing($p); ?><td><?php echo renderProductPricingHtml($cp, 'card'); ?></td><?php endforeach; ?></tr>
                    <tr><td>Brand</td><?php foreach ($products as $p): ?><td><?php echo e($p['brand'] ?? '—'); ?></td><?php endforeach; ?></tr>
                    <tr><td>Category</td><?php foreach ($products as $p): ?><td><?php echo e($p['category_name'] ?? '—'); ?></td><?php endforeach; ?></tr>
                    <tr><td>Rating</td><?php foreach ($products as $p): ?><td><?php echo renderStars($p['rating_avg'] ?? 0); ?> (<?php echo (int)($p['rating_count'] ?? 0); ?>)</td><?php endforeach; ?></tr>
                    <tr><td>Stock</td><?php foreach ($products as $p): ?><td><?php echo (int)$p['product_stock'] > 0 ? 'In Stock (' . (int)$p['product_stock'] . ')' : 'Out of Stock'; ?></td><?php endforeach; ?></tr>
                    <?php foreach ($spec_keys as $key): ?>
                        <tr><td><?php echo e($key); ?></td><?php foreach ($products as $p): ?><td><?php echo e($all_specs[$p['product_id']][$key] ?? '—'); ?></td><?php endforeach; ?></tr>
                    <?php endforeach; ?>
                    <tr><td>Action</td><?php foreach ($products as $p): ?><td><a href="product.php?id=<?php echo (int)$p['product_id']; ?>" class="btn btn--primary btn--sm">View</a></td><?php endforeach; ?></tr>
                </tbody>
            </table>
        </div>
    <?php elseif (count($products) === 1): ?>
        <div class="alert alert-info">Add at least one more product to compare. Click the compare icon on product cards.</div>
        <div class="product-grid" style="max-width:300px;">
            <?php $product = $products[0]; include '../includes/product_card.php'; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <h3 class="empty-state__title">No products to compare</h3>
            <p class="empty-state__desc">Add up to 4 products using the compare icon on product cards.</p>
            <a href="shop.php" class="btn btn--primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<script>
const COMPARE_API = '../api/compare.php';
async function compareAction(body) {
    const res = await fetch(COMPARE_API, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
    return res.json();
}
document.getElementById('clear-compare-btn')?.addEventListener('click', async () => {
    const ok = await bsConfirm('Remove all products from your compare list?', { title: 'Clear Compare List', confirmText: 'Clear All', confirmClass: 'btn--danger' });
    if (!ok) return;
    const data = await compareAction({ action: 'clear' });
    if (data.success) { showToast(data.message, 'success'); location.reload(); }
    else showToast(data.message || 'Error', 'error');
});
document.querySelectorAll('.compare-remove-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const data = await compareAction({ product_id: parseInt(btn.dataset.id), action: 'remove' });
        if (data.success) location.reload();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
