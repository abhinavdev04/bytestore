<?php
// -------------------------------------------------------------
// Customer Registration Page
// - Validates email format using regex
// - Validates password strength using regex
// - Stores password as plain text (academic only)
// -------------------------------------------------------------


session_start();
require '../config/config.php';

$error = '';
$success = '';

$emailPattern    = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
$passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/";
$phonePattern = "/^(\+977)?[9][6-9]\d{8}$/";

if (isset($_POST['register'])) {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if (!preg_match($emailPattern, $email)) {
        $error = "Invalid email format!";
    } elseif (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, digit, and special character.";
    } elseif (!preg_match($phonePattern, $phone)) {
        $error = "Invalid phone number! Must be a valid Nepal number (e.g., 9801234567 or +9779801234567).";
    } else {

        $nameEsc    = mysqli_real_escape_string($conn, $name);
        $emailEsc   = mysqli_real_escape_string($conn, $email);
        $phoneEsc   = mysqli_real_escape_string($conn, $phone);
        $addressEsc = mysqli_real_escape_string($conn, $address);

        // HASH PASSWORD
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkSql = "SELECT * FROM customer WHERE customer_email='$emailEsc'";
        $checkRes = mysqli_query($conn, $checkSql);

        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $error = "Email already registered!";
        } else {
            $insertSql = "
                INSERT INTO customer (customer_name, customer_email, customer_password, customer_phone, customer_address)
                VALUES ('$nameEsc', '$emailEsc', '$hashedPassword', '$phoneEsc', '$addressEsc')
            ";

            if (mysqli_query($conn, $insertSql)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="auth-container">
        <h2>Customer Registration</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                <small>
                    Must have at least 8 characters, including uppercase, lowercase, number, and special character.
                </small>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required></textarea>
            </div>
            
            <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
