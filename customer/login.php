<?php
session_start();
require '../config/config.php';

$error = '';

if (isset($_POST['login'])) {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

    if (!preg_match($emailPattern, $email)) {
        $error = "Invalid email format!";
    } else {

        $emailEsc = mysqli_real_escape_string($conn, $email);

        // DO NOT compare password in SQL anymore
        $sql = "SELECT * FROM customer WHERE customer_email='$emailEsc'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // VERIFY HASH PASSWORD
            if (password_verify($password, $row['customer_password'])) {

                $_SESSION['customer_id']   = $row['customer_id'];
                $_SESSION['customer_name'] = $row['customer_name'];

                header("Location: shop.php");
                exit();
            }
        }

        $error = "Invalid email or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="auth-container">
        <h2>Customer Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
