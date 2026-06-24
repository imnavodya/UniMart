<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product($conn);

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

$products = $productModel->searchProducts($query);

$results = [];
foreach ($products as $p) {
    $results[] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'formatted_price' => formatPrice($p['price']),
        'image_url' => getProductImage($p['image'])
    ];
}

echo json_encode(['success' => true, 'data' => $results]);
exit;
