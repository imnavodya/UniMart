<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Administrator privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'get_order') {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('Order ID is required');
            $stmt = $conn->prepare("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) throw new Exception('Order not found.');

            $stmt = $conn->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $order['created_at_formatted'] = date('M j, Y g:i A', strtotime($order['created_at']));
            $order['total_formatted'] = formatPrice($order['total_amount']);

            foreach($items as &$item) {
                $item['price_formatted'] = formatPrice($item['price']);
                $item['subtotal_formatted'] = formatPrice($item['price'] * $item['quantity']);
                $item['image_url'] = getProductImage($item['image']);
            }

            echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
            exit;

        } else if ($action === 'update_status') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            if (!$id) throw new Exception('Order ID is required.');
            $allowedStatuses = ['pending', 'completed', 'cancelled'];
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('Invalid status provided.');
            }
            
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            exit;

        } else {
            throw new Exception('Invalid action specified.');
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
