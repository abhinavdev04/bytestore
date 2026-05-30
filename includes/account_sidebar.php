<?php
$account_page = $account_page ?? 'dashboard';
$pages = [
    'dashboard' => ['icon' => 'fa-house', 'label' => 'Dashboard'],
    'profile' => ['icon' => 'fa-user', 'label' => 'Profile'],
    'orders' => ['icon' => 'fa-box', 'label' => 'Orders'],
    'addresses' => ['icon' => 'fa-location-dot', 'label' => 'Addresses'],
    'wishlist' => ['icon' => 'fa-heart', 'label' => 'Wishlist'],
    'recent' => ['icon' => 'fa-clock-rotate-left', 'label' => 'Recently Viewed'],
    'notifications' => ['icon' => 'fa-bell', 'label' => 'Notifications'],
    'support' => ['icon' => 'fa-headset', 'label' => 'Support'],
    'settings' => ['icon' => 'fa-gear', 'label' => 'Settings'],
];
?>
<aside class="account-sidebar">
    <div style="text-align:center;padding:1rem 0 1.5rem;">
        <?php $photo = !empty($customer['profile_photo']) ? '../' . $customer['profile_photo'] : '../assets/images/placeholder.svg'; ?>
        <img src="<?php echo e($photo); ?>" alt="Profile" class="account-avatar" onerror="this.src='../assets/images/placeholder.svg'">
        <div style="margin-top:0.75rem;font-weight:600;"><?php echo e($customer['customer_name']); ?></div>
        <div style="font-size:0.8rem;color:var(--color-text-muted);"><?php echo e($customer['customer_email']); ?></div>
    </div>
    <nav>
        <?php foreach ($pages as $key => $p): ?>
            <a href="account.php?page=<?php echo e($key); ?>" class="account-nav__link <?php echo $account_page === $key ? 'active' : ''; ?>">
                <i class="fa-solid <?php echo e($p['icon']); ?>"></i> <?php echo e($p['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
