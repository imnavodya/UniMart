<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function formatPrice($price) {
    return 'LKR ' . number_format($price, 2);
}

function getProductImage($imageName) {
    $path = 'assets/img/products/' . $imageName;
    if (file_exists(__DIR__ . '/../' . $path) && !empty($imageName)) {
        return '/UniMart/' . $path;
    }

    return 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80';
}

function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getUserAvatar($avatarFile = null, $name = '') {
    if ($avatarFile && file_exists(__DIR__ . '/../assets/uploads/avatars/' . $avatarFile)) {
        return '/UniMart/assets/uploads/avatars/' . $avatarFile;
    }

    return null;
}

function getUserInitials($name) {
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= strtoupper(mb_substr($p, 0, 1));
    }
    return $initials ?: '?';
}

function isStudentEmail($email) {
    $allowedDomains = ['nsbm.ac.lk', 'students.nsbm.ac.lk', 'unimart.com'];
    $parts = explode('@', $email);
    if (count($parts) !== 2) return false;
    
    $domain = strtolower(trim($parts[1]));
    
    foreach ($allowedDomains as $allowed) {
        if ($domain === $allowed || str_ends_with($domain, '.' . $allowed)) {
            return true;
        }
    }
    return false;
}
?>
