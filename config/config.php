<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bytestore";

// Application base URL (absolute). Update this to your site domain when deploying.
$BASE_URL = 'http://localhost/bytestore';

// eSewa integration settings. Change to production values when ready.
$ESEWA_PRODUCT_CODE = 'EPAYTEST';
$ESEWA_SECRET_KEY   = '8gBm/:&EnhH.1/q';
$ESEWA_FORM_URL     = 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';
$ESEWA_STATUS_URL   = 'https://rc.esewa.com.np/api/epay/transaction/status/';
$ESEWA_REFUND_URL   = ''; // Optional: set to eSewa refund endpoint if available

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
