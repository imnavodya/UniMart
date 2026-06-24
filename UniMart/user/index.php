<?php
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userAvatar = $stmt->fetchColumn();
$avatarUrl = getUserAvatar($userAvatar, $_SESSION['name']);
$initials = getUserInitials($_SESSION['name']);
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
$totalSpent = 0;
foreach ($orders as $order) {
    if ($order['status'] === 'completed') {
        $totalSpent += $order['total_amount'];
    }
}
?>

<section class="py-5 position-relative z-10" style="margin-top: 100px; min-height: 70vh;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-right">
                <div class="glass-panel p-4 text-center sticky-top" style="top: 100px;">
                    <input type="file" id="userAvatarFileInput" accept="image/*" style="display:none">
                    
                    <div class="position-relative mx-auto mb-3" id="userAvatarTrigger" style="width: 100px; height: 100px; cursor: pointer; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary);">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= $avatarUrl ?>" alt="Avatar" id="userAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div id="userAvatarInitials" class="bg-primary bg-opacity-25 text-primary d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; font-size: 3rem; font-family: 'Outfit', sans-serif;">
                                <?= $initials ?>
                            </div>
                        <?php endif; ?>
                        <div class="adm-avatar-upload position-absolute inset-0 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s; color: white;">
                            <i class="fas fa-camera fs-4"></i>
                        </div>
                    </div>
                    <h3 class="outfit fw-bold text-main mb-1"><?= htmlspecialchars($_SESSION['name']) ?></h3>
                    <p class="text-muted mb-4"><?= isAdmin() ? 'Administrator' : 'Premium Member' ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center border-top border-secondary border-opacity-25 pt-4 mb-3">
                        <span class="text-muted">Total Orders</span>
                        <span class="fw-bold text-main fs-5"><?= count($orders) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top border-secondary border-opacity-25 pt-3 pb-4">
                        <span class="text-muted">Total Spent</span>
                        <span class="fw-bold text-accent fs-5"><?= formatPrice($totalSpent) ?></span>
                    </div>
                    
                    <a href="/UniMart/auth/logout.php" class="btn btn-outline-danger w-100 bg-transparent">Sign Out</a>
                </div>
            </div>
            
            <div class="col-lg-8" data-aos="fade-left">
                <div class="glass-panel p-4">
                    <h4 class="outfit fw-bold text-main mb-4">Order History</h4>
                    
                    <?php if(empty($orders)): ?>
                        <div class="text-center py-5">
                            <div class="fs-1 text-muted mb-3"><i class="fas fa-box-open"></i></div>
                            <h5 class="text-main outfit">No orders yet</h5>
                            <p class="text-muted">When you buy something, it will appear here.</p>
                            <a href="/UniMart/products.php" class="btn btn-glow mt-2">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="user-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td class="fw-bold text-muted">#UM-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="text-muted"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                        <td class="text-accent fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                                        <td class="order-status-cell" data-order-id="<?= $order['id'] ?>" data-current-status="<?= $order['status'] ?>">
                                            <?php if($order['status'] === 'completed'): ?>
                                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                            <?php elseif($order['status'] === 'pending'): ?>
                                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> <?= ucfirst($order['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-glow px-3 py-1" onclick="viewOrder(<?= $order['id'] ?>)">View Details</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; padding: 16px 20px; border-bottom: 1px solid var(--glass-border); }
.user-table td { font-size: 0.95rem; color: var(--text-main); padding: 18px 20px; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.user-table tr:last-child td { border-bottom: none; }
.user-table tr:hover td { background: var(--bg-3); }
.hover-bg-light:hover {
    background-color: var(--bg-3) !important;
}
#userAvatarTrigger:hover .adm-avatar-upload {
    opacity: 1 !important;
}
</style>

<script>
let orderModal;
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('userAvatarTrigger');
    const fileInput = document.getElementById('userAvatarFileInput');

    if (trigger && fileInput) {
        trigger.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (!this.files.length) return;
            const formData = new FormData();
            formData.append('avatar', this.files[0]);

            fetch('/UniMart/api/upload_avatar.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const img = document.getElementById('userAvatarImg');
                        const initials = document.getElementById('userAvatarInitials');
                        if (img) {
                            img.src = data.avatar_url + '?t=' + Date.now();
                        } else {
                            if (initials) initials.remove();
                            const newImg = document.createElement('img');
                            newImg.id = 'userAvatarImg';
                            newImg.src = data.avatar_url;
                            newImg.alt = 'Avatar';
                            newImg.style.width = '100%';
                            newImg.style.height = '100%';
                            newImg.style.objectFit = 'cover';
                            trigger.insertBefore(newImg, trigger.querySelector('.adm-avatar-upload'));
                        }
                    } else {
                        alert(data.message || 'Upload failed');
                    }
                });
        });
    }

    orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
});

function viewOrder(id) {
    const fd = new FormData();
    fd.append('action', 'get_order');
    fd.append('id', id);
    
    fetch('/UniMart/api/get_order.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                const items = data.items;
                
                document.getElementById('modalOrderId').textContent = '#UM-' + String(order.id).padStart(5, '0');
                document.getElementById('modalOrderDate').textContent = order.created_at_formatted;
                document.getElementById('modalOrderTotal').textContent = order.total_formatted;

                let statusBadge = '';
                if(order.status === 'completed') {
                    statusBadge = '<span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Completed</span>';
                } else if(order.status === 'pending') {
                    statusBadge = '<span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> Pending</span>';
                } else {
                    statusBadge = '<span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> ' + order.status.charAt(0).toUpperCase() + order.status.slice(1) + '</span>';
                }
                document.getElementById('modalOrderStatus').innerHTML = statusBadge;

                let itemsHtml = '';
                items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 32px; height: 32px; border-radius: 6px; overflow: hidden; background: var(--bg-3); margin-right: 12px; border: 1px solid var(--glass-border); flex-shrink: 0;">
                                        <img src="${item.image_url}" alt="${item.product_name}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span style="font-weight:600; color:var(--text-main); font-family:'Outfit',sans-serif; font-size: 0.9rem;">${item.product_name}</span>
                                </div>
                            </td>
                            <td class="py-3 text-muted">${item.price_formatted}</td>
                            <td class="py-3 text-center text-main">${item.quantity}</td>
                            <td class="text-end px-4 py-3 fw-bold text-main">${item.subtotal_formatted}</td>
                        </tr>
                    `;
                });
                document.getElementById('modalOrderItems').innerHTML = itemsHtml;
                
                orderModal.show();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while fetching order details.');
        });
}

setInterval(() => {
    fetch('/UniMart/api/get_order_statuses.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                data.statuses.forEach(order => {
                    const cell = document.querySelector(`.order-status-cell[data-order-id="${order.id}"]`);
                    if (cell) {
                        const currentStatus = cell.dataset.currentStatus;
                        if (currentStatus !== order.status) {
                            let badgeHtml = '';
                            if(order.status === 'completed') badgeHtml = '<span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Completed</span>';
                            else if(order.status === 'pending') badgeHtml = '<span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> Pending</span>';
                            else badgeHtml = '<span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> ' + order.status.charAt(0).toUpperCase() + order.status.slice(1) + '</span>';
                            
                            cell.innerHTML = badgeHtml;
                            cell.dataset.currentStatus = order.status;

                            const modalStatusDiv = document.getElementById('modalOrderStatus');
                            if (modalStatusDiv) {
                                const modalIdText = document.getElementById('modalOrderId').textContent;
                                if (modalIdText) {
                                    const currentModalId = parseInt(modalIdText.replace('#UM-', ''), 10);
                                    if (currentModalId === parseInt(order.id, 10)) {
                                        modalStatusDiv.innerHTML = badgeHtml;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
}, 5000);
</script>

<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel border-0 p-0" style="background: var(--bg-2);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
                <h5 class="modal-title outfit fw-bold text-main">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Order Information</h6>
                        <div class="fw-bold text-main mb-1" id="modalOrderId"></div>
                        <div class="text-muted small mb-1" id="modalOrderDate"></div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Status</h6>
                        <div class="mt-2" id="modalOrderStatus"></div>
                    </div>
                </div>
                
                <h6 class="text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Order Items</h6>
                <div class="table-responsive rounded border border-secondary border-opacity-25 mb-4">
                    <table class="user-table mb-0">
                        <thead style="background: var(--bg-3);">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="py-3">Price</th>
                                <th class="py-3 text-center">Qty</th>
                                <th class="py-3 text-end px-4">Total</th>
                            </tr>
                        </thead>
                        <tbody id="modalOrderItems">
                        </tbody>
                        <tfoot class="border-top border-secondary border-opacity-25" style="background: var(--bg-3);">
                            <tr>
                                <td colspan="3" class="text-end px-4 py-3 fw-bold text-main">Total Amount:</td>
                                <td class="text-end px-4 py-3 fw-bold text-accent" id="modalOrderTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-glow" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
