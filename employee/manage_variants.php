<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkEmployeeLogin();

$success = '';
$error = '';
$product_id = sanitizeInt($_GET['product_id'] ?? $_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    header('Location: edit_products.php');
    exit;
}

$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE product_id = $product_id LIMIT 1"));
if (!$product) {
    header('Location: edit_products.php');
    exit;
}

$upload_dir = '../assets/uploads/variants/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_variant'])) {
        $name = mysqli_real_escape_string($conn, trim($_POST['variant_name']));
        $type = mysqli_real_escape_string($conn, trim($_POST['variant_type'] ?? 'Configuration'));
        $price = (float)$_POST['variant_price'];
        $stock = (int)$_POST['variant_stock'];
        $sku = mysqli_real_escape_string($conn, trim($_POST['variant_sku'] ?? ''));
        $img_path = 'NULL';
        if (!empty($_FILES['variant_image']['name']) && $_FILES['variant_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['variant_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $fname = 'variant_' . $product_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($_FILES['variant_image']['tmp_name'], $upload_dir . $fname)) {
                    $img_path = "'" . mysqli_real_escape_string($conn, 'assets/uploads/variants/' . $fname) . "'";
                }
            }
        }
        $sql = "INSERT INTO product_variant (product_id, variant_name, variant_type, variant_price, variant_stock, variant_sku, variant_image_path)
                VALUES ($product_id, '$name', '$type', $price, $stock, '$sku', $img_path)";
        if (mysqli_query($conn, $sql)) $success = 'Variant added.';
        else $error = mysqli_error($conn);
    }

    if (isset($_POST['update_variant'])) {
        $vid = (int)$_POST['variant_id'];
        $name = mysqli_real_escape_string($conn, trim($_POST['variant_name']));
        $type = mysqli_real_escape_string($conn, trim($_POST['variant_type'] ?? 'Configuration'));
        $price = (float)$_POST['variant_price'];
        $stock = (int)$_POST['variant_stock'];
        $sku = mysqli_real_escape_string($conn, trim($_POST['variant_sku'] ?? ''));
        $img_sql = '';
        if (!empty($_FILES['variant_image']['name']) && $_FILES['variant_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['variant_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $fname = 'variant_' . $product_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($_FILES['variant_image']['tmp_name'], $upload_dir . $fname)) {
                    $path = mysqli_real_escape_string($conn, 'assets/uploads/variants/' . $fname);
                    $img_sql = ", variant_image_path = '$path'";
                }
            }
        }
        $sql = "UPDATE product_variant SET variant_name='$name', variant_type='$type', variant_price=$price, variant_stock=$stock, variant_sku='$sku' $img_sql WHERE variant_id=$vid AND product_id=$product_id";
        if (mysqli_query($conn, $sql)) $success = 'Variant updated.';
        else $error = mysqli_error($conn);
    }

    if (isset($_POST['delete_variant'])) {
        $vid = (int)$_POST['variant_id'];
        mysqli_query($conn, "DELETE FROM product_variant WHERE variant_id=$vid AND product_id=$product_id");
        $success = 'Variant deleted.';
    }
}

$variants = mysqli_query($conn, "SELECT * FROM product_variant WHERE product_id = $product_id ORDER BY variant_type, variant_name");

$page_title = 'Manage Variants';
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/employee-panel.css">

<div class="employee-page">
    <div class="card">
        <div class="card__header">
            <div>
                <h1 class="card__title">Variants: <?php echo e($product['product_name']); ?></h1>
                <p class="text-muted">Product #<?php echo $product_id; ?></p>
            </div>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <a href="edit_products.php" class="btn btn--outline btn--sm"><i class="fa-solid fa-arrow-left"></i> Products</a>
                <a href="../customer/product.php?id=<?php echo $product_id; ?>" class="btn btn--outline btn--sm" target="_blank">View Storefront</a>
            </div>
        </div>
        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
    </div>

    <div class="card">
        <h2>Add New Variant</h2>
        <form method="POST" enctype="multipart/form-data" class="variant-form-grid">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="add_variant" value="1">
            <div class="form-group"><label>Type (e.g. Color, Storage)</label><input type="text" name="variant_type" placeholder="Color" required></div>
            <div class="form-group"><label>Variant Name</label><input type="text" name="variant_name" placeholder="Black / 256GB" required></div>
            <div class="form-group"><label>Price (Rs.)</label><input type="number" name="variant_price" step="0.01" value="<?php echo e($product['product_price']); ?>" required></div>
            <div class="form-group"><label>Stock</label><input type="number" name="variant_stock" min="0" value="10" required></div>
            <div class="form-group"><label>SKU</label><input type="text" name="variant_sku"></div>
            <div class="form-group"><label>Variant Image</label><input type="file" name="variant_image" accept="image/*"></div>
            <div class="form-group form-group--full"><button type="submit" class="btn btn--success"><i class="fa-solid fa-plus"></i> Add Variant</button></div>
        </form>
    </div>

    <div class="card">
        <h2>Existing Variants</h2>
        <?php if ($variants && mysqli_num_rows($variants) > 0): ?>
            <div class="variant-admin-list">
            <?php while ($v = mysqli_fetch_assoc($variants)):
                $vimg = variantImageUrl($v['variant_image_path'] ?? '', $product['product_image_path']);
            ?>
                <div class="variant-admin-card">
                    <div class="variant-admin-card__preview">
                        <img src="<?php echo e($vimg); ?>" alt="<?php echo e($v['variant_name']); ?>" onerror="this.src='../assets/images/placeholder.svg'">
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="variant-form-grid">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="variant_id" value="<?php echo (int)$v['variant_id']; ?>">
                        <div class="form-group"><label>Type</label><input type="text" name="variant_type" value="<?php echo e($v['variant_type'] ?? 'Configuration'); ?>" required></div>
                        <div class="form-group"><label>Name</label><input type="text" name="variant_name" value="<?php echo e($v['variant_name']); ?>" required></div>
                        <div class="form-group"><label>Price</label><input type="number" name="variant_price" step="0.01" value="<?php echo e($v['variant_price']); ?>" required></div>
                        <div class="form-group"><label>Stock</label><input type="number" name="variant_stock" value="<?php echo (int)$v['variant_stock']; ?>" required></div>
                        <div class="form-group"><label>SKU</label><input type="text" name="variant_sku" value="<?php echo e($v['variant_sku'] ?? ''); ?>"></div>
                        <div class="form-group"><label>Replace Image</label><input type="file" name="variant_image" accept="image/*"></div>
                        <div class="form-group form-group--full variant-admin-card__actions">
                            <button type="submit" name="update_variant" value="1" class="btn btn--primary btn--sm"><i class="fa-solid fa-save"></i> Save</button>
                            <button type="submit" name="delete_variant" value="1" class="btn btn--danger btn--sm" data-confirm="Delete this variant?" data-confirm-title="Delete Variant"><i class="fa-solid fa-trash"></i> Delete</button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No variants yet. Add color, storage, or RAM options above.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
