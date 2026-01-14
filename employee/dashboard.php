<?php
// -------------------------------------------------------------
// Employee (Staff) Dashboard
// - Shows basic statistics
// - Provides quick links to manage products, customers, employees, orders
// -------------------------------------------------------------

session_start();
require '../config/config.php';
require '../includes/auth.php';

// Only staff can access this page
checkEmployeeLogin();

$employee_id = $_SESSION['employee_id'];

// Simple stats queries
$total_products  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM product"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customer"))['count'];
$total_employees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee"))['count'];
$total_orders    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>Staff Dashboard</h2>
        <p>Welcome, <?php echo $_SESSION['employee_name']; ?>!</p>
    </div>
    
    <!-- Simple statistics section -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="number"><?php echo $total_products; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Customers</h3>
            <div class="number"><?php echo $total_customers; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Employees</h3>
            <div class="number"><?php echo $total_employees; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="number"><?php echo $total_orders; ?></div>
        </div>
    </div>
    
    <!-- Quick actions for staff (all former admin features are here) -->
    <div class="card">
        <h3>Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="add_products.php" class="btn btn-success">Add Product</a>
            <a href="edit_products.php" class="btn btn-primary">Edit Products</a>
            <a href="delete_products.php" class="btn btn-danger">Delete Products</a>
            <a href="manage_customers.php" class="btn btn-primary">Manage Customers</a>
            <a href="manage_employees.php" class="btn btn-primary">Manage Employees</a>
            <a href="view_orders.php" class="btn btn-primary">View Orders</a>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

