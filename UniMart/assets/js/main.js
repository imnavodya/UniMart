function openQuickViewFromSearch(productId) {

    const searchModalEl = document.getElementById('searchModal');
    const searchModal = bootstrap.Modal.getInstance(searchModalEl);
    if (searchModal) searchModal.hide();

    searchModalEl.addEventListener('hidden.bs.modal', function handler() {
        searchModalEl.removeEventListener('hidden.bs.modal', handler);

        const modalEl = document.getElementById('quickViewModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        document.getElementById('qv-name').textContent = 'Loading...';
        document.getElementById('qv-description').textContent = '';
        document.getElementById('qv-price').textContent = '';
        document.getElementById('qv-image').src = '';
        modal.show();

        fetch(`/UniMart/api/get_product.php?id=${productId}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const p = response.data;
                    document.getElementById('qv-name').textContent = p.name;
                    document.getElementById('qv-price').textContent = p.formatted_price;
                    document.getElementById('qv-description').textContent = p.description;
                    document.getElementById('qv-category').textContent = p.category_name;
                    document.getElementById('qv-image').src = p.image_url;
                    document.getElementById('qv-id').value = p.id;
                    const stockEl = document.getElementById('qv-stock');
                    if (p.stock > 0) {
                        stockEl.className = 'text-success small';
                        stockEl.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${p.stock} In Stock`;
                    } else {
                        stockEl.className = 'text-danger small';
                        stockEl.innerHTML = `<i class="fas fa-times-circle me-1"></i> Out of Stock`;
                    }
                }
            });
    }, { once: true });
}

document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50
    });

    const navbar = document.querySelector('.navbar-floating');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    gsap.registerPlugin(ScrollTrigger);

    if (document.querySelector('.hero-title')) {
        const tl = gsap.timeline();

        tl.from(".hero-title", {
            y: 50,
            opacity: 0,
            duration: 1,
            ease: "power3.out"
        })
            .from(".hero-subtitle", {
                y: 30,
                opacity: 0,
                duration: 0.8,
                ease: "power3.out"
            }, "-=0.5")
            .from(".hero-buttons", {
                y: 20,
                opacity: 0,
                duration: 0.5,
                ease: "power2.out"
            }, "-=0.4")
            .from(".hero-img-container", {
                x: 50,
                opacity: 0,
                duration: 1.2,
                ease: "power3.out"
            }, "-=1");

        gsap.to(".floating-element", {
            y: -20,
            rotation: 5,
            duration: 3,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut",
            stagger: 0.2
        });
    }

    const viewButtons = document.querySelectorAll('.view-product-btn');
    if (viewButtons.length > 0) {
        const modalEl = document.getElementById('quickViewModal');
        const modal = new bootstrap.Modal(modalEl);

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const productId = this.getAttribute('data-id');

                document.getElementById('qv-name').textContent = 'Loading...';
                document.getElementById('qv-description').textContent = 'Please wait...';

                modal.show();

                fetch(`/UniMart/api/get_product.php?id=${productId}`)
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            const p = response.data;
                            document.getElementById('qv-name').textContent = p.name;
                            document.getElementById('qv-price').textContent = p.formatted_price;
                            document.getElementById('qv-description').textContent = p.description;
                            document.getElementById('qv-category').textContent = p.category_name;
                            document.getElementById('qv-image').src = p.image_url;
                            document.getElementById('qv-id').value = p.id;

                            const stockEl = document.getElementById('qv-stock');
                            if (p.stock > 0) {
                                stockEl.className = 'text-success small';
                                stockEl.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${p.stock} In Stock`;
                            } else {
                                stockEl.className = 'text-danger small';
                                stockEl.innerHTML = `<i class="fas fa-times-circle me-1"></i> Out of Stock`;
                            }
                        } else {
                            document.getElementById('qv-name').textContent = 'Error';
                            document.getElementById('qv-description').textContent = response.message;
                        }
                    })
                    .catch(err => console.error(err));
            });
        });
    }

    const removeCartBtns = document.querySelectorAll('.remove-cart-item');
    removeCartBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            fetch(`/UniMart/api/remove_from_cart.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.setItem('openCart', '1');
                        window.location.reload();
                    }
                });
        });
    });

    if (sessionStorage.getItem('openCart')) {
        sessionStorage.removeItem('openCart');
        const cartOffcanvasEl = document.getElementById('cartOffcanvas');
        if (cartOffcanvasEl) {
            const cartOffcanvas = new bootstrap.Offcanvas(cartOffcanvasEl);
            cartOffcanvas.show();
        }
    }

    const searchInput = document.getElementById('liveSearchInput');
    const searchResults = document.getElementById('liveSearchResults');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('d-none');
                searchResults.innerHTML = '';
                return;
            }

            searchResults.classList.remove('d-none');
            searchResults.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';

            searchTimeout = setTimeout(() => {
                fetch(`/UniMart/api/search_products.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            const data = response.data;
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="search-no-results"><i class="fas fa-search me-2 opacity-50"></i>No products found.</div>';
                                return;
                            }

                            let html = '';
                            data.forEach(p => {
                                html += `
                                    <div class="search-result-item" role="button" data-id="${p.id}" onclick="openQuickViewFromSearch(${p.id})">
                                        <img src="${p.image_url}" alt="${p.name}" class="search-result-thumb">
                                        <div class="flex-grow-1">
                                            <div class="search-result-name">${p.name}</div>
                                            <div class="search-result-price">${p.formatted_price}</div>
                                        </div>
                                        <i class="fas fa-eye text-muted small opacity-50"></i>
                                    </div>
                                `;
                            });
                            html += `<a href="/UniMart/products.php?search=${encodeURIComponent(query)}" class="search-view-all">View All Results &rarr;</a>`;
                            searchResults.innerHTML = html;
                        }
                    })
                    .catch(() => {
                        searchResults.innerHTML = '<div class="search-no-results text-danger">An error occurred. Please try again.</div>';
                    });
            }, 300);
        });

        const searchModalEl = document.getElementById('searchModal');
        if (searchModalEl) {
            searchModalEl.addEventListener('shown.bs.modal', () => {
                searchInput.focus();
            });
            searchModalEl.addEventListener('hidden.bs.modal', () => {
                searchInput.value = '';
                searchResults.classList.add('d-none');
                searchResults.innerHTML = '';
            });
        }
    }

    // Notification Logic
    const dismissBtns = document.querySelectorAll('.dismiss-notification');
    const notificationBadge = document.getElementById('notificationBadge');
    const emptyState = document.getElementById('emptyNotificationState');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const notificationList = document.getElementById('notificationList');

    function updateNotificationBadge() {
        if (!notificationList) return;
        const count = notificationList.querySelectorAll('.notification-item').length;
        if (notificationBadge) {
            if (count > 0) {
                notificationBadge.style.display = 'block';
            } else {
                notificationBadge.style.display = 'none';
            }
        }
        if (count === 0 && emptyState) {
            emptyState.style.setProperty('display', 'flex', 'important');
            if(markAllBtn) markAllBtn.parentElement.style.display = 'none';
        }
    }

    dismissBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const item = this.closest('.notification-item');
            
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                item.remove();
                updateNotificationBadge();
            }, 300);
        });
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            const items = document.querySelectorAll('.notification-item');
            items.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        item.remove();
                        updateNotificationBadge();
                    }, 300);
                }, index * 100);
            });
        });
    }
});
