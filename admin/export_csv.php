<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$type = $_GET['type'] ?? '';

if ($type === 'orders') {
    $filename = "orders_export_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Customer Name', 'Customer Email', 'Total Amount', 'Status', 'Date']);
    
    $stmt = $conn->query("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            'UM-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
            $row['customer_name'],
            $row['email'],
            $row['total_amount'],
            $row['status'],
            $row['created_at']
        ]);
    }
    fclose($output);
    exit;
} elseif ($type === 'products') {
    $filename = "products_export_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Product ID', 'Name', 'Category', 'Price', 'Stock']);
    
    $stmt = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            'UM-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
            $row['name'],
            $row['category_name'],
            $row['price'],
            $row['stock']
        ]);
    }
    fclose($output);
    exit;
} else {
    die("Invalid export type.");
}
