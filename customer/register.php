<?php
session_start();
require '../config/config.php';
require '../includes/functions.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!validateName($name)) {
        $error = 'Please enter a valid name (min 2 characters).';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format.';
    } elseif (!validatePassword($password)) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!validatePhone($phone)) {
        $error = 'Invalid phone number.';
    } elseif (strlen($address) < 5) {
        $error = 'Please enter a complete address.';
    } else {
        $nameEsc = mysqli_real_escape_string($conn, $name);
        $emailEsc = mysqli_real_escape_string($conn, $email);
        $phoneEsc = mysqli_real_escape_string($conn, $phone);
        $addressEsc = mysqli_real_escape_string($conn, $address);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $check = mysqli_query($conn, "SELECT customer_id FROM customer WHERE customer_email='$emailEsc' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = 'Email already registered.';
        } else {
            $sql = "INSERT INTO customer (customer_name, customer_email, customer_password, customer_phone, customer_address) VALUES ('$nameEsc','$emailEsc','$hashedPassword','$phoneEsc','$addressEsc')";
            if (mysqli_query($conn, $sql)) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$page_title = 'Register';
include '../includes/header.php';
?>

<div class="auth-container">
    <h2>Create Account</h2>
    <p class="auth-subtitle">Join ByteStore for the best tech shopping experience</p>

    <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?> <a href="login.php">Login here</a></div><?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" data-validate-form>
        <div class="form-group"><label>Full Name</label><input type="text" name="name" data-validate="name" required value="<?php echo e($_POST['name'] ?? ''); ?>"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" data-validate="email" required value="<?php echo e($_POST['email'] ?? ''); ?>"></div>
        <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" data-validate="phone" required value="<?php echo e($_POST['phone'] ?? ''); ?>"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" data-validate="password" required><p class="form-hint">Minimum 6 characters</p></div>
        <div class="form-group"><label>Address</label><textarea name="address" data-validate="address" rows="3" required><?php echo e($_POST['address'] ?? ''); ?></textarea></div>
        <button type="submit" name="register" class="btn btn--primary btn--block">Create Account</button>
    </form>
    <?php endif; ?>

    <p style="text-align:center;margin-top:1.25rem;font-size:0.875rem;">
        Already have an account? <a href="login.php">Sign in</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
