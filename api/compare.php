<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? 'add';
$product_id = sanitizeInt($input['product_id'] ?? 0);

if ($action === 'clear') {
    $_SESSION['compare'] = [];
    jsonResponse(['success' => true, 'message' => 'Compare list cleared', 'count' => 0]);
}

if ($action === 'remove') {
    if ($product_id <= 0) {
        jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
    }
    removeFromCompare($product_id);
    jsonResponse(['success' => true, 'message' => 'Removed from compare', 'count' => count(getCompareList())]);
}

if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
}

$compare = getCompareList();

if ($action === 'add') {
    if (in_array($product_id, $compare, true)) {
        jsonResponse(['success' => false, 'message' => 'Product already in compare list', 'count' => count($compare)]);
    }
    if (count($compare) >= 4) {
        jsonResponse(['success' => false, 'message' => 'Maximum 4 products for comparison. Remove one first.', 'count' => count($compare)]);
    }
    addToCompare($product_id);
    jsonResponse(['success' => true, 'message' => 'Added to compare list', 'count' => count(getCompareList())]);
}

jsonResponse(['success' => false, 'message' => 'Unknown action'], 400);
