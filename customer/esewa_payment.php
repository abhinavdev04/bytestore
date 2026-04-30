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

// eSewa Sandbox config
$product_code = "EPAYTEST";
$secret_key   = "8gBm/:&EnhH.1/q";
$esewa_url    = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";

// Amount must be a plain integer or decimal - NO formatting
$total_amount = number_format((float)$amount, 2, '.', ''); // e.g. "1799.00"

$transaction_uuid = "BYTESTORE-" . $order_id . "-" . time();

$_SESSION['esewa_txn_uuid']     = $transaction_uuid;
$_SESSION['esewa_total_amount'] = $total_amount;

$base_url    = "http://localhost/bytestore";
$success_url = $base_url . "/customer/esewa_success.php";
$failure_url = $base_url . "/customer/esewa_failure.php";

// Signature: must match EXACTLY what you put in signed_field_names
$message   = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
$signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .redirect-card { text-align: center; padding: 40px; max-width: 540px; margin: 60px auto; }
        .esewa-logo-big { font-size: 42px; font-weight: 900; color: #60BB46; margin-bottom: 10px; }
        .spinner { width: 48px; height: 48px; border: 5px solid #ddd; border-top-color: #60BB46; border-radius: 50%; animation: spin 0.9s linear infinite; margin: 20px auto; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .order-info { background: #f9f9f9; border-radius: 8px; padding: 14px 20px; margin: 20px 0; text-align: left; }
        .order-info p { margin: 4px 0; }

        /* DEBUG box - remove after fixing */
        .debug-box { background:#fff8e1; border:1px solid #ffe082; border-radius:8px; padding:14px; margin:20px 0; text-align:left; font-family:monospace; font-size:12px; white-space:pre-wrap; word-break:break-all; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="card redirect-card">
        <div class="esewa-logo-big">eSewa</div>
        <h3>Redirecting to eSewa Payment...</h3>

        <div class="order-info">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Amount:</strong> Rs. <?php echo $total_amount; ?></p>
        </div>

        <!-- DEBUG: show exactly what we're sending -->
        <div class="debug-box"><?php
echo "product_code:     $product_code\n";
echo "total_amount:     $total_amount\n";
echo "transaction_uuid: $transaction_uuid\n";
echo "success_url:      $success_url\n";
echo "failure_url:      $failure_url\n";
echo "sig message:      $message\n";
echo "signature:        $signature\n";
        ?></div>
        <!-- END DEBUG -->

        <div class="spinner"></div>
        <p style="color:#777;">You will be redirected to eSewa shortly...</p>

        <form id="esewaForm" action="<?php echo $esewa_url; ?>" method="POST" style="display:none;">
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
        </form>

        <a href="checkout.php" style="color:#aaa; font-size:13px; margin-top:18px; display:block;">Cancel and go back</a>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script>
        // Give 4 seconds so you can read the debug box before redirect
        setTimeout(function () {
            document.getElementById('esewaForm').submit();
        }, 4000);
    </script>
</body>
</html>