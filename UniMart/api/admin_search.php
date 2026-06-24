<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode(['products' => [], 'categories' => [], 'orders' => []]);
    exit;
}

$searchQuery = "%{$q}%";
$results = [
    'products' => [],
    'categories' => [],
    'orders' => []
];

$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE name LIKE ? LIMIT 5");
$stmt->execute([$searchQuery]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($products as &$p) {
    $p['image_url'] = getProductImage($p['image']);
}
$results['products'] = $products;
$stmt = $conn->prepare("SELECT id, name, icon FROM categories WHERE name LIKE ? LIMIT 5");
$stmt->execute([$searchQuery]);
$results['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("
    SELECT o.id, o.total_amount, o.status, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id LIKE ? OR u.name LIKE ? 
    LIMIT 5
");
$stmt->execute([$searchQuery, $searchQuery]);
$results['orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
