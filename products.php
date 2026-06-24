<?php 
require_once 'includes/header.php'; 

require_once 'classes/Product.php';

$productModel = new Product($conn);

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$products = $productModel->getAll($categoryId, $search);
$categories = $productModel->getCategories();
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px;">
    <div class="container">
        <div class="row align-items-end mb-5" data-aos="fade-up">
            <div class="col-md-7">
                <nav class="ecommerce-breadcrumb" aria-label="breadcrumb">
                    <a href="/UniMart/index.php">Home</a>
                    <span class="separator">/</span>
                    <span class="current">Products</span>
                </nav>
                <h1 class="outfit fw-bold fs-1">Our <span class="text-gradient-primary">Products</span></h1>
                <p class="text-muted mb-0">Browse the finest products available at UniMart</p>
            </div>
            <div class="col-md-5 mt-3 mt-md-0">
                <form action="products.php" method="GET" class="products-search-form position-relative">
                    <?php if ($categoryId): ?>
                        <input type="hidden" name="category" value="<?= $categoryId ?>">
                    <?php endif; ?>
                    <i class="fas fa-search products-search-icon"></i>
                    <input
                        type="text"
                        name="search"
                        class="products-search-input outfit"
                        placeholder="Search products..."
                        value="<?= htmlspecialchars($search) ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="products-search-btn">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 mb-4" data-aos="fade-right">
                <div class="glass-panel p-4 sticky-top" style="top: 100px;">
                    <h5 class="outfit fw-bold text-main mb-4">Categories</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li>
                            <a href="products.php" class="category-list-link <?= !$categoryId ? 'active' : '' ?>">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box"><i class="fas fa-border-all"></i></div>
                                    <span class="fw-medium">All Products</span>
                                </div>
                            </a>
                        </li>
                        <?php foreach($categories as $cat): ?>
                        <li>
                            <a href="products.php?category=<?= $cat['id'] ?>" class="category-list-link <?= $categoryId == $cat['id'] ? 'active' : '' ?>">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box"><i class="<?= htmlspecialchars($cat['icon']) ?>"></i></div>
                                    <span class="fw-medium"><?= htmlspecialchars($cat['name']) ?></span>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row g-4">
                    <?php if (count($products) > 0): ?>
                        <?php foreach($products as $index => $product): ?>
                        <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 ?>">
                            <div class="glass-card h-100 d-flex flex-column">
                                <div class="product-img-wrapper" style="height: 200px;">
                                    <img src="<?= getProductImage($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <h5 class="outfit fw-bold text-main mb-2 fs-6"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="product-price mb-3 fs-5"><?= formatPrice($product['price']) ?></p>
                                    <div class="mt-auto d-flex gap-2">
                                        <button class="btn btn-outline-glow flex-grow-1 p-2 text-center view-product-btn" data-id="<?= $product['id'] ?>">View</button>
                                        <a href="/UniMart/api/add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-glow flex-grow-1 p-2 text-center"><i class="fas fa-cart-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <h3 class="text-muted outfit">No products found.</h3>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
