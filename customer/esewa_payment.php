<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

// Make sure order data exists in session
if (!isset($_SESSION['esewa_order_id']) || !isset($_SESSION['esewa_amount'])) {
    header("Location: checkout.php");
    exit();
}

$order_id  = $_SESSION['esewa_order_id'];
$amount    = $_SESSION['esewa_amount'];

// -------------------------------------------------------
// eSewa Sandbox (Test) Configuration
// For production replace these with your real credentials
// -------------------------------------------------------
$product_code    = "EPAYTEST";           // Merchant code — use your real one in production
$secret_key      = "8gBm/:&EnhH.1/q";   // Secret key   — use your real one in production
$esewa_url       = "https://rc-epay.esewa.com.np/api/epay/main/v2/form"; // Sandbox URL

// Breakdown — eSewa requires these fields separately
// For simplicity we treat the full amount as product_amount with 0 for all taxes/charges
$product_amount  = $amount;    // actual product price total
$tax_amount      = 0;
$service_charge  = 0;
$delivery_charge = 0;
$total_amount    = $amount;    // must equal product + tax + service + delivery

// Unique transaction UUID — combines order_id with a timestamp to ensure uniqueness
$transaction_uuid = "BYTESTORE-" . $order_id . "-" . time();

// Store UUID in session so success page can verify it
$_SESSION['esewa_txn_uuid'] = $transaction_uuid;

// Callback URLs (must be full absolute URLs accessible by eSewa's servers)
// For local testing with XAMPP: use your actual LAN IP, e.g. http://192.168.x.x/bytestore/...
// For production: use your real domain
$base_url        = "http://localhost/bytestore";   // <-- change this to your domain in production
$success_url = $base_url . "/customer/esewa_debug.php";
$failure_url = $base_url . "/customer/esewa_debug.php";

// -------------------------------------------------------
// Generate HMAC-SHA256 signature
// eSewa v2 requires: total_amount,transaction_uuid,product_code
// -------------------------------------------------------
$message   = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
$signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .redirect-card {
            text-align: center;
            padding: 40px;
            max-width: 500px;
            margin: 60px auto;
        }
        .esewa-logo-big {
            font-size: 42px;
            font-weight: 900;
            color: #60BB46;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .spinner {
            width: 48px; height: 48px;
            border: 5px solid #ddd;
            border-top-color: #60BB46;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .order-info { background: #f9f9f9; border-radius: 8px; padding: 14px 20px; margin: 20px 0; text-align: left; }
        .order-info p { margin: 4px 0; }
        .cancel-link { color: #aaa; font-size: 13px; margin-top: 18px; display: block; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="card redirect-card">
        <div class="esewa-logo-big">eSewa</div>
        <h3>Redirecting to eSewa Payment...</h3>

        <div class="order-info">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Amount:</strong> Rs. <?php echo number_format($total_amount, 2); ?></p>
        </div>

        <div class="spinner"></div>
        <p style="color:#777;">Please wait, you are being redirected to eSewa for secure payment.</p>
        <p style="color:#aaa; font-size:13px;">Do not close this window.</p>

        <!-- eSewa Payment Form — auto-submits via JS -->
        <form id="esewaForm" action="<?php echo $esewa_url; ?>" method="POST" style="display:none;">
            <input type="hidden" name="amount"           value="<?php echo $product_amount; ?>">
            <input type="hidden" name="tax_amount"       value="<?php echo $tax_amount; ?>">
            <input type="hidden" name="total_amount"     value="<?php echo $total_amount; ?>">
            <input type="hidden" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>">
            <input type="hidden" name="product_code"     value="<?php echo $product_code; ?>">
            <input type="hidden" name="product_service_charge" value="<?php echo $service_charge; ?>">
            <input type="hidden" name="product_delivery_charge" value="<?php echo $delivery_charge; ?>">
            <input type="hidden" name="success_url"      value="<?php echo $success_url; ?>">
            <input type="hidden" name="failure_url"      value="<?php echo $failure_url; ?>">
            <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
            <input type="hidden" name="signature"        value="<?php echo $signature; ?>">
        </form>

        <a class="cancel-link" href="checkout.php">Cancel and go back</a>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Auto-submit the eSewa form after 1.5 seconds
        setTimeout(function () {
            document.getElementById('esewaForm').submit();
        }, 1500);
    </script>
</body>
</html>