<?php
// -------------------------------------------------------------
// Delete Products Page (Staff)
// - Allows staff to delete products from catalog
// - No logging table used, only direct delete
// -------------------------------------------------------------

session_start();
require '../config/config.php';
require '../includes/auth.php';

// Require staff login for this page
checkEmployeeLogin();

$employee_id = $_SESSION['employee_id'];
$success = '';
$error   = '';

// Handle delete action from GET parameter
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];

    // Directly delete the product (no audit log)
    if (mysqli_query($conn, "DELETE FROM product WHERE product_id=$product_id")) {
        $success = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }
}

// Load all products to display in table
$result = mysqli_query($conn, "SELECT * FROM product ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Products - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>Delete Products</h2>
        
        <!-- Success and error messages -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <!-- Product table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td>
                            <img src="../<?php echo $product['product_image_path']; ?>" 
                                 alt="<?php echo $product['product_name']; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 onerror="this.src='../assets/images/placeholder.jpg'">
                        </td>
                        <td><?php echo $product['product_name']; ?></td>
                        <td>Rs. <?php echo number_format($product['product_price'], 2); ?></td>
                        <td><?php echo $product['product_stock']; ?></td>
                        <td>
                            <a href="delete_products.php?delete=<?php echo $product['product_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone!')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

