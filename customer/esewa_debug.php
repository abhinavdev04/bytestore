<?php
session_start();

// TEMPORARY DEBUG FILE - DELETE AFTER TESTING
// Place this at: customer/esewa_debug.php
// Then in esewa_payment.php temporarily change success_url to point here

echo "<h2>eSewa Debug - Raw Response</h2>";
echo "<h3>GET params:</h3><pre>";
print_r($_GET);
echo "</pre>";

echo "<h3>POST params:</h3><pre>";
print_r($_POST);
echo "</pre>";

echo "<h3>Session:</h3><pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_GET['data'])) {
    $decoded = base64_decode($_GET['data']);
    echo "<h3>Decoded 'data' field:</h3><pre>";
    print_r(json_decode($decoded, true));
    echo "</pre>";
}
?>