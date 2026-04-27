<?php
// -------------------------------------------------------------
// Shared header file used by both customer and employee pages.
// - Starts the session if needed
// - Renders the top navigation bar
// - Shows different links based on current folder (customer/employee)
//   and whether the user is logged in
// -------------------------------------------------------------

// Start session if not already started so we can read login variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ByteStore - Tech E-Commerce</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="loading">
    <!-- Simple top loading bar to make page transitions feel smoother -->
    <div id="page-loader">
        <div class="loader-bar"></div>
    </div>
    <script>
        // Very short fake loading delay so the page feels like it's loading
        // without slowing the user down too much.
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.body.classList.remove('loading');
                document.body.classList.add('loaded');
            }, 250); // adjust this (in ms) if you want slightly longer/shorter
        });
    </script>
    <header>
        <nav>
            <!-- Logo always sends the user back to the public homepage -->
            <div class="logo" onclick="window.location.href='../index.php'">

            <div 
            class="logo" 
            onclick="window.location.href='index.php'"
            style="
                font-family: 'Segoe UI', sans-serif;
                font-size: 28px;
                font-weight: 700;
                letter-spacing: 1px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-block;
            "
            onmouseover="this.style.textShadow='0 0 8px #00c6ff'"
            onmouseout="this.style.textShadow='none'"
            >
            <span style="color:#00c6ff;">Byte</span><span style="color:#ffffff;">Store</span>
</div>





            </div>
            <ul>
                <?php 
                // Determine which folder we are in (customer / employee / root)
                $current_page = basename($_SERVER['PHP_SELF']);
                $current_dir = basename(dirname($_SERVER['PHP_SELF']));

                // For pages inside sub-folders we need to go one level up for the homepage
                $home_link = ($current_dir == 'customer' || $current_dir == 'employee') ? '../index.php' : 'index.php';
                ?>

                <!-- Link back to homepage -->
                <li>
                    <a href="<?php echo $home_link; ?>" <?php if($current_page == 'index.php') echo 'class="active"'; ?>>
                        Home
                    </a>
                </li>

                <?php if ($current_dir == 'customer'): ?>
                    <!-- Navigation for customer-area pages -->
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <!-- Logged-in customer: show shopping links and logout -->
                        <li><a href="shop.php" <?php if($current_page == 'shop.php') echo 'class="active"'; ?>>Shop</a></li>
                        <li><a href="cart.php" <?php if($current_page == 'cart.php') echo 'class="active"'; ?>>Cart</a></li>
                        <li><a href="logout.php">Logout (<?php echo $_SESSION['customer_name']; ?>)</a></li>
                    <?php else: ?>
                        <!-- Guest on customer pages: show login/register -->
                        <li><a href="login.php" <?php if($current_page == 'login.php') echo 'class="active"'; ?>>Customer Login</a></li>
                        <li><a href="register.php" <?php if($current_page == 'register.php') echo 'class="active"'; ?>>Register</a></li>
                    <?php endif; ?>

                <?php elseif ($current_dir == 'employee'): ?>
                    <!-- Navigation for employee (staff) pages -->
                    <?php if (isset($_SESSION['employee_id'])): ?>
                        <!-- Logged-in staff: show dashboard link and logout -->
                        <li><a href="dashboard.php" <?php if($current_page == 'dashboard.php') echo 'class="active"'; ?>>Staff Dashboard</a></li>
                        <li><a href="logout.php">Logout (<?php echo $_SESSION['employee_name']; ?>)</a></li>
                    <?php else: ?>
                        <!-- Guest on employee pages: show staff login -->
                        <li><a href="login.php" <?php if($current_page == 'login.php') echo 'class="active"'; ?>>Staff Login</a></li>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Navigation on public pages (homepage or other root pages) -->
                    <li><a href="customer/login.php">Customer Login</a></li>
                    <li><a href="customer/register.php">Register</a></li>
                    <!-- Employees are now the only staff role -->
                    <li><a href="employee/login.php">Staff Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <!-- Container opened here and closed in footer.php so every page content
         sits inside the same consistent layout -->
    <div class="container">

