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
    $phone    = trim($_POST['employee_phone'] ?? '');
    $shopName = trim($_POST['shop_name'] ?? '');

    if (!preg_match($emailPattern, $email)) {
        $error = "Invalid email format!";
    } elseif (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, digit, and special character.";
    } elseif ($phone === '') {
        $error = "Phone number is required.";
    } else {

        $nameEsc     = mysqli_real_escape_string($conn, $name);
        $emailEsc    = mysqli_real_escape_string($conn, $email);
        $phoneEsc    = mysqli_real_escape_string($conn, $phone);
        $shopNameEsc = mysqli_real_escape_string($conn, $shopName);

       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkRes = mysqli_query($conn, "SELECT * FROM employee WHERE employee_email='$emailEsc'");

        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $error = "Email already exists!";
        } else {

            $insertSql = "
                INSERT INTO employee (employee_name, employee_email, employee_password, employee_phone, shop_name)
                VALUES ('$nameEsc', '$emailEsc', '$hashedPassword', '$phoneEsc', '$shopNameEsc')
            ";

            if (mysqli_query($conn, $insertSql)) {
                $success = "Employee added successfully!";
            } else {
                $error = "Failed to add employee.";
            }
        }
    }
}

// Handle update action for employee (only super admin)
if (isset($_POST['update_employee']) && $_SESSION['employee_id'] == 1) {
    $emp_id   = (int)($_POST['employee_id'] ?? 0);
    $name     = trim($_POST['employee_name'] ?? '');
    $email    = trim($_POST['employee_email'] ?? '');
    $phone    = trim($_POST['employee_phone'] ?? '');
    $shopName = trim($_POST['shop_name'] ?? '');

    if (!preg_match($emailPattern, $email)) {
        $error = "Invalid email format!";
    } elseif ($phone === '') {
        $error = "Phone number is required.";
    } else {
        $nameEsc     = mysqli_real_escape_string($conn, $name);
        $emailEsc    = mysqli_real_escape_string($conn, $email);
        $phoneEsc    = mysqli_real_escape_string($conn, $phone);
        $shopNameEsc = mysqli_real_escape_string($conn, $shopName);

        // Ensure email is unique for other employees
        $checkRes = mysqli_query($conn, "SELECT * FROM employee WHERE employee_email='$emailEsc' AND employee_id <> $emp_id");
        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $error = "Another employee already uses this email.";
        } else {
            $updateSql = "
                UPDATE employee
                SET employee_name='$nameEsc',
                    employee_email='$emailEsc',
                    employee_phone='$phoneEsc',
                    shop_name='$shopNameEsc'
                WHERE employee_id=$emp_id
            ";
            if (mysqli_query($conn, $updateSql)) {
                $success = "Employee updated successfully!";
            } else {
                $error = "Failed to update employee.";
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

// Single employee for edit (if requested)
$edit_employee = null;
if (isset($_GET['edit']) && $_SESSION['employee_id'] == 1) {
    $edit_id = (int)$_GET['edit'];
    $edit_res = mysqli_query($conn, "SELECT * FROM employee WHERE employee_id=$edit_id LIMIT 1");
    if ($edit_res && mysqli_num_rows($edit_res) === 1) {
        $edit_employee = mysqli_fetch_assoc($edit_res);
    }
}
$page_title = 'Manage Employees';
require '../includes/functions.php';
include '../includes/header.php';
?>
    
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
                    <label>Phone</label>
                    <input type="text" name="employee_phone" required>
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

        <?php if ($edit_employee): ?>
            <!-- Edit Employee Form -->
            <h3>Edit Employee (ID: <?php echo $edit_employee['employee_id']; ?>)</h3>
            <form method="POST" style="margin-bottom: 30px; padding: 20px; background: #eef6ff; border-radius: 5px;">
                <input type="hidden" name="employee_id" value="<?php echo $edit_employee['employee_id']; ?>">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="employee_name" value="<?php echo htmlspecialchars($edit_employee['employee_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="employee_email" value="<?php echo htmlspecialchars($edit_employee['employee_email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="employee_phone" value="<?php echo htmlspecialchars($edit_employee['employee_phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Shop Name</label>
                        <input type="text" name="shop_name" value="<?php echo htmlspecialchars($edit_employee['shop_name']); ?>" required>
                    </div>
                </div>
                <button type="submit" name="update_employee" class="btn btn-success">Update Employee</button>
            </form>
        <?php endif; ?>
        
        <!-- Employee list -->
        <h3>Employee List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Shop Name</th>
                    <th>Registered</th>
                    <?php 
                        if ($_SESSION['employee_id'] == 1): ?>
                        <th>Actions</th>
                     <?php endif; ?>
                </tr>
            </thead>
            <tbody>
               <?php while ($employee = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $employee['employee_id']; ?></td>
                        <td><?php echo $employee['employee_name']; ?></td>
                        <td><?php echo $employee['employee_email']; ?></td>
                        <td><?php echo $employee['employee_phone']; ?></td>
                        <td><?php echo $employee['shop_name']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($employee['created_at'])); ?></td>
                        <?php if ($_SESSION['employee_id'] == 1): ?>
                            <td>
                                <a href="manage_employees.php?edit=<?php echo $employee['employee_id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="manage_employees.php?delete=<?php echo $employee['employee_id']; ?>" 
                                   class="btn btn-danger" 
                                   data-confirm="Are you sure you want to delete this employee?"
                                   data-confirm-title="Delete Employee">
                                   Delete
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php include '../includes/footer.php'; ?>


