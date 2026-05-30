<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($q) < 2) {
    jsonResponse(['products' => [], 'categories' => [], 'brands' => []]);
}

$q_esc = mysqli_real_escape_string($conn, $q);
$like = '%' . $q_esc . '%';

$products = [];
$p_sql = "SELECT product_id, product_name, product_price, product_image_path, brand, category_id
    FROM product
    WHERE product_name LIKE '$like' OR product_description LIKE '$like' OR brand LIKE '$like'
    ORDER BY CASE WHEN product_name LIKE '" . $q_esc . "%' THEN 0 ELSE 1 END, rating_avg DESC
    LIMIT 8";
$p_res = mysqli_query($conn, $p_sql);
if ($p_res) {
    while ($row = mysqli_fetch_assoc($p_res)) {
        $row['product_image_path'] = resolveProductImage($row['product_image_path']);
        $products[] = $row;
    }
}

$categories = [];
$c_sql = "SELECT c.category_id, c.category_name, COUNT(p.product_id) as product_count
    FROM category c LEFT JOIN product p ON c.category_id = p.category_id
    WHERE c.category_name LIKE '$like'
    GROUP BY c.category_id LIMIT 5";
$c_res = mysqli_query($conn, $c_sql);
if ($c_res) while ($row = mysqli_fetch_assoc($c_res)) $categories[] = $row;

$brands = [];
$b_sql = "SELECT DISTINCT brand FROM product WHERE brand LIKE '$like' AND brand IS NOT NULL AND brand != '' LIMIT 5";
$b_res = mysqli_query($conn, $b_sql);
if ($b_res) while ($row = mysqli_fetch_assoc($b_res)) $brands[] = $row['brand'];

jsonResponse(['products' => $products, 'categories' => $categories, 'brands' => $brands]);
