<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkEmployeeLogin();

$success = '';
$error   = '';

if (isset($_POST['add_product'])) {
    $name        = trim($_POST['product_name']        ?? '');
    $description = trim($_POST['product_description'] ?? '');
    $price       = $_POST['product_price']            ?? '';
    $stock       = $_POST['product_stock']            ?? '';

    // ── Server-side validation ─────────────────────────────
    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif (!validateProductName($name)) {
        $error = 'Product name must be between 3 and 200 characters.';
    } elseif (empty($description)) {
        $error = 'Product description is required.';
    } elseif (strlen($description) < 20) {
        $error = 'Description must be at least 20 characters. Please provide more detail.';
    } elseif (strlen($description) > 5000) {
        $error = 'Description is too long (max 5000 characters).';
    } elseif ($price === '' || $price === null) {
        $error = 'Price is required.';
    } elseif (!validateProductPrice($price)) {
        $error = 'Price must be a positive number (e.g. 999.99). Maximum is Rs. 9,999,999.';
    } elseif ($stock === '' || $stock === null) {
        $error = 'Stock quantity is required.';
    } elseif (!validateProductStock($stock)) {
        $error = 'Stock must be a whole number between 0 and 99,999.';
    } elseif (empty($_FILES['product_image']['name'])) {
        $error = 'A product image is required.';
    } else {
        // ── Image upload validation ────────────────────────
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $max_size      = 5 * 1024 * 1024; // 5 MB
        $file          = $_FILES['product_image'];
        $finfo         = finfo_open(FILEINFO_MIME_TYPE);
        $mime          = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_types)) {
            $error = 'Invalid image type. Please upload a JPG, PNG, WebP, or GIF file.';
        } elseif ($file['size'] > $max_size) {
            $error = 'Image is too large. Maximum file size is 5 MB.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Image upload failed. Please try again.';
        } else {
            // ── Save image ─────────────────────────────────
            $upload_dir = '../assets/uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }
            $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename  = uniqid('product_', true) . '.' . strtolower($ext);
            $dest      = $upload_dir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $error = 'Could not save the image. Please check folder permissions.';
            } else {
                // ── Insert product ─────────────────────────
                $nameEsc  = mysqli_real_escape_string($conn, $name);
                $descEsc  = mysqli_real_escape_string($conn, $description);
                $imgPath  = 'assets/uploads/products/' . $filename;
                $priceF   = (float)$price;
                $stockI   = (int)$stock;

                $sql = "INSERT INTO product
                            (product_name, product_description, product_price, product_stock, product_image_path)
                        VALUES
                            ('$nameEsc', '$descEsc', $priceF, $stockI, '$imgPath')";

                if (mysqli_query($conn, $sql)) {
                    $success = 'Product "' . e($name) . '" added successfully!';
                } else {
                    // Remove uploaded file if DB insert fails
                    @unlink($dest);
                    $error = 'Database error. Could not add product. Please try again.';
                }
            }
        }
    }
}

$page_title = 'Add Product';
include '../includes/header.php';
?>

<div class="card">
    <h2>Add New Product</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-primary" style="margin-bottom: 20px;">Back to Dashboard</a>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Product Name <span style="color:red">*</span></label>
            <input type="text" name="product_name" required minlength="3" maxlength="200"
                   value="<?php echo e($_POST['product_name'] ?? ''); ?>">
            <p class="form-hint">3–200 characters</p>
        </div>

        <div class="form-group">
            <label>Description <span style="color:red">*</span></label>
            <textarea name="product_description" rows="5" required minlength="20" maxlength="5000"><?php echo e($_POST['product_description'] ?? ''); ?></textarea>
            <p class="form-hint">At least 20 characters. Describe the product clearly.</p>
        </div>

        <div class="form-group">
            <label>Price (Rs.) <span style="color:red">*</span></label>
            <input type="number" name="product_price" step="0.01" min="0.01" max="9999999"
                   required value="<?php echo e($_POST['product_price'] ?? ''); ?>">
            <p class="form-hint">Enter price in Nepali Rupees (e.g. 1299.00)</p>
        </div>

        <div class="form-group">
            <label>Stock Quantity <span style="color:red">*</span></label>
            <input type="number" name="product_stock" min="0" max="99999"
                   required value="<?php echo e($_POST['product_stock'] ?? ''); ?>">
            <p class="form-hint">Enter 0 if out of stock</p>
        </div>

        <div class="form-group">
            <label>Product Image <span style="color:red">*</span></label>
            <input type="file" name="product_image" accept="image/jpeg,image/png,image/webp,image/gif" required>
            <p class="form-hint">JPG, PNG, WebP, or GIF — max 5 MB</p>
        </div>

        <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>