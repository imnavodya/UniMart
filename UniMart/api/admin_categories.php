<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? 'fas fa-folder');

            if (empty($name)) {
                throw new Exception('Category name is required');
            }

            $stmt = $conn->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
            $stmt->execute([$name, $icon]);
            
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
            exit;

        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? 'fas fa-folder');

            if (!$id || empty($name)) {
                throw new Exception('Category ID and name are required');
            }

            $stmt = $conn->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
            $stmt->execute([$name, $icon, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            exit;

        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            
            if (!$id) {
                throw new Exception('Category ID is required');
            }

            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            exit;

        } else {
            throw new Exception('Invalid action');
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
