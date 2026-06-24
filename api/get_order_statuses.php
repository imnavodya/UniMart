<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, status FROM orders WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'statuses' => $statuses]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching statuses']);
}
