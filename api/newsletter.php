<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = sanitizeEmail($input['email'] ?? '');

if (!validateEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Please enter a valid email address'], 400);
}

$email_esc = mysqli_real_escape_string($conn, $email);
$check = mysqli_query($conn, "SELECT subscriber_id FROM newsletter WHERE email = '$email_esc' LIMIT 1");
if ($check && mysqli_num_rows($check) > 0) {
    jsonResponse(['success' => true, 'message' => 'You are already subscribed!']);
}

mysqli_query($conn, "INSERT INTO newsletter (email) VALUES ('$email_esc')");
jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
