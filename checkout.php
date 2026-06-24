<?php 
require_once 'includes/header.php'; 
requireLogin();

if (empty($_SESSION['cart'])) {
    header("Location: /UniMart/cart.php");
    exit;
}

$total = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $conn->query("SELECT id, price FROM products WHERE id IN ($ids)");
$products = $stmt->fetchAll();

foreach ($products as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    $total += $p['price'] * $qty;
}

$finalTotal = $total + ($total * 0.05);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$_SESSION['user_id'], $finalTotal]);
        $orderId = $conn->lastInsertId();
        
        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $itemStmt->execute([$orderId, $p['id'], $qty, $p['price']]);
            
            $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $p['id']]);
        }
        
        $conn->commit();
        $_SESSION['cart'] = [];
        $success = true;
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Checkout failed. Please try again.";
    }
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container d-flex justify-content-center align-items-center h-100">
        <?php if (isset($success)): ?>
            <div class="glass-panel p-5 text-center" data-aos="zoom-in" style="max-width: 600px;">
                <div class="fs-1 text-success mb-4"><i class="fas fa-check-circle"></i></div>
                <h2 class="outfit fw-bold text-main mb-3">Payment Successful!</h2>
                <p class="text-muted mb-4 fs-5">Thank you for your purchase. Your order has been placed successfully and will be delivered to your campus location soon.</p>
                <div class="glass-card p-3 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order ID:</span>
                        <span class="text-main fw-bold">#UM-<?= str_pad($orderId, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Amount Paid:</span>
                        <span class="text-accent fw-bold"><?= formatPrice($finalTotal) ?></span>
                    </div>
                </div>
                <a href="/UniMart/index.php" class="btn btn-glow w-100 py-3">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="glass-panel p-5 w-100" data-aos="zoom-in" style="max-width: 600px;">
                <h2 class="outfit fw-bold text-main mb-4 text-center">Checkout Simulation</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger border-0 rounded-4">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label text-muted">Card Number</label>
                        <input type="text" class="form-control bg-white text-main border-secondary border-opacity-25 px-4 py-3" value="**** **** **** 4242" readonly>
                        <div class="form-text text-muted mt-2"><i class="fas fa-info-circle me-1"></i> This is a simulation. No real payment will be processed.</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label text-muted">Expiry</label>
                            <input type="text" class="form-control bg-white text-main border-secondary border-opacity-25 px-4 py-3" value="12/28" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted">CVC</label>
                            <input type="text" class="form-control bg-white text-main border-secondary border-opacity-25 px-4 py-3" value="***" readonly>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-glow w-100 py-3 fs-5 mt-3">Pay <?= formatPrice($finalTotal) ?></button>
                </form>

                <div class="mt-4 pt-4 border-top border-secondary border-opacity-25">
                    <div class="trust-badge">
                        <div class="trust-badge-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="trust-badge-text text-start">
                            <h6>Secure Campus Payment</h6>
                            <p>Your payment is 100% simulated and secure.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
