<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';

$base = getBasePath();
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$is_customer = ($current_dir === 'customer');
$is_employee = ($current_dir === 'employee');
$is_logged_in_customer = isset($_SESSION['customer_id']);
$is_logged_in_employee = isset($_SESSION['employee_id']);

$cart_count = 0;
$wishlist_count = 0;
$compare_count = count(getCompareList());

if (isset($conn)) {
    expireProductSales($conn);
}
if ($is_logged_in_customer && isset($conn)) {
    $cart_count = getCartCount($conn, $_SESSION['customer_id']);
    $wishlist_count = getWishlistCount($conn, $_SESSION['customer_id']);
    checkWishlistStockAlerts($conn, $_SESSION['customer_id']);
    $notif_count = getNotificationCount($conn, $_SESSION['customer_id']);
} else {
    $notif_count = 0;
}

$categories_nav = isset($conn) ? getCategories($conn) : [];
?>
<!DOCTYPE html>
<html lang="en" data-base="<?php echo e($base); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ByteStore - Premium electronics e-commerce in Nepal. Laptops, phones, gaming gear, and more.">
    <title><?php echo isset($page_title) ? e($page_title) . ' - ByteStore' : 'ByteStore - Premium Tech Store Nepal'; ?></title>
    <link rel="stylesheet" href="<?php echo e($base); ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo e($base); ?>assets/css/qa-fixes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="icon" href="<?php echo e($base); ?>assets/images/favicon.svg" type="image/svg+xml">
</head>
<body class="loading">
<div id="page-loader"><div class="loader-bar"></div></div>

<header class="site-header">
    <nav class="navbar">
        <a href="<?php echo e($base); ?>index.php" class="navbar__logo" aria-label="ByteStore Home">
            <span class="navbar__logo-accent">Byte</span><span class="navbar__logo-text">Store</span>
        </a>

        <?php if (!$is_employee): ?>
        <div class="navbar__search">
            <svg class="navbar__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="search" id="navbar-search" class="navbar__search-input" placeholder="Search products..." autocomplete="off" aria-label="Search products">
            <div id="search-suggestions" class="search-suggestions"></div>
        </div>
        <?php endif; ?>

        <ul class="navbar__nav" id="navbar-nav">
            <li><a href="<?php echo e($base); ?>index.php" class="navbar__link <?php echo $current_page === 'index.php' && !$is_customer && !$is_employee ? 'active' : ''; ?>">Home</a></li>

            <?php if ($is_logged_in_customer): ?>
                <li><a href="<?php echo e($base); ?>customer/shop.php" class="navbar__link <?php echo $current_page === 'shop.php' ? 'active' : ''; ?>">Shop</a></li>
                <li class="navbar__dropdown">
                    <button class="navbar__link navbar__dropdown-trigger" data-dropdown-trigger type="button" aria-haspopup="true" aria-expanded="false">Categories <i class="fa-solid fa-chevron-down navbar__dropdown-caret" aria-hidden="true"></i></button>
                    <div class="navbar__dropdown-menu navbar__categories-menu">
                        <?php foreach ($categories_nav as $cat): ?>
                            <a href="<?php echo e($base); ?>customer/shop.php?category=<?php echo (int)$cat['category_id']; ?>" class="navbar__dropdown-item navbar__category-link">
                                <?php echo e($cat['category_name']); ?>
                                <span class="text-muted"><?php echo (int)$cat['product_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </li>
            <?php elseif (!$is_employee): ?>
                <li><a href="<?php echo e($base); ?>pages/about.php" class="navbar__link">About</a></li>
                <li><a href="<?php echo e($base); ?>pages/contact.php" class="navbar__link">Contact</a></li>
            <?php endif; ?>

            <?php if ($is_logged_in_employee): ?>
                <li><a href="<?php echo e($base); ?>employee/dashboard.php" class="navbar__link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo e($base); ?>employee/view_orders.php" class="navbar__link">Orders</a></li>
            <?php endif; ?>
        </ul>

        <div class="navbar__actions">
            <button id="theme-toggle" class="navbar__icon-btn" aria-label="Toggle dark mode" title="Toggle theme">
                <span class="theme-toggle-icon"><i class="fa-solid fa-moon"></i></span>
            </button>

            <?php if ($is_logged_in_customer): ?>
                <a href="<?php echo e($base); ?>customer/account.php?page=notifications" class="navbar__icon-btn" title="Notifications" aria-label="Notifications">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($notif_count > 0): ?><span class="navbar__badge"><?php echo $notif_count; ?></span><?php endif; ?>
                </a>
                <a href="<?php echo e($base); ?>customer/wishlist.php" class="navbar__icon-btn" title="Wishlist" aria-label="Wishlist">
                    <i class="fa-solid fa-heart"></i>
                    <?php if ($wishlist_count > 0): ?><span id="wishlist-count" class="navbar__badge"><?php echo $wishlist_count; ?></span><?php endif; ?>
                </a>
                <a href="<?php echo e($base); ?>customer/compare.php" class="navbar__icon-btn" title="Compare" aria-label="Compare products">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span id="compare-count" class="navbar__badge" style="<?php echo $compare_count > 0 ? '' : 'display:none'; ?>"><?php echo $compare_count; ?></span>
                </a>
                <a href="<?php echo e($base); ?>customer/cart.php" class="navbar__icon-btn" title="Cart" aria-label="Shopping cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($cart_count > 0): ?><span class="navbar__badge"><?php echo $cart_count; ?></span><?php endif; ?>
                </a>
                <div class="navbar__dropdown">
                    <button class="navbar__icon-btn" data-dropdown-trigger aria-label="Account menu" title="My Account">
                        <i class="fa-solid fa-user"></i>
                    </button>
                    <div class="navbar__dropdown-menu">
                        <div style="padding:0.5rem 0.875rem;font-size:0.75rem;color:var(--color-text-muted);">Signed in as</div>
                        <div style="padding:0 0.875rem 0.5rem;font-weight:600;font-size:0.875rem;"><?php echo e($_SESSION['customer_name']); ?></div>
                        <a href="<?php echo e($base); ?>customer/account.php" class="navbar__dropdown-item">My Dashboard</a>
                        <a href="<?php echo e($base); ?>customer/account.php?page=orders" class="navbar__dropdown-item">Order History</a>
                        <a href="<?php echo e($base); ?>customer/account.php?page=wishlist" class="navbar__dropdown-item">Wishlist</a>
                        <a href="<?php echo e($base); ?>customer/account.php?page=support" class="navbar__dropdown-item">Support</a>
                        <a href="<?php echo e($base); ?>customer/account.php?page=settings" class="navbar__dropdown-item">Settings</a>
                        <hr style="border:none;border-top:1px solid var(--color-border);margin:0.5rem 0;">
                        <a href="<?php echo e($base); ?>customer/logout.php" class="navbar__dropdown-item">Logout</a>
                    </div>
                </div>
            <?php elseif (!$is_logged_in_employee): ?>
                <a href="<?php echo e($base); ?>customer/login.php" class="btn btn--sm btn--outline">Login</a>
                <a href="<?php echo e($base); ?>customer/register.php" class="btn btn--sm btn--primary">Register</a>
            <?php else: ?>
                <span class="navbar__link" style="font-size:0.8rem;"><?php echo e($_SESSION['employee_name']); ?></span>
                <a href="<?php echo e($base); ?>employee/logout.php" class="btn btn--sm btn--outline">Logout</a>
            <?php endif; ?>

            <button id="mobile-menu-toggle" class="navbar__mobile-toggle" aria-label="Toggle menu" aria-expanded="false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>
        </div>
    </nav>
</header>

<main class="main-content">
