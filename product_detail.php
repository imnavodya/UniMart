<?php 
require_once 'includes/header.php'; 

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container">
        <div class="glass-panel p-5" data-aos="zoom-in">
            <div class="row align-items-center">
                <div class="col-md-6 mb-5 mb-md-0">
                    <div class="product-img-wrapper" style="height: 400px;">
                        <img src="<?= getProductImage($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 100%; object-fit: contain;">
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill"><?= htmlspecialchars($product['category_name']) ?></span>
                    <nav class="ecommerce-breadcrumb mb-3" aria-label="breadcrumb">
                        <a href="/UniMart/index.php">Home</a>
                        <span class="separator">/</span>
                        <a href="/UniMart/products.php">Products</a>
                        <span class="separator">/</span>
                        <span class="current"><?= htmlspecialchars($product['category_name']) ?></span>
                    </nav>
                    <h1 class="outfit fw-bold text-main mb-3 fs-1"><?= htmlspecialchars($product['name']) ?></h1>
                    <h2 class="text-accent fs-2 mb-4 outfit fw-bold"><?= formatPrice($product['price']) ?></h2>
                    
                    <p class="text-muted mb-4 lh-lg fs-5">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>
                    
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="text-muted"><i class="fas fa-box text-primary me-2"></i> Stock: </span>
                        <span class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                            <?= $product['stock'] > 0 ? $product['stock'] . ' Available' : 'Out of Stock' ?>
                        </span>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                    <form action="/UniMart/api/add_to_cart.php" method="POST" class="d-flex gap-3">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control bg-white text-main border-secondary border-opacity-25 text-center" style="width: 80px;">
                        <button type="submit" class="btn btn-glow flex-grow-1 fs-5 py-3"><i class="fas fa-cart-plus me-2"></i> Add to Cart</button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn-outline-glow w-100 fs-5 py-3" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
