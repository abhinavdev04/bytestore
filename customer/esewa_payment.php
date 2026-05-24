<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

if (!isset($_SESSION['esewa_order_id']) || !isset($_SESSION['esewa_amount'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_SESSION['esewa_order_id'];
$amount   = $_SESSION['esewa_amount'];

// Pull eSewa settings from central config
$product_code = $ESEWA_PRODUCT_CODE ?? 'EPAYTEST';
$secret_key   = $ESEWA_SECRET_KEY ?? '';
$esewa_url    = $ESEWA_FORM_URL ?? 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';

$total_amount     = number_format((float)$amount, 2, '.', '');
$transaction_uuid = "BYTESTORE-" . $order_id . "-" . time();

$_SESSION['esewa_txn_uuid']     = $transaction_uuid;
$_SESSION['esewa_total_amount'] = $total_amount;

// Determine base URL: prefer configured BASE_URL, otherwise derive from server
if (!empty($BASE_URL)) {
    $base_url = rtrim($BASE_URL, '/');
} else {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $root = preg_replace('#/customer.*$#', '', dirname($_SERVER['SCRIPT_NAME']));
    $base_url = rtrim($scheme . '://' . $host . $root, '/');
}

$success_url = $base_url . '/customer/esewa_success.php';
$failure_url = $base_url . '/customer/esewa_failure.php';

$message   = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
$signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

// Debug: record the payload that will be sent to eSewa (helps diagnose missing data)
$log_path = dirname(__DIR__) . '/esewa_debug.log';
$debug_payload  = "=== eSewa Payment Form Payload ===\n";
$debug_payload .= "Time: " . date('Y-m-d H:i:s') . "\n";
$debug_payload .= "Form fields:\n";
$debug_payload .= "total_amount={$total_amount}\n";
$debug_payload .= "transaction_uuid={$transaction_uuid}\n";
$debug_payload .= "product_code={$product_code}\n";
$debug_payload .= "signature={$signature}\n";
$debug_payload .= "success_url={$success_url}\n";
$debug_payload .= "failure_url={$failure_url}\n";
// Include legacy field names in debug payload too
$debug_payload .= "pid={$transaction_uuid}\n";
$debug_payload .= "scd={$product_code}\n";
$debug_payload .= "amt={$total_amount}\n";
$debug_payload .= "tAmt={$total_amount}\n";
$debug_payload .= "su={$success_url}\n";
$debug_payload .= "fu={$failure_url}\n";
$debug_payload .= "psc=0\n";
$debug_payload .= "pdc=0\n";
file_put_contents($log_path, $debug_payload, FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .redirect-card { text-align: center; padding: 40px; max-width: 500px; margin: 60px auto; }
        .esewa-logo-big { font-size: 42px; font-weight: 900; color: #60BB46; margin-bottom: 10px; }
        .spinner { width: 48px; height: 48px; border: 5px solid #ddd; border-top-color: #60BB46; border-radius: 50%; animation: spin 0.9s linear infinite; margin: 20px auto; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .order-info { background: #f9f9f9; border-radius: 8px; padding: 14px 20px; margin: 20px 0; text-align: left; }
        .order-info p { margin: 4px 0; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="card redirect-card">
        <div class="esewa-logo-big">eSewa</div>
        <h3>Redirecting to eSewa Payment...</h3>
        <div class="order-info">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Amount:</strong> Rs. <?php echo number_format((float)$total_amount, 2); ?></p>
        </div>
        <div class="spinner"></div>
        <p style="color:#777;">Please wait...</p>

        <!-- Form is VISIBLE and submitted immediately via JS -->
        <form id="esewaForm" action="<?php echo $esewa_url; ?>" method="POST">
            <input type="hidden" name="amount"                  value="<?php echo $total_amount; ?>">
            <input type="hidden" name="tax_amount"              value="0">
            <input type="hidden" name="total_amount"            value="<?php echo $total_amount; ?>">
            <input type="hidden" name="transaction_uuid"        value="<?php echo $transaction_uuid; ?>">
            <input type="hidden" name="product_code"            value="<?php echo $product_code; ?>">
            <input type="hidden" name="product_service_charge"  value="0">
            <input type="hidden" name="product_delivery_charge" value="0">
            <input type="hidden" name="success_url"             value="<?php echo $success_url; ?>">
            <input type="hidden" name="failure_url"             value="<?php echo $failure_url; ?>">
            <input type="hidden" name="signed_field_names"      value="total_amount,transaction_uuid,product_code">
            <input type="hidden" name="signature"               value="<?php echo $signature; ?>">
            <!-- Backwards-compatible fields (older eSewa integrations expect these names) -->
            <input type="hidden" name="pid"                    value="<?php echo $transaction_uuid; ?>">
            <input type="hidden" name="scd"                    value="<?php echo $product_code; ?>">
            <input type="hidden" name="amt"                    value="<?php echo $total_amount; ?>">
            <input type="hidden" name="tAmt"                   value="<?php echo $total_amount; ?>">
            <input type="hidden" name="su"                     value="<?php echo $success_url; ?>">
            <input type="hidden" name="fu"                     value="<?php echo $failure_url; ?>">
            <input type="hidden" name="psc"                    value="0">
            <input type="hidden" name="pdc"                    value="0">
            <!-- Fallback button in case JS is slow -->
            <button type="submit" class="btn btn-success" style="margin-top:16px;">
                Click here if not redirected automatically
            </button>
        </form>

        <a href="esewa_cancel.php" style="color:#aaa; font-size:13px; margin-top:18px; display:block;">Cancel Payment / Refund</a>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script>
        // Submit immediately - no delay
        window.onload = function() {
            document.getElementById('esewaForm').submit();
        };
    </script>
</body>
</html>