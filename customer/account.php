<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$customer_id = (int)$_SESSION['customer_id'];
$account_page = $_GET['page'] ?? 'dashboard';
$message = flashMessage('success');
$error = flashMessage('error');

$customer_res = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = $customer_id LIMIT 1");
$customer = mysqli_fetch_assoc($customer_res);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['customer_phone']));
        $address = mysqli_real_escape_string($conn, trim($_POST['customer_address']));
        if (validateName($name) && validatePhone($phone)) {
            mysqli_query($conn, "UPDATE customer SET customer_name='$name', customer_phone='$phone', customer_address='$address' WHERE customer_id=$customer_id");
            $_SESSION['customer_name'] = $name;
            flashMessage('success', 'Profile updated successfully.');
        } else {
            flashMessage('error', 'Please check your input.');
        }
        header('Location: account.php?page=profile'); exit;
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (password_verify($current, $customer['customer_password']) && $new_pass === $confirm && validatePassword($new_pass)) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE customer SET customer_password='$hash' WHERE customer_id=$customer_id");
            flashMessage('success', 'Password changed successfully.');
        } else {
            flashMessage('error', 'Could not change password. Check current password and ensure new passwords match (min 6 chars).');
        }
        header('Location: account.php?page=settings'); exit;
    }

    if (isset($_POST['add_address'])) {
        $label = mysqli_real_escape_string($conn, $_POST['label'] ?? 'Home');
        $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
        $address_line = mysqli_real_escape_string($conn, trim($_POST['address_line']));
        $city = mysqli_real_escape_string($conn, trim($_POST['city']));
        if (validateName($full_name) && validatePhone($phone) && strlen($address_line) >= 5) {
            mysqli_query($conn, "INSERT INTO customer_address (customer_id, label, full_name, phone, address_line, city) VALUES ($customer_id, '$label', '$full_name', '$phone', '$address_line', '$city')");
            flashMessage('success', 'Address added.');
        } else {
            flashMessage('error', 'Please fill all address fields correctly.');
        }
        header('Location: account.php?page=addresses'); exit;
    }

    if (isset($_POST['delete_address'])) {
        $addr_id = (int)$_POST['address_id'];
        mysqli_query($conn, "DELETE FROM customer_address WHERE address_id=$addr_id AND customer_id=$customer_id");
        flashMessage('success', 'Address removed.');
        header('Location: account.php?page=addresses'); exit;
    }

    if (isset($_POST['submit_review'])) {
        $product_id = (int)$_POST['product_id'];
        $rating = max(1, min(5, (int)$_POST['rating']));
        $title = mysqli_real_escape_string($conn, trim($_POST['review_title'] ?? ''));
        $text = mysqli_real_escape_string($conn, trim($_POST['review_text']));
        if (strlen($text) >= 10) {
            mysqli_query($conn, "INSERT INTO product_review (product_id, customer_id, rating, review_title, review_text) VALUES ($product_id, $customer_id, $rating, '$title', '$text')");
            updateProductRatings($conn, $product_id);
            flashMessage('success', 'Review submitted!');
        }
        header('Location: account.php?page=orders'); exit;
    }
}

// Profile photo upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($_FILES['profile_photo']['type'], $allowed)) {
        $dir = '../assets/uploads/profiles/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $customer_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $dir . $filename)) {
            $path = 'assets/uploads/profiles/' . $filename;
            mysqli_query($conn, "UPDATE customer SET profile_photo='$path' WHERE customer_id=$customer_id");
            flashMessage('success', 'Profile photo updated.');
        }
    }
    header('Location: account.php?page=profile'); exit;
}

$page_title = 'My Account';
include '../includes/header.php';
echo renderBreadcrumbs([['label' => 'Home', 'url' => '../index.php'], ['label' => 'My Account', 'url' => '']]);

if ($message) echo '<div class="alert alert-success">' . e($message) . '</div>';
if ($error) echo '<div class="alert alert-error">' . e($error) . '</div>';
?>

<div class="account-layout">
    <?php include '../includes/account_sidebar.php'; ?>

    <div>
        <?php if ($account_page === 'dashboard'): ?>
            <div class="dashboard-stats">
                <?php
                $order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE customer_id=$customer_id"))['c'];
                $wishlist_count = getWishlistCount($conn, $customer_id);
                $cart_count = getCartCount($conn, $customer_id);
                ?>
                <div class="stat-card stat-card--primary"><div class="stat-card__label">Total Orders</div><div class="stat-card__value"><?php echo $order_count; ?></div></div>
                <div class="stat-card"><div class="stat-card__label">Wishlist Items</div><div class="stat-card__value"><?php echo $wishlist_count; ?></div></div>
                <div class="stat-card"><div class="stat-card__label">Cart Items</div><div class="stat-card__value"><?php echo $cart_count; ?></div></div>
            </div>
            <div class="card">
                <h2>Welcome back, <?php echo e($customer['customer_name']); ?>!</h2>
                <p class="text-muted" style="margin:1rem 0;">Manage your orders, wishlist, and account settings from here.</p>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <a href="shop.php" class="btn btn--primary">Continue Shopping</a>
                    <a href="account.php?page=orders" class="btn btn--outline">View Orders</a>
                    <a href="wishlist.php" class="btn btn--outline">My Wishlist</a>
                </div>
            </div>
            <?php
            $recent = mysqli_query($conn, "SELECT p.* FROM recently_viewed rv JOIN product p ON rv.product_id = p.product_id WHERE rv.customer_id = $customer_id ORDER BY rv.viewed_at DESC LIMIT 4");
            if ($recent && mysqli_num_rows($recent) > 0):
            ?>
            <div class="card" style="margin-top:1.5rem;">
                <h3 style="margin-bottom:1rem;">Recently Viewed</h3>
                <div class="product-grid">
                    <?php while ($product = mysqli_fetch_assoc($recent)): include '../includes/product_card.php'; endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php elseif ($account_page === 'profile'): ?>
            <div class="card profile-form">
                <h2>Profile Information</h2>
                <form method="POST" enctype="multipart/form-data" style="margin-top:1.5rem;">
                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <?php $photo = !empty($customer['profile_photo']) ? '../' . $customer['profile_photo'] : '../assets/images/placeholder.svg'; ?>
                        <img src="<?php echo e($photo); ?>" alt="Profile" class="account-avatar" style="width:100px;height:100px;" onerror="this.src='../assets/images/placeholder.svg'">
                        <div style="margin-top:0.75rem;"><input type="file" name="profile_photo" accept="image/*"><button type="submit" class="btn btn--sm btn--outline" style="margin-left:0.5rem;">Upload Photo</button></div>
                    </div>
                </form>
                <form method="POST" data-validate-form>
                    <input type="hidden" name="update_profile" value="1">
                    <div class="form-group"><label>Full Name</label><input type="text" name="customer_name" data-validate="name" value="<?php echo e($customer['customer_name']); ?>" required></div>
                    <div class="form-group"><label>Email</label><input type="email" value="<?php echo e($customer['customer_email']); ?>" disabled><p class="form-hint">Email cannot be changed</p></div>
                    <div class="form-group"><label>Phone</label><input type="tel" name="customer_phone" data-validate="phone" value="<?php echo e($customer['customer_phone']); ?>" required></div>
                    <div class="form-group"><label>Default Address</label><textarea name="customer_address" data-validate="address" rows="3" required><?php echo e($customer['customer_address']); ?></textarea></div>
                    <button type="submit" class="btn btn--primary">Save Changes</button>
                </form>
            </div>

        <?php elseif ($account_page === 'orders'): ?>
            <div class="card">
                <h2>Order History</h2>
                <?php
                $orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY order_date DESC");
                if ($orders && mysqli_num_rows($orders) > 0):
                ?>
                <table style="margin-top:1rem;">
                    <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th></th></tr></thead>
                    <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders)):
                        $status_class = strtolower($order['order_status']);
                    ?>
                        <tr>
                            <td>#<?php echo (int)$order['order_id']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                            <td><span class="status-badge status-<?php echo e($status_class); ?>"><?php echo e($order['order_status']); ?></span></td>
                            <td><?php echo e($order['payment_status']); ?></td>
                            <td><a href="cart.php#order-<?php echo (int)$order['order_id']; ?>" class="btn btn--sm btn--outline">Track</a></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-state" style="padding:2rem;"><p>No orders yet.</p><a href="shop.php" class="btn btn--primary">Start Shopping</a></div>
                <?php endif; ?>
            </div>

        <?php elseif ($account_page === 'addresses'): ?>
            <div class="card">
                <h2>Saved Addresses</h2>
                <?php $addrs = mysqli_query($conn, "SELECT * FROM customer_address WHERE customer_id = $customer_id ORDER BY is_default DESC"); ?>
                <?php if ($addrs && mysqli_num_rows($addrs) > 0): ?>
                    <?php while ($addr = mysqli_fetch_assoc($addrs)): ?>
                        <div style="padding:1rem;border:1px solid var(--color-border);border-radius:var(--radius-md);margin:1rem 0;">
                            <strong><?php echo e($addr['label']); ?></strong>
                            <p><?php echo e($addr['full_name']); ?> — <?php echo e($addr['phone']); ?></p>
                            <p class="text-muted"><?php echo e($addr['address_line']); ?>, <?php echo e($addr['city']); ?></p>
                            <form method="POST" style="margin-top:0.5rem;"><input type="hidden" name="address_id" value="<?php echo (int)$addr['address_id']; ?>"><button type="submit" name="delete_address" class="btn btn--sm btn--danger">Remove</button></form>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <h3 style="margin-top:1.5rem;">Add New Address</h3>
                <form method="POST" data-validate-form style="margin-top:1rem;">
                    <input type="hidden" name="add_address" value="1">
                    <div class="form-row">
                        <div class="form-group"><label>Label</label><select name="label"><option>Home</option><option>Office</option><option>Other</option></select></div>
                        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" data-validate="name" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Phone</label><input type="tel" name="phone" data-validate="phone" required></div>
                        <div class="form-group"><label>City</label><input type="text" name="city" value="Kathmandu" required></div>
                    </div>
                    <div class="form-group"><label>Address</label><textarea name="address_line" data-validate="address" rows="2" required></textarea></div>
                    <button type="submit" class="btn btn--primary">Add Address</button>
                </form>
            </div>

        <?php elseif ($account_page === 'recent'): ?>
            <div class="card">
                <h2>Recently Viewed</h2>
                <?php $recent = mysqli_query($conn, "SELECT p.* FROM recently_viewed rv JOIN product p ON rv.product_id = p.product_id WHERE rv.customer_id = $customer_id ORDER BY rv.viewed_at DESC LIMIT 20"); ?>
                <?php if ($recent && mysqli_num_rows($recent) > 0): ?>
                    <div class="product-grid" style="margin-top:1rem;">
                        <?php while ($product = mysqli_fetch_assoc($recent)): include '../includes/product_card.php'; endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state"><p>No recently viewed products.</p><a href="shop.php" class="btn btn--primary">Browse Products</a></div>
                <?php endif; ?>
            </div>

        <?php elseif ($account_page === 'wishlist'): ?>
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">My Wishlist</h2>
                    <a href="wishlist.php" class="btn btn--outline btn--sm">Full Wishlist Page</a>
                </div>
                <?php
                $wl = mysqli_query($conn, "SELECT w.*, p.* FROM wishlist w JOIN product p ON w.product_id = p.product_id WHERE w.customer_id = $customer_id ORDER BY w.added_at DESC");
                ?>
                <?php if ($wl && mysqli_num_rows($wl) > 0): ?>
                    <div class="product-grid">
                        <?php while ($product = mysqli_fetch_assoc($wl)): include '../includes/product_card.php'; endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state__icon"><i class="fa-solid fa-heart"></i></div>
                        <h3 class="empty-state__title">Your wishlist is empty</h3>
                        <p class="empty-state__desc">Save products you love using the heart icon on product cards.</p>
                        <a href="shop.php" class="btn btn--primary">Browse Products</a>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($account_page === 'support'): ?>
            <div class="card">
                <h2>Contact Support</h2>
                <p class="text-muted" style="margin-bottom:1rem;">Submit a ticket and track its status below.</p>
                <form method="POST" action="support.php" class="form-grid" style="max-width:600px;">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Order">Order Issue</option>
                            <option value="Product">Product Question</option>
                            <option value="Payment">Payment / Refund</option>
                            <option value="Technical">Technical Issue</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Subject</label><input type="text" name="subject" required maxlength="200"></div>
                    <div class="form-group"><label>Message</label><textarea name="message" rows="5" required minlength="10"></textarea></div>
                    <button type="submit" class="btn btn--primary"><i class="fa-solid fa-paper-plane"></i> Submit Ticket</button>
                </form>
            </div>
            <div class="card" style="margin-top:1.5rem;">
                <h3>My Support Tickets</h3>
                <?php
                $tickets = @mysqli_query($conn, "SELECT * FROM support_ticket WHERE customer_id = $customer_id ORDER BY updated_at DESC LIMIT 20");
                ?>
                <?php if ($tickets && mysqli_num_rows($tickets) > 0): ?>
                    <table style="margin-top:1rem;">
                        <thead><tr><th>ID</th><th>Subject</th><th>Status</th><th>Updated</th></tr></thead>
                        <tbody>
                        <?php while ($t = mysqli_fetch_assoc($tickets)): ?>
                            <tr>
                                <td>#<?php echo (int)$t['ticket_id']; ?></td>
                                <td><a href="support.php?id=<?php echo (int)$t['ticket_id']; ?>"><?php echo e($t['subject']); ?></a></td>
                                <td><span class="status-badge status-<?php echo strtolower($t['status']); ?>"><?php echo e($t['status']); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($t['updated_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted" style="margin-top:1rem;">No support tickets yet.</p>
                <?php endif; ?>
            </div>

        <?php elseif ($account_page === 'notifications'): ?>
            <div class="card">
                <h2>Notifications</h2>
                <?php
                @mysqli_query($conn, "UPDATE customer_notification SET is_read = 1 WHERE customer_id = $customer_id");
                $notifs = @mysqli_query($conn, "SELECT n.*, p.product_name FROM customer_notification n LEFT JOIN product p ON n.product_id = p.product_id WHERE n.customer_id = $customer_id ORDER BY n.created_at DESC LIMIT 50");
                ?>
                <?php if ($notifs && mysqli_num_rows($notifs) > 0): ?>
                    <?php while ($n = mysqli_fetch_assoc($notifs)): ?>
                        <div class="alert alert-info" style="margin-top:0.75rem;display:flex;gap:0.75rem;align-items:center;">
                            <i class="fa-solid fa-<?php echo $n['type'] === 'wishlist_stock' ? 'heart' : 'bell'; ?>"></i>
                            <div>
                                <strong><?php echo e($n['message']); ?></strong>
                                <div class="text-muted" style="font-size:0.8rem;"><?php echo date('M j, Y g:i A', strtotime($n['created_at'])); ?></div>
                                <?php if ($n['product_id']): ?><a href="product.php?id=<?php echo (int)$n['product_id']; ?>">View product</a><?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state"><p>No notifications yet.</p></div>
                <?php endif; ?>
            </div>

        <?php elseif ($account_page === 'settings'): ?>
            <div class="card">
                <h2>Security Settings</h2>
                <form method="POST" data-validate-form style="margin-top:1.5rem;max-width:400px;">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
                    <div class="form-group"><label>New Password</label><input type="password" name="new_password" data-validate="password" required></div>
                    <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" data-validate="confirm_password" required></div>
                    <button type="submit" class="btn btn--primary">Change Password</button>
                </form>
            </div>

        <?php else: ?>
            <div class="card"><p>Page not found. <a href="account.php">Return to dashboard</a></p></div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
