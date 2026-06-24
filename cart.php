<?php 
require_once 'includes/header.php'; 

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header("Location: cart.php");
    exit;
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
        $p['cart_quantity'] = $qty;
        $p['subtotal'] = $subtotal;
        $cartItems[] = $p;
    }
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container">
        <div class="mb-5" data-aos="fade-down">
            <h1 class="outfit fw-bold fs-1">Shopping <span class="text-gradient-primary">Cart</span></h1>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="glass-panel p-5 text-center" data-aos="fade-up">
                <div class="fs-1 text-muted mb-4"><i class="fas fa-shopping-cart"></i></div>
                <h3 class="outfit fw-bold text-main mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                <a href="/UniMart/products.php" class="btn btn-glow">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8" data-aos="fade-right">
                    <div class="glass-panel p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless text-main align-middle mb-0">
                                <thead>
                                    <tr class="border-bottom border-secondary text-muted outfit">
                                        <th scope="col" colspan="2">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col" class="text-end">Subtotal</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                    <tr class="border-bottom border-secondary border-opacity-25">
                                        <td style="width: 80px;">
                                            <div class="bg-white bg-opacity-10 rounded p-1" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                <img src="<?= getProductImage($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="outfit fw-bold mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                        </td>
                                        <td><?= formatPrice($item['price']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill"><?= $item['cart_quantity'] ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-accent"><?= formatPrice($item['subtotal']) ?></td>
                                        <td class="text-end">
                                            <a href="cart.php?remove=<?= $item['id'] ?>" class="text-danger fs-5 hover-opacity-100 opacity-75 transition"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="glass-panel p-4 sticky-top" style="top: 100px;">
                        <h4 class="outfit fw-bold text-main mb-4">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Subtotal</span>
                            <span class="text-main"><?= formatPrice($total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Shipping</span>
                            <span class="text-success">Free (Campus)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 text-muted">
                            <span>Tax</span>
                            <span class="text-main"><?= formatPrice($total * 0.05) ?></span>
                        </div>
                        
                        <hr class="border-secondary opacity-50 mb-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold text-main outfit">Total</span>
                            <span class="fs-4 fw-bold text-accent outfit"><?= formatPrice($total + ($total * 0.05)) ?></span>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                            <a href="/UniMart/checkout.php" class="btn btn-glow w-100 py-3 fs-5">Proceed to Checkout</a>
                        <?php else: ?>
                            <a href="/UniMart/auth/login.php?redirect=/UniMart/checkout.php" class="btn btn-outline-glow w-100 py-3 fs-5">Login to Checkout</a>
                        <?php endif; ?>
                        
                        <div class="mt-4 text-center">
                            <i class="fas fa-lock text-success me-2"></i> <span class="text-muted small">Secure encrypted checkout</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
