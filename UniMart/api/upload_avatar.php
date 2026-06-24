<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['avatar'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['avatar'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 2 * 1024 * 1024; 

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Use JPG, PNG, GIF, or WebP.']);
    exit;
}
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max 2MB.']);
    exit;
}

$uploadDir = __DIR__ . '/../assets/uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$old = $stmt->fetchColumn();
if ($old && file_exists($uploadDir . $old)) {
    unlink($uploadDir . $old);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$destPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->execute([$filename, $_SESSION['user_id']]);
$_SESSION['avatar'] = $filename;

echo json_encode(['success' => true, 'avatar_url' => '/UniMart/assets/uploads/avatars/' . $filename]);
exit;
