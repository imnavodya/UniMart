<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);

$avatarUrl = null;
$initials = '?';
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT avatar, name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch();
    $avatarUrl = getUserAvatar($u['avatar'] ?? null);
    $initials = getUserInitials($u['name'] ?? $_SESSION['name']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMart - Premium Marketplace for NSBM</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="/UniMart/assets/css/style.css?v=<?= time() ?>">
</head>
<body>


    <nav class="navbar navbar-expand-lg navbar-dark navbar-floating">
        <div class="container-fluid">
            <a class="navbar-brand outfit fw-bold text-gradient-primary fs-3" href="/UniMart/index.php">UniMart</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="/UniMart/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>" href="/UniMart/products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'categories.php' ? 'active' : '' ?>" href="/UniMart/categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'index.php' ? '' : '' ?>" href="/UniMart/index.php#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'support.php' ? 'active' : '' ?>" href="/UniMart/support.php">Support</a>
                    </li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <a href="#" data-bs-toggle="modal" data-bs-target="#searchModal" class="text-main text-decoration-none fs-5 hover-opacity-100 opacity-75 transition">
                    <i class="fas fa-search"></i>
                </a>

                <?php if (isLoggedIn()): ?>
                <a href="#" data-bs-toggle="offcanvas" data-bs-target="#notificationOffcanvas" class="text-main text-decoration-none fs-5 position-relative hover-opacity-100 opacity-75 transition mx-2">
                    <i class="far fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" id="notificationBadge">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </a>
                <?php endif; ?>
                
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <a class="text-main text-decoration-none hover-opacity-100 opacity-75 transition" href="#" role="button" data-bs-toggle="dropdown">
                            <?php if ($avatarUrl): ?>
                                <img src="<?= $avatarUrl ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid var(--primary);">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary bg-opacity-25 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: bold; border: 1px solid var(--primary);">
                                    <?= $initials ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-panel border-0 mt-3">
                            <li class="px-3 py-2 text-muted small">Signed in as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="/UniMart/admin/index.php"><i class="fas fa-shield-alt me-2 text-muted"></i>Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/UniMart/user/index.php"><i class="fas fa-layer-group me-2 text-muted"></i>My Dashboard</a></li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <li><a class="dropdown-item text-danger" href="/UniMart/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/UniMart/auth/login.php" class="text-main text-decoration-none fs-5 hover-opacity-100 opacity-75 transition">
                        <i class="far fa-user"></i>
                    </a>
                <?php endif; ?>

                <a href="#" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" class="text-main text-decoration-none fs-5 position-relative hover-opacity-100 opacity-75 transition">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6rem; padding: 0.25em 0.5em;">
                            <?= count($_SESSION['cart']) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>
