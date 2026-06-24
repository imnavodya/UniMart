</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="offcanvas offcanvas-end glass-panel border-0" tabindex="-1" id="notificationOffcanvas" style="width: 350px; background: var(--bg-2);">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
        <h5 class="offcanvas-title outfit fw-bold text-main"><i class="fas fa-bell me-2 text-primary"></i>Admin Alerts</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="flex-grow-1 overflow-auto p-0 custom-scrollbar" id="notificationList">
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 bg-primary bg-opacity-10 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-primary mt-1"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">New Order Received!</h6>
                        <p class="mb-1 small text-muted">Order #UM-00125 was just placed by John Student.</p>
                        <small class="text-primary opacity-75 fw-bold">10 mins ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss" style="background:transparent; border:none;"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-warning mt-1"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">Low Stock Alert</h6>
                        <p class="mb-1 small text-muted">Product 'MacBook Pro M3 Max' is running low on stock (2 left).</p>
                        <small class="text-muted opacity-75">1 hour ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss" style="background:transparent; border:none;"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="notification-item p-3 border-bottom border-secondary border-opacity-25 position-relative transition" style="cursor: pointer;">
                <div class="d-flex gap-3">
                    <div class="text-success mt-1"><i class="fas fa-user-plus"></i></div>
                    <div>
                        <h6 class="mb-1 outfit fw-bold text-main">New User Registered</h6>
                        <p class="mb-1 small text-muted">Sarah Smith just created an account.</p>
                        <small class="text-muted opacity-75">5 hours ago</small>
                    </div>
                </div>
                <button class="btn btn-sm text-muted position-absolute top-0 end-0 mt-1 me-1 dismiss-notification hover-opacity-100 opacity-50" aria-label="Dismiss" style="background:transparent; border:none;"><i class="fas fa-times"></i></button>
            </div>
        </div>
        
        <div class="p-3 border-top border-secondary border-opacity-25 bg-white bg-opacity-5 text-center flex-grow-1 d-flex flex-column justify-content-center" id="emptyNotificationState" style="display: none !important;">
            <i class="fas fa-check-circle display-4 text-muted mb-3 opacity-50"></i>
            <h6 class="text-main outfit fw-bold">All clear!</h6>
            <p class="text-muted small mb-0">No new alerts to review.</p>
        </div>
        
        <div class="p-3 border-top border-secondary border-opacity-25 bg-white bg-opacity-5 mt-auto" id="markAllReadContainer">
            <button class="adm-btn adm-btn-ghost w-100 justify-content-center" id="markAllReadBtn" style="border-radius: 8px;">Dismiss all</button>
        </div>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 620px;">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0">
                <form onsubmit="return false;" class="position-relative">
                    <div class="search-bar-wrapper">
                        <i class="fas fa-search search-bar-icon"></i>
                        <input type="text" id="adminSearchInput" class="search-bar-input outfit" placeholder="Search orders, products, categories..." autocomplete="off">
                        <kbd class="search-bar-kbd">ESC</kbd>
                    </div>
                    <div id="adminSearchResults" class="mt-2 rounded" style="background: var(--card-bg); max-height: 400px; overflow-y: auto; display: none; border: 1px solid var(--border-c); box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const bg = type === 'success' ? '#22C55E' : (type === 'error' ? '#EF4444' : '#00D4FF');
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
    
    toast.style.cssText = `
        background: ${bg}; color: white; padding: 12px 20px; border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px;
        font-weight: 600; font-family: 'Outfit', sans-serif; font-size: 0.9rem;
        transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s;
        opacity: 0;
    `;
    
    toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; }, 10);
    setTimeout(() => { toast.style.transform = 'translateX(120%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 3000);
}

document.querySelectorAll('th.sortable').forEach(th => {
    th.style.cursor = 'pointer';
    th.title = 'Click to sort';
    th.addEventListener('click', () => {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const index = Array.from(th.parentNode.children).indexOf(th);
        const isAsc = th.classList.contains('asc');
    
        table.querySelectorAll('th').forEach(h => h.classList.remove('asc', 'desc'));
        th.classList.add(isAsc ? 'desc' : 'asc');
        
        rows.sort((a, b) => {
            let valA = a.children[index].innerText.trim();
            let valB = b.children[index].innerText.trim();
            let numA = parseFloat(valA.replace(/[^0-9.-]+/g, ""));
            let numB = parseFloat(valB.replace(/[^0-9.-]+/g, ""));
            if (!isNaN(numA) && !isNaN(numB)) {
                return isAsc ? numB - numA : numA - numB;
            }
            return isAsc ? valB.localeCompare(valA) : valA.localeCompare(valB);
        });
        
        tbody.append(...rows);
    });
});

const searchInput = document.getElementById('adminSearchInput');
const searchResults = document.getElementById('adminSearchResults');

if (searchInput) {
    let timeout = null;
    searchInput.addEventListener('keyup', function(e) {
        clearTimeout(timeout);
        const q = this.value.trim();
        
        if (q.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        timeout = setTimeout(() => {
            fetch(`/UniMart/api/admin_search.php?q=${encodeURIComponent(q)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) return;
                    
                    let html = '';

                    if (data.orders.length > 0) {
                        html += '<div class="px-3 py-2 text-muted small fw-bold text-uppercase" style="background: var(--bg-3);">Orders</div>';
                        data.orders.forEach(o => {
                            let badge = o.status == 'completed' ? 'text-success' : (o.status == 'cancelled' ? 'text-danger' : 'text-warning');
                            let orderLink = `/UniMart/admin/orders.php?search=${o.id}`;
                            html += `<a href="${orderLink}" class="d-flex align-items-center justify-content-between p-3 border-bottom text-decoration-none" style="color: var(--text-main); transition: background 0.2s; border-color: var(--border-c) !important;" onmouseover="this.style.background='var(--bg-3)'" onmouseout="this.style.background='transparent'">
                                <div><span class="fw-bold">#UM-${String(o.id).padStart(5,'0')}</span> <span class="text-muted ms-2">${o.customer_name}</span></div>
                                <div class="fw-bold ${badge}">LKR ${o.total_amount}</div>
                            </a>`;
                        });
                    }

                    if (data.products.length > 0) {
                        html += '<div class="px-3 py-2 text-muted small fw-bold text-uppercase" style="background: var(--bg-3);">Products</div>';
                        data.products.forEach(p => {
                            let productLink = `/UniMart/admin/products.php?search=${encodeURIComponent(p.name)}`;
                            html += `<a href="${productLink}" class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none" style="color: var(--text-main); transition: background 0.2s; border-color: var(--border-c) !important;" onmouseover="this.style.background='var(--bg-3)'" onmouseout="this.style.background='transparent'">
                                <img src="${p.image_url}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <div class="fw-bold">${p.name}</div>
                                    <div class="text-accent small fw-bold">LKR ${p.price}</div>
                                </div>
                            </a>`;
                        });
                    }

                    if (data.categories.length > 0) {
                        html += '<div class="px-3 py-2 text-muted small fw-bold text-uppercase" style="background: var(--bg-3);">Categories</div>';
                        data.categories.forEach(c => {
                            let categoryLink = `/UniMart/admin/categories.php`;
                            html += `<a href="${categoryLink}" class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none" style="color: var(--text-main); transition: background 0.2s; border-color: var(--border-c) !important;" onmouseover="this.style.background='var(--bg-3)'" onmouseout="this.style.background='transparent'">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(0,212,255,0.1); color: #00D4FF; display: flex; align-items: center; justify-content: center;"><i class="${c.icon}"></i></div>
                                <div class="fw-bold">${c.name}</div>
                            </a>`;
                        });
                    }
                    
                    if (html === '') {
                        html = '<div class="p-4 text-center text-muted">No results found for "'+q+'"</div>';
                    }
                    
                    searchResults.innerHTML = html;
                    searchResults.style.display = 'block';
                });
        }, 300);
    });

    document.getElementById('searchModal').addEventListener('hidden.bs.modal', function () {
        searchInput.value = '';
        searchResults.style.display = 'none';
    });
}

const dismissBtns = document.querySelectorAll('.dismiss-notification');
const notificationBadge = document.getElementById('notificationBadge');
const emptyState = document.getElementById('emptyNotificationState');
const markAllBtn = document.getElementById('markAllReadBtn');
const notificationList = document.getElementById('notificationList');

function updateNotificationBadge() {
    if (!notificationList) return;
    const count = notificationList.querySelectorAll('.notification-item').length;
    if (notificationBadge) {
        notificationBadge.style.display = count > 0 ? 'block' : 'none';
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
</script>
</body>
</html>
