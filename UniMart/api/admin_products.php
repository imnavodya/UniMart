<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product($conn);

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);

            if (empty($name) || !$categoryId || $price <= 0) {
                throw new Exception('Name, Category, and a valid Price are required.');
            }

            $imageName = 'default.jpg';
            
            if ($action === 'update') {
                if (!$id) throw new Exception('Product ID is required for updating.');
                $existingProduct = $productModel->getById($id);
                $imageName = $existingProduct['image'] ?: 'default.jpg';
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception('Invalid image format. Allowed formats: JPG, PNG, GIF, WEBP.');
                }
                if ($file['size'] > 2 * 1024 * 1024) {
                    throw new Exception('Image size must be less than 2MB.');
                }
                
                $uploadDir = __DIR__ . '/../assets/img/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = 'prod_' . time() . '_' . rand(100,999) . '.' . $ext;
                
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                    if ($action === 'update' && $imageName !== 'default.jpg' && file_exists($uploadDir . $imageName)) {
                        unlink($uploadDir . $imageName);
                    }
                    $imageName = $newFileName;
                }
            }

            if ($action === 'create') {
                $productModel->create($categoryId, $name, $description, $price, $imageName, $stock);
                echo json_encode(['success' => true, 'message' => 'Product created successfully']);
            } else {
                $productModel->update($id, $categoryId, $name, $description, $price, $imageName, $stock);
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            }
            exit;

        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('Product ID is required');

            $existingProduct = $productModel->getById($id);
            $imageName = $existingProduct ? $existingProduct['image'] : null;

            $productModel->delete($id);

            if ($imageName && $imageName !== 'default.jpg') {
                $path = __DIR__ . '/../assets/img/products/' . $imageName;
                if (file_exists($path)) unlink($path);
            }
            
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
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
