<?php 
require_once 'includes/header.php'; 
require_once 'classes/Stats.php';

$statsModel = new Stats($conn);
$userCount = $statsModel->getUserCount();
$productCount = $statsModel->getProductCount();
$categoryCount = $statsModel->getCategoryCount();
?>

<section class="hero-section overflow-hidden">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 position-relative z-10">
                <h1 class="hero-title text-gradient">Shop Smarter.<br>Live <span class="text-gradient-primary">Better.</span></h1>
                <p class="hero-subtitle fs-5">Your premium marketplace exclusively designed for NSBM students and staff. Discover tech, apparel, and more in a futuristic shopping experience.</p>
                <div class="hero-buttons d-flex gap-3">
                    <a href="/UniMart/products.php" class="btn btn-glow">Explore Products</a>
                    <a href="#about" class="btn btn-outline-glow"><i class="fas fa-info-circle me-2"></i>Learn More</a>
                </div>
            </div>
            <div class="col-lg-6 position-relative hero-img-container d-none d-lg-block">
                <div class="glass-panel p-4 floating-element mx-auto d-flex align-items-center justify-content-center" style="width: 360px; height: 360px; border-radius: 40px; background: var(--primary-glow); backdrop-filter: blur(8px);">
                    <img src="/UniMart/assets/img/logo/logo.png" alt="UniMart Logo" class="img-fluid position-absolute" style="filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1)); width: 450px; max-width: none;">
                </div>
                
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-primary fs-3 shadow-lg" style="width: 80px; height: 80px; border-radius: 50%; top: 5%; left: 5%; animation-delay: 0.5s;">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-highlight fs-3 shadow-lg" style="width: 100px; height: 100px; border-radius: 50%; bottom: 10%; right: -5%; animation-delay: 1.5s;">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-accent fs-3 shadow-lg" style="width: 70px; height: 70px; border-radius: 50%; top: 40%; right: 75%; animation-delay: 2.5s;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-info fs-3 shadow-lg" style="width: 90px; height: 90px; border-radius: 50%; top: -5%; right: 10%; animation-delay: 1.2s;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-success fs-3 shadow-lg" style="width: 65px; height: 65px; border-radius: 50%; bottom: 5%; left: 10%; animation-delay: 0.8s;">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="floating-element position-absolute glass-panel d-flex align-items-center justify-content-center text-warning fs-3 shadow-lg" style="width: 75px; height: 75px; border-radius: 50%; bottom: 35%; right: -15%; animation-delay: 2.1s;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-5 position-relative z-10 mb-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="outfit fw-bold fs-1">Why Choose <span class="text-gradient-primary">UniMart</span></h2>
            <p class="text-muted">Experience shopping reinvented for the campus lifestyle.</p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['icon' => 'fas fa-shield-alt', 'title' => 'Secure Shopping', 'desc' => 'End-to-end encrypted transactions to keep your data safe.'],
                ['icon' => 'fas fa-shipping-fast', 'title' => 'Fast Delivery', 'desc' => 'Next-day on-campus delivery directly to your dorm or faculty.'],
                ['icon' => 'fas fa-user-check', 'title' => 'Verified Sellers', 'desc' => 'All sellers are verified NSBM staff or student entrepreneurs.'],
                ['icon' => 'fas fa-tags', 'title' => 'Student Discounts', 'desc' => 'Exclusive discounts and deals tailored for the university community.']
            ];
            foreach ($features as $index => $f):
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="fs-1 text-primary mb-3">
                        <i class="<?= $f['icon'] ?>"></i>
                    </div>
                    <h4 class="outfit fw-bold mb-2" style="color: var(--text-main);"><?= $f['title'] ?></h4>
                    <p class="text-muted small mb-0"><?= $f['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="about" class="py-5 position-relative z-10 mb-5">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill px-3 py-2 mb-3 outfit fw-semibold">About UniMart</span>
            <h2 class="outfit fw-bold fs-1">Built for the <span class="text-gradient-primary">Campus Community</span></h2>
            <p class="text-muted fs-5 mx-auto" style="max-width: 560px;">UniMart was born at NSBM Green University a platform exclusively crafted for students and staff to buy, sell, and connect.</p>
        </div>

        <div class="row g-4 mb-5 justify-content-center">
            <?php

            $stats = [
                ['value' => $userCount . '+', 'label' => 'Active Users', 'icon' => 'fas fa-users', 'color' => 'var(--primary)'],
                ['value' => $productCount . '+',   'label' => 'Products Listed', 'icon' => 'fas fa-box-open', 'color' => 'var(--accent)'],
                ['value' => $categoryCount . '+',    'label' => 'Categories', 'icon' => 'fas fa-th', 'color' => 'var(--secondary)'],
                ['value' => '98%',    'label' => 'Satisfaction Rate', 'icon' => 'fas fa-heart', 'color' => 'var(--highlight)'],
            ];
            foreach ($stats as $i => $s): ?>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <div class="glass-card p-4 text-center h-100">
                    <div class="fs-2 mb-2" style="color: <?= $s['color'] ?>"><i class="<?= $s['icon'] ?>"></i></div>
                    <div class="outfit fw-bold" style="font-size: 2rem; line-height:1.1; color: var(--text-main);"><?= $s['value'] ?></div>
                    <div class="text-muted small mt-1"><?= $s['label'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="glass-panel p-5 h-100">
                    <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill px-3 py-1 mb-3 outfit">Our Mission</span>
                    <h3 class="outfit fw-bold mb-3 fs-2" style="color: var(--text-main);">Empowering Every <span class="text-gradient-primary">NSBM Member</span></h3>
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        UniMart is more than a marketplace, it's an ecosystem designed to support student entrepreneurs, faculty sellers, and budget-conscious buyers all under one roof. We believe the campus economy should be accessible, transparent, and beautiful.
                    </p>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <?php
                        $points = [
                            ['icon' => 'fas fa-shield-alt', 'color' => 'var(--primary)',   'text' => 'End-to-end secure transactions'],
                            ['icon' => 'fas fa-user-check', 'color' => 'var(--accent)',    'text' => 'Verified NSBM community members only'],
                            ['icon' => 'fas fa-bolt',       'color' => 'var(--highlight)', 'text' => 'Lightning-fast checkout experience'],
                        ];
                        foreach ($points as $pt): ?>
                        <li class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: var(--bg-3);">
                                <i class="<?= $pt['icon'] ?>" style="color: <?= $pt['color'] ?>; font-size: 0.85rem;"></i>
                            </div>
                            <span class="text-muted"><?= $pt['text'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/UniMart/products.php" class="btn btn-glow">Start Shopping</a>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="glass-card p-4 text-center d-flex flex-column justify-content-center h-100" style="min-height: 180px;">
                            <div class="fs-1 mb-2" style="color: var(--primary);"><i class="fas fa-graduation-cap"></i></div>
                            <h6 class="outfit fw-bold mb-1" style="color: var(--text-main);">Student First</h6>
                            <p class="text-muted small mb-0">Designed with NSBM students at the core of every decision.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="glass-card p-4 text-center d-flex flex-column justify-content-center h-100" style="min-height: 180px;">
                            <div class="fs-1 mb-2" style="color: var(--accent);"><i class="fas fa-leaf"></i></div>
                            <h6 class="outfit fw-bold mb-1" style="color: var(--text-main);">Green Campus</h6>
                            <p class="text-muted small mb-0">Supporting NSBM's eco-friendly and sustainable campus vision.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="glass-card p-4 text-center d-flex flex-column justify-content-center h-100" style="min-height: 180px;">
                            <div class="fs-1 mb-2" style="color: var(--secondary);"><i class="fas fa-handshake"></i></div>
                            <h6 class="outfit fw-bold mb-1" style="color: var(--text-main);">Community Driven</h6>
                            <p class="text-muted small mb-0">Every feature is shaped by real feedback from our members.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="glass-card p-4 text-center d-flex flex-column justify-content-center h-100" style="min-height: 180px;">
                            <div class="fs-1 mb-2" style="color: var(--highlight);"><i class="fas fa-lock"></i></div>
                            <h6 class="outfit fw-bold mb-1" style="color: var(--text-main);">Privacy & Trust</h6>
                            <p class="text-muted small mb-0">Your data and payments are fully protected and private.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
