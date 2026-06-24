    <footer class="mt-5 py-5 glass-panel rounded-0 border-start-0 border-end-0 border-bottom-0 position-relative z-10" style="margin-top: 100px !important;">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <h3 class="outfit fw-bold text-gradient-primary">UniMart</h3>
                    <p class="text-muted mt-3 pe-4">The ultimate premium marketplace exclusively designed for NSBM students and staff. Shop smarter, live better.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-main fs-5 opacity-75 hover-opacity-100 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-main fs-5 opacity-75 hover-opacity-100 transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-main fs-5 opacity-75 hover-opacity-100 transition"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-2">
                    <h5 class="text-main mb-4 outfit">Navigate</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="/UniMart/index.php" class="text-muted text-decoration-none hover-white">Home</a></li>
                        <li><a href="/UniMart/index.php#about" class="text-muted text-decoration-none hover-white">About</a></li>
                        <li><a href="/UniMart/index.php#features" class="text-muted text-decoration-none hover-white">Features</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="text-main mb-4 outfit">Shop</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="/UniMart/products.php" class="text-muted text-decoration-none hover-white">All Products</a></li>
                        <li><a href="/UniMart/categories.php" class="text-muted text-decoration-none hover-white">Categories</a></li>
                        <li><a href="#" class="text-muted text-decoration-none hover-white">Deals</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="text-main mb-4 outfit">Support</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="/UniMart/support.php#faq" class="text-muted text-decoration-none hover-white">FAQ</a></li>
                        <li><a href="/UniMart/support.php#shipping" class="text-muted text-decoration-none hover-white">Shipping</a></li>
                        <li><a href="/UniMart/support.php#returns" class="text-muted text-decoration-none hover-white">Returns</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-5 mb-4 opacity-25">
            <div class="text-center text-muted small">
                &copy; <?= date('Y') ?> UniMart. NSBM Web Application Development Project. All rights reserved.
            </div>
        </div>
    </footer>

<?php
$cartItems = [];
$cartSubtotal = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $cartSubtotal += $subtotal;
        $product['quantity'] = $qty;
        $product['subtotal'] = $subtotal;
        $cartItems[] = $product;
    }
}
$cartTax = $cartSubtotal * 0.08;
$cartTotal = $cartSubtotal + $cartTax;
?>

<div class="offcanvas offcanvas-end glass-panel border-0" tabindex="-1" id="notificationOffcanvas" style="width: 350px;">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
        <h5 class="offcanvas-title outfit fw-bold text-main"><i class="far fa-bell me-2 text-primary"></i>Notifications</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="flex-grow-1 overflow-auto p-0 custom-scrollbar" id="notificationList">
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 bg-primary bg-opacity-10 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-primary mt-1"><i class="fas fa-box"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">Order Shipped!</h6>
                        <p class="mb-1 small text-muted">Your order #UM-00123 has been shipped and is on its way.</p>
                        <small class="text-primary opacity-75 fw-bold">2 hours ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-success mt-1"><i class="fas fa-tags"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">Flash Sale Alert</h6>
                        <p class="mb-1 small text-muted">Don't miss out! 50% off on all accessories for the next 24 hours.</p>
                        <small class="text-muted opacity-75">1 day ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-warning mt-1"><i class="fas fa-star"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">Review your purchase</h6>
                        <p class="mb-1 small text-muted">How do you like the Sony WH-1000XM5? Leave a review and earn points!</p>
                        <small class="text-muted opacity-75">3 days ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss"><i class="fas fa-times"></i></button>
            </div>
        </div>
        
        <div class="p-3 border-top border-secondary border-opacity-25 bg-white bg-opacity-5 text-center flex-grow-1 d-flex flex-column justify-content-center" id="emptyNotificationState" style="display: none !important;">
            <i class="far fa-bell-slash display-4 text-muted mb-3 opacity-50"></i>
            <h6 class="text-main outfit fw-bold">You're all caught up!</h6>
            <p class="text-muted small mb-0">No new notifications right now.</p>
        </div>
        
        <div class="p-3 border-top border-secondary border-opacity-25 bg-white bg-opacity-5 mt-auto" id="markAllReadContainer">
            <button class="btn btn-outline-glow w-100 py-2 fw-bold btn-sm" id="markAllReadBtn">Mark all as read</button>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end glass-panel border-0" tabindex="-1" id="cartOffcanvas" style="width: 400px;">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
        <h5 class="offcanvas-title outfit fw-bold text-main"><i class="fas fa-shopping-bag me-2 text-primary"></i>Your Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <?php if(empty($cartItems)): ?>
            <div class="p-4 text-center my-auto">
                <i class="fas fa-shopping-cart fs-1 text-muted mb-3 opacity-50"></i>
                <h5 class="text-main outfit">Your cart is empty</h5>
                <p class="text-muted small mb-4">Looks like you haven't added anything yet.</p>
                <button type="button" class="btn btn-glow" data-bs-dismiss="offcanvas">Continue Shopping</button>
            </div>
        <?php else: ?>
            <div class="flex-grow-1 overflow-auto p-4 custom-scrollbar">
                <?php foreach($cartItems as $item): ?>
                <div class="d-flex gap-3 mb-4 align-items-center position-relative">
                    <div class="bg-white bg-opacity-5 rounded p-2 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <img src="<?= getProductImage($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-main outfit fw-bold mb-1 pe-4"><?= htmlspecialchars($item['name']) ?></h6>
                        <div class="text-muted small mb-1">Qty: <?= $item['quantity'] ?></div>
                        <div class="text-accent fw-bold"><?= formatPrice($item['price']) ?></div>
                    </div>
                    <button class="btn btn-sm text-danger position-absolute top-0 end-0 p-0 remove-cart-item hover-opacity-100 opacity-75" data-id="<?= $item['id'] ?>"><i class="fas fa-times fs-5"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="p-4 border-top border-secondary border-opacity-25 bg-white bg-opacity-5 mt-auto">
                <div class="d-flex justify-content-between text-muted mb-2 small">
                    <span>Subtotal</span>
                    <span><?= formatPrice($cartSubtotal) ?></span>
                </div>
                <div class="d-flex justify-content-between text-muted mb-3 small">
                    <span>Estimated Tax</span>
                    <span><?= formatPrice($cartTax) ?></span>
                </div>
                <div class="d-flex justify-content-between text-main fw-bold mb-4 fs-5">
                    <span>Total</span>
                    <span class="text-accent"><?= formatPrice($cartTotal) ?></span>
                </div>
                <?php if(isLoggedIn()): ?>
                    <a href="/UniMart/checkout.php" class="btn btn-glow w-100 py-3 fw-bold">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="/UniMart/auth/login.php?redirect=/UniMart/checkout.php" class="btn btn-outline-glow w-100 py-3 fw-bold">Login to Checkout</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 680px;">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0">
                <form action="/UniMart/products.php" method="GET" class="position-relative">
                    <div class="search-bar-wrapper">
                        <i class="fas fa-search search-bar-icon"></i>
                        <input 
                            type="text" 
                            id="liveSearchInput" 
                            name="search" 
                            class="search-bar-input outfit" 
                            placeholder="Search products, categories..." 
                            autocomplete="off"
                        >
                        <kbd class="search-bar-kbd">ESC</kbd>
                    </div>
                    <div id="liveSearchResults" class="search-results-panel d-none">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel border-0 overflow-hidden">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title outfit fw-bold text-main">Product Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 bg-white bg-opacity-5 d-flex align-items-center justify-content-center p-4">
                        <img id="qv-image" src="" alt="Product" class="img-fluid rounded" style="max-height: 300px; object-fit: contain;">
                    </div>
                    <div class="col-md-6 p-4 d-flex flex-column">
                        <span id="qv-category" class="badge bg-primary bg-opacity-25 text-primary rounded-pill mb-2 align-self-start">Loading...</span>
                        <h3 id="qv-name" class="outfit fw-bold text-main mb-2">Loading Product...</h3>
                        <h4 id="qv-price" class="text-accent fw-bold mb-3">--</h4>
                        <p id="qv-description" class="text-muted small mb-4 flex-grow-1">Fetching details...</p>
                        
                        <div class="mb-3">
                            <span id="qv-stock" class="text-success small"><i class="fas fa-check-circle me-1"></i> In Stock</span>
                        </div>
                        
                        <form id="qv-form" action="/UniMart/api/add_to_cart.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="id" id="qv-id" value="">
                            <div class="input-group" style="width: 130px;">
                                <button type="button" class="btn btn-outline-secondary px-3 text-main border-secondary border-opacity-25" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">-</button>
                                <input type="number" name="quantity" class="form-control text-center bg-transparent text-main border-secondary border-opacity-25" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-secondary px-3 text-main border-secondary border-opacity-25" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                            </div>
                            <button type="submit" class="btn btn-glow flex-grow-1">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="/UniMart/assets/js/main.js"></script>

<div id="toastContainer" class="toast-container"></div>

<script>

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `ecommerce-toast`;
    
    let icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
    let iconColor = type === 'success' ? 'var(--success)' : 'var(--primary)';
    
    toast.innerHTML = `
        <i class="fas ${icon} ecommerce-toast-icon" style="color: ${iconColor};"></i>
        <div class="ecommerce-toast-content">
            <h6>${type === 'success' ? 'Success' : 'Notice'}</h6>
            <p>${message}</p>
        </div>
    `;
    
    document.getElementById('toastContainer').appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[action*="add_to_cart"]');
    forms.forEach(form => {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('added') === '1') {
            showToast('Item successfully added to cart!');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
});
</script>
</body>
</html>

