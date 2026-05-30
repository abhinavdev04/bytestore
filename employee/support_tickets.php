<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';
require '../includes/functions.php';

checkEmployeeLogin();

$success = '';
$error = '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_ticket'])) {
    $tid = (int)$_POST['ticket_id'];
    $reply = mysqli_real_escape_string($conn, trim($_POST['reply_message']));
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Pending');
    $eid = (int)$_SESSION['employee_id'];
    if ($tid > 0 && strlen($reply) >= 3) {
        mysqli_query($conn, "INSERT INTO support_reply (ticket_id, sender_type, sender_id, message) VALUES ($tid, 'employee', $eid, '$reply')");
        mysqli_query($conn, "UPDATE support_ticket SET status = '$status', updated_at = NOW() WHERE ticket_id = $tid");
        $success = 'Reply sent and ticket updated.';
    }
}

$where = ['1=1'];
if ($filter_status) $where[] = "t.status = '$filter_status'";
if ($search) $where[] = "(t.subject LIKE '%$search%' OR t.message LIKE '%$search%' OR c.customer_name LIKE '%$search%')";
$where_sql = implode(' AND ', $where);

$tickets = mysqli_query($conn, "SELECT t.*, c.customer_name, c.customer_email FROM support_ticket t JOIN customer c ON t.customer_id = c.customer_id WHERE $where_sql ORDER BY t.updated_at DESC LIMIT 100");

$view_id = (int)($_GET['id'] ?? 0);
$view_ticket = null;
$view_replies = null;
if ($view_id > 0) {
    $vr = mysqli_query($conn, "SELECT t.*, c.customer_name, c.customer_email FROM support_ticket t JOIN customer c ON t.customer_id = c.customer_id WHERE t.ticket_id = $view_id LIMIT 1");
    $view_ticket = $vr ? mysqli_fetch_assoc($vr) : null;
    if ($view_ticket) {
        $view_replies = mysqli_query($conn, "SELECT * FROM support_reply WHERE ticket_id = $view_id ORDER BY created_at ASC");
    }
}

$page_title = 'Support Tickets';
include '../includes/header.php';
?>

<div class="card">
    <h1><i class="fa-solid fa-headset"></i> Support Tickets</h1>
    <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
    <form method="GET" class="admin-search-bar" style="margin-top:1rem;display:flex;gap:0.75rem;flex-wrap:wrap;">
        <input type="search" name="q" placeholder="Search tickets..." value="<?php echo e($search); ?>" style="flex:1;">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach (['Open', 'Pending', 'Resolved', 'Closed'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $filter_status === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn--primary">Filter</button>
        <a href="support_tickets.php" class="btn btn--outline">Clear</a>
    </form>
</div>

<?php if ($view_ticket): ?>
<div class="card">
    <h2>#<?php echo (int)$view_ticket['ticket_id']; ?> — <?php echo e($view_ticket['subject']); ?></h2>
    <p class="text-muted"><?php echo e($view_ticket['customer_name']); ?> · <?php echo e($view_ticket['customer_email']); ?> · <?php echo e($view_ticket['category']); ?></p>
    <div class="support-message"><?php echo nl2br(e($view_ticket['message'])); ?></div>
    <?php if ($view_replies && mysqli_num_rows($view_replies) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($view_replies)): ?>
            <div class="support-reply support-reply--<?php echo e($r['sender_type']); ?>">
                <strong><?php echo $r['sender_type'] === 'employee' ? 'Staff' : 'Customer'; ?></strong>
                <p><?php echo nl2br(e($r['message'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <form method="POST" style="margin-top:1.5rem;">
        <input type="hidden" name="reply_ticket" value="1">
        <input type="hidden" name="ticket_id" value="<?php echo (int)$view_ticket['ticket_id']; ?>">
        <div class="form-group"><label>Reply</label><textarea name="reply_message" rows="4" required></textarea></div>
        <div class="form-group"><label>Status</label>
            <select name="status">
                <?php foreach (['Open', 'Pending', 'Resolved', 'Closed'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $view_ticket['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn--primary">Send Reply</button>
        <a href="support_tickets.php" class="btn btn--outline">Back to list</a>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Customer</th><th>Subject</th><th>Category</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
        <?php if ($tickets) while ($t = mysqli_fetch_assoc($tickets)): ?>
            <tr>
                <td>#<?php echo (int)$t['ticket_id']; ?></td>
                <td><?php echo e($t['customer_name']); ?></td>
                <td><?php echo e(substr($t['subject'], 0, 40)); ?></td>
                <td><?php echo e($t['category']); ?></td>
                <td><span class="status-badge status-<?php echo strtolower($t['status']); ?>"><?php echo e($t['status']); ?></span></td>
                <td><?php echo date('M j, Y', strtotime($t['updated_at'])); ?></td>
                <td><a href="support_tickets.php?id=<?php echo (int)$t['ticket_id']; ?>" class="btn btn--sm btn--primary">View</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
