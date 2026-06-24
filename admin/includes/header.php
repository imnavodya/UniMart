<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$currentPage = basename($_SERVER['PHP_SELF']);
$stmt = $conn->prepare("SELECT avatar, name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$adminUser = $stmt->fetch();
$avatarUrl  = getUserAvatar($adminUser['avatar'] ?? null);
$initials   = getUserInitials($adminUser['name'] ?? 'A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — UniMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/UniMart/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/UniMart/assets/css/admin.css?v=<?= time() ?>">
</head>
<body>

<aside class="adm-sidebar">
    <a href="/UniMart/index.php" class="adm-logo d-block text-decoration-none">
        <div class="adm-logo-text">UniMart<span style="-webkit-text-fill-color:var(--text-main);">.</span></div>
        <div style="font-size:0.65rem; color:var(--text-muted); margin-top:2px; font-family:'Inter',sans-serif;">Admin Panel</div>
    </a>

    <nav class="adm-nav">
        <div class="adm-nav-label">Main</div>
        <a href="/UniMart/admin/index.php" class="adm-nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span> Dashboard
        </a>
        <a href="/UniMart/admin/products.php" class="adm-nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-box-open"></i></span> Products
        </a>
        <a href="/UniMart/admin/categories.php" class="adm-nav-link <?= $currentPage == 'categories.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span> Categories
        </a>
        <a href="/UniMart/admin/orders.php" class="adm-nav-link <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span> Orders
        </a>

        <div class="adm-nav-label">Store</div>
        <a href="/UniMart/index.php" class="adm-nav-link">
            <span class="nav-icon"><i class="fas fa-store"></i></span> View Store
        </a>
    </nav>

    <div class="adm-sidebar-footer">
        <a href="/UniMart/auth/logout.php" class="adm-logout-btn">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout
        </a>
    </div>
</aside>

<header class="adm-topbar">
    <div>
        <div class="adm-topbar-title">
            <?php
            $titles = [
                'index.php'      => 'Dashboard',
                'products.php'   => 'Products',
                'categories.php' => 'Categories',
                'orders.php'     => 'Orders',
            ];
            echo $titles[$currentPage] ?? 'Admin';
            ?>
        </div>
        <div class="adm-topbar-sub">Welcome back, <?= htmlspecialchars($adminUser['name']) ?> 👋</div>
    </div>

    <div class="adm-topbar-actions">
        <a href="#" data-bs-toggle="modal" data-bs-target="#searchModal" class="adm-icon-btn" title="Search">
            <i class="fas fa-search"></i>
        </a>
        <div class="position-relative">
            <button class="adm-icon-btn" title="Notifications" data-bs-toggle="offcanvas" data-bs-target="#notificationOffcanvas">
                <i class="fas fa-bell"></i>
            </button>
            <span class="position-absolute" id="notificationBadge" style="top:-4px;right:-4px;width:8px;height:8px;background:var(--primary);border-radius:50%;border:2px solid #0d1530;"></span>
        </div>
        <div class="dropdown">
            <div class="adm-avatar" id="adminAvatarTrigger">
                <?php if ($avatarUrl): ?>
                    <img src="<?= $avatarUrl ?>" alt="Avatar" id="adminAvatarImg">
                <?php else: ?>
                    <div class="adm-avatar-initials" id="adminAvatarInitials"><?= $initials ?></div>
                <?php endif; ?>
            </div>
            <div style="font-size:0.85rem;font-weight:600;color:var(--text-main);cursor:pointer;margin-left:4px;" data-bs-toggle="dropdown">
                <?= htmlspecialchars($adminUser['name']) ?>
            </div>
            <ul class="dropdown-menu dropdown-menu-end glass-panel border-0 mt-2" style="min-width:180px;">
                <li><a class="dropdown-item" href="/UniMart/index.php"><i class="fas fa-store me-2 text-muted"></i>View Store</a></li>
                <li><hr class="dropdown-divider bg-secondary"></li>
                <li><a class="dropdown-item text-danger" href="/UniMart/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<main class="adm-content">
<div class="adm-page-body">
