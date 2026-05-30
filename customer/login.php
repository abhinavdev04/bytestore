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
        $result = mysqli_query($conn, "SELECT * FROM customer WHERE customer_email='$emailEsc' LIMIT 1");
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['customer_password'])) {
                $_SESSION['customer_id'] = $row['customer_id'];
                $_SESSION['customer_name'] = $row['customer_name'];
                header('Location: shop.php');
                exit();
            }
        }
        $error = 'Invalid email or password.';
    }
}

$page_title = 'Login';
include '../includes/header.php';
?>

<div class="auth-container">
    <h2>Welcome Back</h2>
    <p class="auth-subtitle">Sign in to your ByteStore account</p>

    <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

    <form method="POST" data-validate-form>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" data-validate="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" name="login" class="btn btn--primary btn--block">Sign In</button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:0.875rem;">
        Don't have an account? <a href="register.php">Create one</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
