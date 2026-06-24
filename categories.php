<?php 
require_once 'includes/header.php'; 

$stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container">
        <div class="mb-5" data-aos="fade-down">
            <nav class="ecommerce-breadcrumb mb-3" aria-label="breadcrumb">
                <a href="/UniMart/index.php">Home</a>
                <span class="separator">/</span>
                <span class="current">Categories</span>
            </nav>
            <h1 class="outfit fw-bold fs-1">Shop by <span class="text-gradient-primary">Category</span></h1>
            <p class="text-muted">Find exactly what you're looking for by browsing our curated categories.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            $delay = 0;
            foreach ($categories as $cat):
            ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                <div class="glass-card p-4 text-center category-card h-100 d-flex flex-column justify-content-center" onclick="window.location.href='/UniMart/products.php?category=<?= $cat['id'] ?>'">
                    <div class="category-icon-wrapper shadow-lg mb-4">
                        <i class="<?= htmlspecialchars($cat['icon']) ?>"></i>
                    </div>
                    <h4 class="outfit fw-bold text-main mb-2"><?= htmlspecialchars($cat['name']) ?></h4>
                    <p class="text-muted small mb-0">Explore <i class="fas fa-arrow-right ms-1 text-primary"></i></p>
                </div>
            </div>
            <?php 
            $delay += 100;
            endforeach; 
            ?>
        </div>
    </div>
</section>

<style>
.category-card {
    cursor: pointer;
    transition: all 0.3s ease;
}
.category-card:hover .text-primary {
    color: var(--primary) !important;
}
</style>

<?php require_once 'includes/footer.php'; ?>
