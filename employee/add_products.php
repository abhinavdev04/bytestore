<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/product_handler.php';
require '../includes/product_form.php';

checkEmployeeLogin();

// Handle form submission and get messages
$result = handleProductAction($conn, 'employee', $_SESSION['employee_id']);
$success = $result['success'];
$error = $result['error'];
$page_title = 'Add Product';
require '../includes/functions.php';
include '../includes/header.php';
?>

    <div class="card">
        <h2>Add New Product</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary" style="margin-bottom: 20px;">Back to Dashboard</a>

        <!-- Product Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="product_description" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label>Price</label>
                <input type="number" name="product_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="product_stock" min="0" required>
            </div>

            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" accept="image/*" required>
            </div>

            <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
