<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkCustomerLogin();

$customer_id = (int)$_SESSION['customer_id'];
$ticket_id = sanitizeInt($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'], $_POST['message'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'Other');
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    if (strlen($message) >= 10 && strlen($subject) >= 3) {
        mysqli_query($conn, "INSERT INTO support_ticket (customer_id, category, subject, message, status) VALUES ($customer_id, '$category', '$subject', '$message', 'Open')");
        flashMessage('success', 'Support ticket submitted. We will respond within 24 hours.');
        header('Location: account.php?page=support');
        exit;
    }
    flashMessage('error', 'Please provide a subject and message (min 10 characters).');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message']) && $ticket_id > 0) {
    $reply = mysqli_real_escape_string($conn, trim($_POST['reply_message']));
    $check = mysqli_query($conn, "SELECT ticket_id FROM support_ticket WHERE ticket_id = $ticket_id AND customer_id = $customer_id LIMIT 1");
    if ($check && mysqli_num_rows($check) && strlen($reply) >= 3) {
        mysqli_query($conn, "INSERT INTO support_reply (ticket_id, sender_type, sender_id, message) VALUES ($ticket_id, 'customer', $customer_id, '$reply')");
        mysqli_query($conn, "UPDATE support_ticket SET status = 'Pending', updated_at = NOW() WHERE ticket_id = $ticket_id");
        flashMessage('success', 'Reply sent.');
    }
    header('Location: support.php?id=' . $ticket_id);
    exit;
}

$ticket = null;
$replies = null;
if ($ticket_id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM support_ticket WHERE ticket_id = $ticket_id AND customer_id = $customer_id LIMIT 1");
    $ticket = $res ? mysqli_fetch_assoc($res) : null;
    if ($ticket) {
        $replies = mysqli_query($conn, "SELECT * FROM support_reply WHERE ticket_id = $ticket_id ORDER BY created_at ASC");
    }
}

$page_title = 'Support';
include '../includes/header.php';
echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '../index.php'],
    ['label' => 'Account', 'url' => 'account.php'],
    ['label' => 'Support', 'url' => ''],
]);
?>

<?php if ($msg = flashMessage('success')): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>
<?php if ($err = flashMessage('error')): ?><div class="alert alert-error"><?php echo e($err); ?></div><?php endif; ?>

<?php if ($ticket): ?>
<div class="card">
    <div class="card__header">
        <h1>Ticket #<?php echo (int)$ticket['ticket_id']; ?>: <?php echo e($ticket['subject']); ?></h1>
        <span class="status-badge status-<?php echo strtolower($ticket['status']); ?>"><?php echo e($ticket['status']); ?></span>
    </div>
    <p class="text-muted">Category: <?php echo e($ticket['category']); ?> · Created <?php echo date('M j, Y g:i A', strtotime($ticket['created_at'])); ?></p>
    <div class="support-message"><?php echo nl2br(e($ticket['message'])); ?></div>
    <?php if ($replies && mysqli_num_rows($replies) > 0): ?>
        <h3 style="margin:1.5rem 0 0.75rem;">Conversation</h3>
        <?php while ($r = mysqli_fetch_assoc($replies)): ?>
            <div class="support-reply support-reply--<?php echo e($r['sender_type']); ?>">
                <strong><?php echo $r['sender_type'] === 'employee' ? 'ByteStore Support' : 'You'; ?></strong>
                <span class="text-muted" style="font-size:0.8rem;"> · <?php echo date('M j, g:i A', strtotime($r['created_at'])); ?></span>
                <p><?php echo nl2br(e($r['message'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php if (!in_array($ticket['status'], ['Closed', 'Resolved'], true)): ?>
    <form method="POST" style="margin-top:1.5rem;">
        <div class="form-group"><label>Your Reply</label><textarea name="reply_message" rows="4" required></textarea></div>
        <button type="submit" class="btn btn--primary">Send Reply</button>
    </form>
    <?php endif; ?>
    <a href="account.php?page=support" class="btn btn--outline" style="margin-top:1rem;">← Back to tickets</a>
</div>
<?php else: ?>
<div class="card">
    <h1>Contact Support</h1>
    <form method="POST" style="max-width:600px;margin-top:1rem;">
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
        <div class="form-group"><label>Message</label><textarea name="message" rows="6" required minlength="10"></textarea></div>
        <button type="submit" class="btn btn--primary"><i class="fa-solid fa-paper-plane"></i> Submit Ticket</button>
    </form>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
