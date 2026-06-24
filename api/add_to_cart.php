<?php
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId > 0 && $quantity > 0) {
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/UniMart/cart.php';
header("Location: $referer");
exit;
?>
