<?php
// -------------------------------------------------------------
// Manage Customers (Staff)
// - Staff can view and delete customers
// - No separate admin role
// - No logging tables used
// -------------------------------------------------------------

session_start();
require '../config/config.php';
require '../includes/auth.php';

// Require staff login
checkEmployeeLogin();

$success = '';
$error   = '';

// Handle delete action
if (isset($_GET['delete'])) {
    $customer_id = (int)$_GET['delete'];

    // Directly delete from customer table (no audit log)
    if (mysqli_query($conn, "DELETE FROM customer WHERE customer_id=$customer_id")) {
        $success = "Customer deleted successfully!";
    } else {
        $error = "Failed to delete customer.";
    }
}

// Get all customers
$result = mysqli_query($conn, "SELECT * FROM customer ORDER BY created_at DESC");
$page_title = 'Manage Customers';
require '../includes/functions.php';
include '../includes/header.php';
?>
    
    <div class="card">
        <h2>Manage Customers</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Registered</th>
                    <?php 
                        if ($_SESSION['employee_id'] == 1): ?>
                        <th>Action</th>
                     <?php endif; ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($customer = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $customer['customer_id']; ?></td>
                        <td><?php echo $customer['customer_name']; ?></td>
                        <td><?php echo $customer['customer_email']; ?></td>
                        <td><?php echo substr($customer['customer_address'], 0, 50); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($customer['created_at'])); ?></td>
                        <td>
                            <?php 
                            if ($_SESSION['employee_id'] == 1): ?>
                                <a href="manage_customers.php?delete=<?php echo $customer['customer_id']; ?>" 
                               class="btn btn-danger" 
                               data-confirm="Are you sure you want to delete this customer?"
                               data-confirm-title="Delete Customer">
                               Delete
                            </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php include '../includes/footer.php'; ?>


