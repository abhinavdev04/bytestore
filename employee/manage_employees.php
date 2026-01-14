<?php
// -------------------------------------------------------------
// Manage Employees (Staff)
// - Staff can add and delete employee accounts
// - Uses regex to validate email and password strength
// - Passwords stored in plain text (academic only)
// -------------------------------------------------------------


session_start();
require '../config/config.php';
require '../includes/auth.php';

checkEmployeeLogin();

$success = '';
$error   = '';

$emailPattern    = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
$passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/";


if (isset($_POST['add_employee'])) {

    $name     = trim($_POST['employee_name'] ?? '');
    $email    = trim($_POST['employee_email'] ?? '');
    $password = $_POST['employee_password'] ?? '';
    $shopName = trim($_POST['shop_name'] ?? '');

    if (!preg_match($emailPattern, $email)) {
        $error = "Invalid email format!";
    } elseif (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, digit, and special character.";
    } else {

        $nameEsc     = mysqli_real_escape_string($conn, $name);
        $emailEsc    = mysqli_real_escape_string($conn, $email);
        $shopNameEsc = mysqli_real_escape_string($conn, $shopName);

       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkRes = mysqli_query($conn, "SELECT * FROM employee WHERE employee_email='$emailEsc'");

        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $error = "Email already exists!";
        } else {

            $insertSql = "
                INSERT INTO employee (employee_name, employee_email, employee_password, shop_name)
                VALUES ('$nameEsc', '$emailEsc', '$hashedPassword', '$shopNameEsc')
            ";

            if (mysqli_query($conn, $insertSql)) {
                $success = "Employee added successfully!";
            } else {
                $error = "Failed to add employee.";
            }
        }
    }
}

// Handle delete action for employee
if (isset($_GET['delete'])) {
    $employee_id = (int)$_GET['delete'];
    

    if (mysqli_query($conn, "DELETE FROM employee WHERE employee_id=$employee_id")) {
        $success = "Employee deleted successfully!";
    } else {
        $error = "Failed to delete employee.";
    }
}

// Get all employees
$result = mysqli_query($conn, "SELECT * FROM employee ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="card">
        <h2>Manage Employees</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <!-- Add Employee Form -->
        <h3>Add New Employee</h3>
        <form method="POST" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="employee_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="employee_email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="employee_password" required>
                    <small>
                        Must have at least 8 characters, including uppercase, lowercase, number, and special character.
                    </small>
                </div>
                <div class="form-group">
                    <label>Shop Name</label>
                    <input type="text" name="shop_name" required>
                </div>
            </div>
            <button type="submit" name="add_employee" class="btn btn-success">Add Employee</button>
        </form>
        
        <!-- Employee list -->
        <h3>Employee List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Shop Name</th>
                    <th>Registered</th>
                    <?php 
                        if ($_SESSION['employee_id'] == 1): ?>
                        <th>Action</th>
                     <?php endif; ?>
                </tr>
            </thead>
            <tbody>
               <?php while ($employee = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $employee['employee_id']; ?></td>
                        <td><?php echo $employee['employee_name']; ?></td>
                        <td><?php echo $employee['employee_email']; ?></td>
                        <td><?php echo $employee['shop_name']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($employee['created_at'])); ?></td>
                        <td>
                            <?php 
                            // Only show Delete button if logged-in employee is super admin
                            
                            if ($_SESSION['employee_id'] == 1): ?>
                                <a href="manage_employees.php?delete=<?php echo $employee['employee_id']; ?>" 
                                class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this employee?')">
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
</body>
</html>


