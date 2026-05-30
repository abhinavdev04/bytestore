<?php
session_start();
require '../config/config.php';
require '../includes/functions.php';

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!validateEmail($email)) {
        $error = 'Invalid email format.';
    } else {
        $emailEsc = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn, "SELECT * FROM employee WHERE employee_email='$emailEsc' LIMIT 1");
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['employee_password'])) {
                $_SESSION['employee_id'] = $row['employee_id'];
                $_SESSION['employee_name'] = $row['employee_name'];
                header('Location: dashboard.php');
                exit();
            }
        }
        $error = 'Invalid email or password.';
    }
}

$page_title = 'Staff Login';
include '../includes/header.php';
?>

<div class="auth-container">
    <h2>Staff Login</h2>
    <p class="auth-subtitle">Employee access to ByteStore management panel</p>

    <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

    <form method="POST" data-validate-form>
        <div class="form-group"><label>Email</label><input type="email" name="email" data-validate="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" name="login" class="btn btn--primary btn--block">Sign In</button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;"><a href="../index.php">← Back to Store</a></p>
</div>

<?php include '../includes/footer.php'; ?>
