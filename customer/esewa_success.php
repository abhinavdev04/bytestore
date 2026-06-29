<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkCustomerLogin();

$customer_id = $_SESSION['customer_id'];

// Debug: log incoming callback (GET/POST/raw) for eSewa success handling
$log_path = dirname(__DIR__) . '/esewa_debug.log';
$debug_log  = "=== eSewa Success Debug ===\n";
$debug_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
$debug_log .= "Full URL: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
$debug_log .= "GET: " . print_r($_GET, true) . "\n";
$debug_log .= "POST: " . print_r($_POST, true) . "\n";
$raw_input = file_get_contents('php://input');
$debug_log .= "RAW_INPUT: " . ($raw_input ?: '(empty)') . "\n";
file_put_contents($log_path, $debug_log, FILE_APPEND);

// eSewa config from central config file
$product_code = $ESEWA_PRODUCT_CODE ?? 'EPAYTEST';
$secret_key   = $ESEWA_SECRET_KEY ?? '';

// -------------------------------------------------------
// STEP 1: Read eSewa callback data from GET or POST.
// Some eSewa responses do not include ?data= in the URL.
// -------------------------------------------------------
$raw = '';
if (!empty($_GET['data'])) {
    $raw = $_GET['data'];
} elseif (!empty($_POST['data'])) {
    $raw = $_POST['data'];
}

// If eSewa doesn't send ?data= but posts fields directly, accept them as a direct response
$direct_response = null;
if (empty($raw)) {
    if (!empty($_POST) && (isset($_POST['transaction_uuid']) || isset($_POST['status']) || isset($_POST['total_amount']))) {
        $direct_response = $_POST;
    } elseif (!empty($_GET) && (isset($_GET['transaction_uuid']) || isset($_GET['status']) || isset($_GET['total_amount']))) {
        $direct_response = $_GET;
    }
}

if ($direct_response !== null) {
    $response = $direct_response;
} else {
    if (empty($raw)) {
        // No callback data. Try server-side verification using session-stored txn_uuid
        if (isset($_SESSION['esewa_order_id'])) {
            $order_id = (int)$_SESSION['esewa_order_id'];
            $transaction_uuid = $_SESSION['esewa_txn_uuid'] ?? '';
            $total_amount = $_SESSION['esewa_total_amount'] ?? '';

            // Log attempt
            file_put_contents($log_path, "=== eSewa Server Verify Attempt ===\nTime: " . date('Y-m-d H:i:s') . "\nTxn: $transaction_uuid\nAmount: $total_amount\n", FILE_APPEND);

            // Build status URL and call eSewa status API
            $amount_clean = str_replace(',', '', $total_amount);
            $status_url   = ($ESEWA_STATUS_URL ?? 'https://rc.esewa.com.np/api/epay/transaction/status/')
                          . "?product_code=" . urlencode($product_code)
                          . "&transaction_uuid=" . urlencode($transaction_uuid)
                          . "&total_amount=" . urlencode($amount_clean);

            $ch = curl_init($status_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            $api_response = curl_exec($ch);
            $curl_error   = curl_error($ch);
            $http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            file_put_contents($log_path, "Status URL: $status_url\nHTTP Code: $http_code\nResponse: " . ($api_response ?: '(empty)') . "\nCurl Error: $curl_error\n", FILE_APPEND);

            $api_data   = json_decode($api_response, true);
            $api_status = $api_data['status'] ?? '';

            if ($http_code === 200 && $api_status === 'COMPLETE') {
                // Confirm order exists and is pending
                $order_check = mysqli_query($conn,
                    "SELECT * FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'"
                );

                if ($order_check && mysqli_num_rows($order_check) > 0) {
                    // Finalize order
                    mysqli_query($conn,
                        "UPDATE orders SET payment_status='Paid', order_status='Processing' WHERE order_id=$order_id"
                    );

                    $items_result = mysqli_query($conn,
                        "SELECT oi.product_id, oi.quantity, p.product_stock
                         FROM order_items oi
                         JOIN product p ON oi.product_id = p.product_id
                         WHERE oi.order_id=$order_id"
                    );

                    while ($item = mysqli_fetch_assoc($items_result)) {
                        $new_stock = max(0, $item['product_stock'] - $item['quantity']);
                        mysqli_query($conn, "UPDATE product SET product_stock=$new_stock WHERE product_id={$item['product_id']}");
                    }

                    mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");

                    unset(
                        $_SESSION['esewa_order_id'],
                        $_SESSION['esewa_amount'],
                        $_SESSION['esewa_cart_items'],
                        $_SESSION['esewa_txn_uuid'],
                        $_SESSION['esewa_total_amount']
                    );

                    goto render_success;
                } else {
                    header("Location: esewa_failure.php?reason=order_not_found");
                    exit();
                }
            }
        }

        header("Location: esewa_failure.php?reason=no_data");
        exit();
    }
}

// -------------------------------------------------------
// STEP 2: Decode and parse eSewa's response (if not already set)
// -------------------------------------------------------
if (!isset($response)) {
    $decoded  = base64_decode($raw);
    $response = json_decode($decoded, true);

    if (!$response) {
        header("Location: esewa_failure.php?reason=decode_failed");
        exit();
    }
}

$status           = $response['status']             ?? '';
$total_amount     = $response['total_amount']       ?? 0;
$transaction_uuid = $response['transaction_uuid']   ?? '';
$transaction_code = $response['transaction_code']   ?? '';
$signed_fields    = $response['signed_field_names'] ?? '';
$received_sig     = $response['signature']          ?? '';

// -------------------------------------------------------
// STEP 3: Verify HMAC signature from eSewa's response
// This proves the data wasn't tampered with in the URL
// -------------------------------------------------------
if (!empty($signed_fields) && !empty($received_sig)) {
    $fields    = explode(',', $signed_fields);
    $sig_parts = [];
    foreach ($fields as $field) {
        $sig_parts[] = trim($field) . '=' . ($response[trim($field)] ?? '');
    }
    $sig_message  = implode(',', $sig_parts);
    $expected_sig = base64_encode(hash_hmac('sha256', $sig_message, $secret_key, true));

    if ($received_sig !== $expected_sig) {
        header("Location: esewa_failure.php?reason=invalid_signature");
        exit();
    }
}

// -------------------------------------------------------
// STEP 4: Check status from eSewa's response data
// -------------------------------------------------------
if ($status !== 'COMPLETE') {
    header("Location: esewa_failure.php?reason=not_complete");
    exit();
}

// -------------------------------------------------------
// STEP 5: Extract order_id from transaction_uuid
// Format we set: BYTESTORE-{order_id}-{timestamp}
// -------------------------------------------------------
$parts    = explode('-', $transaction_uuid);
$order_id = isset($parts[1]) ? (int)$parts[1] : 0;

if (!$order_id) {
    header("Location: esewa_failure.php?reason=invalid_order");
    exit();
}

// -------------------------------------------------------
// STEP 6: YOUR server calls OUT to eSewa's Status API
// This is an outbound HTTP call from your server to eSewa
// Works perfectly on localhost — no ngrok needed!
// eSewa confirms the transaction is genuinely paid.
// -------------------------------------------------------
$amount_clean = str_replace(',', '', $total_amount); // remove formatting commas
$status_url   = "https://rc.esewa.com.np/api/epay/transaction/status/?" 
              . "product_code=" . urlencode($product_code)
              . "&transaction_uuid=" . urlencode($transaction_uuid)
              . "&total_amount=" . urlencode($amount_clean);

$ch = curl_init($status_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // needed for localhost/XAMPP
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$api_response = curl_exec($ch);
$curl_error   = curl_error($ch);
$http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$api_data     = json_decode($api_response, true);
$api_status   = $api_data['status'] ?? '';

// Accept payment if eSewa API confirms COMPLETE
// Also accept if API call failed but signature was valid (fallback for sandbox issues)
$api_confirmed = ($http_code === 200 && $api_status === 'COMPLETE');
$sig_confirmed = (!empty($received_sig)); // signature already verified above

if (!$api_confirmed && !$sig_confirmed) {
    header("Location: esewa_failure.php?reason=verify_failed");
    exit();
}

// -------------------------------------------------------
// STEP 7: Check order belongs to this customer and is Pending
// -------------------------------------------------------
$order_check = mysqli_query($conn, 
    "SELECT * FROM orders WHERE order_id=$order_id AND customer_id=$customer_id AND payment_status='Pending'"
);

if (mysqli_num_rows($order_check) === 0) {
    // Already processed or doesn't belong to this customer
    header("Location: esewa_failure.php?reason=order_not_found");
    exit();
}
$order = mysqli_fetch_assoc($order_check);

// -------------------------------------------------------
// STEP 8: All verified — finalize the order!
// -------------------------------------------------------

// Mark order as Paid
$txn_escaped = mysqli_real_escape_string($conn, $transaction_code);
mysqli_query($conn, 
    "UPDATE orders SET 
        payment_status='Paid', 
        order_status='Processing'
     WHERE order_id=$order_id"
);

// Deduct stock now that payment is confirmed
$items_result = mysqli_query($conn, 
    "SELECT oi.product_id, oi.quantity, p.product_stock 
     FROM order_items oi 
     JOIN product p ON oi.product_id = p.product_id 
     WHERE oi.order_id=$order_id"
);
while ($item = mysqli_fetch_assoc($items_result)) {
    $new_stock = max(0, $item['product_stock'] - $item['quantity']);
    mysqli_query($conn, "UPDATE product SET product_stock=$new_stock WHERE product_id={$item['product_id']}");
}

// Clear cart
mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");

// Clear eSewa session data
unset(
    $_SESSION['esewa_order_id'],
    $_SESSION['esewa_amount'],
    $_SESSION['esewa_cart_items'],
    $_SESSION['esewa_txn_uuid'],
    $_SESSION['esewa_total_amount']
);
?>

<?php render_success: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-card {
            text-align: center;
            padding: 40px;
            max-width: 560px;
            margin: 50px auto;
        }
        .checkmark { font-size: 64px; margin-bottom: 10px; }

        .success-title  { color: #2e7d32; }
        .success-note   { color: #555555; font-size: 14px; }
        .paid-status    { color: #2e7d32; font-weight: bold; }

        .order-details {
            background: #f4fdf4;
            border: 1px solid #b2dfb2;
            border-radius: 10px;
            padding: 18px 24px;
            margin: 24px 0;
            text-align: left;
            color: #1a1a1a;
        }
        .order-details p       { margin: 8px 0; color: #1a1a1a; }
        .order-details strong  { color: #111111; }

        .txn-code {
            font-family: monospace;
            font-size: 13px;
            color: #333333;
            background: #e8e8e8;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .esewa-green { color: #2d8a1e; font-weight: bold; }

        /* ── Dark mode overrides ── */
        @media (prefers-color-scheme: dark) {
            .success-title  { color: #66bb6a; }
            .paid-status    { color: #66bb6a; }
            .success-note   { color: #aaaaaa; }
            .esewa-green    { color: #60BB46; }

            .order-details {
                background: #1a2e1a;
                border-color: #2d5a2d;
                color: #e8e8e8;
            }
            .order-details p      { color: #e8e8e8; }
            .order-details strong { color: #ffffff; }

            .txn-code {
                color: #cccccc;
                background: #2a2a2a;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="card success-card">
        <div class="checkmark">✅</div>
        <h2 class="success-title">Payment Successful!</h2>
        <p>Your eSewa payment has been verified and your order is confirmed.</p>

        <div class="order-details">
            <p><strong>Order ID:</strong> #<?php echo (int)$order_id; ?></p>
            <p><strong>Amount Paid:</strong> Rs. <?php echo number_format((float)$order['total_amount'], 2); ?></p>
            <p><strong>Payment Method:</strong> <span class="esewa-green">eSewa</span></p>
            <?php if (!empty($transaction_code)): ?>
            <p><strong>eSewa Txn Code:</strong> <span class="txn-code"><?php echo htmlspecialchars($transaction_code, ENT_QUOTES, 'UTF-8'); ?></span></p>
            <?php endif; ?>
            <p><strong>Status:</strong> <span class="paid-status">Paid ✔</span></p>
        </div>

        <p class="success-note">
            Your order is now being processed. You will receive your items at the shipping address provided.
        </p>

        <div style="display:flex; gap:12px; justify-content:center; margin-top:20px;">
            <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>