<?php 
require_once 'includes/header.php'; 

// Fetch all orders
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = $searchQuery !== '' ? "WHERE o.id = :search OR u.name LIKE :searchLike" : "";

$stmt = $conn->prepare("
    SELECT o.*, u.name as customer_name, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    $whereClause
    ORDER BY o.created_at DESC
");

if ($searchQuery !== '') {
    $stmt->execute(['search' => (int)$searchQuery, 'searchLike' => "%{$searchQuery}%"]);
} else {
    $stmt->execute();
}
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="outfit fw-bold text-main mb-0">Orders</h1>
        <p class="text-muted mb-0">Manage customer orders</p>
    </div>
    <a href="export_csv.php?type=orders" class="btn btn-glow"><i class="fas fa-file-export me-2"></i>Export CSV</a>
</div>

<div class="adm-panel p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="adm-table">
            <thead style="background: var(--bg-3);">
                <tr>
                    <th class="px-4 sortable" title="Click to sort">Order ID</th>
                    <th class="sortable" title="Click to sort">Customer</th>
                    <th class="sortable" title="Click to sort">Date</th>
                    <th class="sortable" title="Click to sort">Total Amount</th>
                    <th class="sortable" title="Click to sort">Status</th>
                    <th class="text-end px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td class="px-4" style="color:var(--text-muted);font-weight:600;">#UM-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td>
                        <div style="font-weight:600; color:var(--text-main); font-family:'Outfit',sans-serif;"><?= htmlspecialchars($order['customer_name']) ?></div>
                        <div style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;"><?= htmlspecialchars($order['email']) ?></div>
                    </td>
                    <td style="color:var(--text-muted);"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                    <td style="color:var(--accent);font-weight:600;"><?= formatPrice($order['total_amount']) ?></td>
                    <td class="order-status-cell" data-order-id="<?= $order['id'] ?>">
                        <?php
                            $bgClass = $order['status'] === 'completed' ? 'bg-success text-success' : ($order['status'] === 'pending' ? 'bg-warning text-warning' : 'bg-danger text-danger');
                        ?>
                        <select class="form-select form-select-sm fw-bold border-0 shadow-none <?= $bgClass ?>" style="--bs-bg-opacity: .15; font-size: 0.75rem; border-radius: 20px; width: 120px; cursor: pointer;" onchange="updateStatusQuick(<?= $order['id'] ?>, this.value, this)">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?> class="text-dark">Pending</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?> class="text-dark">Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?> class="text-dark">Cancelled</option>
                        </select>
                    </td>
                    <td class="text-end px-4">
                        <button class="adm-btn adm-btn-ghost" style="padding:4px 10px; font-size:0.75rem;" title="View" onclick="viewOrder(<?= $order['id'] ?>)"><i class="fas fa-eye m-0"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($orders)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">No orders found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Order Offcanvas -->
<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="orderOffcanvas" style="width: 550px; background: var(--bg-2);">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25 p-4">
        <h5 class="offcanvas-title outfit fw-bold text-main" id="orderOffcanvasTitle">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-4 d-flex flex-column">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Customer Information</h6>
                <div class="fw-bold text-main mb-1" id="offcanvasCustomerName"></div>
                <div class="text-muted small mb-1" id="offcanvasCustomerEmail"></div>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Order Information</h6>
                <div class="fw-bold text-main mb-1" id="offcanvasOrderId"></div>
                <div class="text-muted small mb-1" id="offcanvasOrderDate"></div>
                <div class="mt-2 d-flex justify-content-sm-end align-items-center gap-2">
                    <span id="offcanvasStatusLoading" style="display:none;" class="spinner-border spinner-border-sm text-primary"></span>
                    <span id="offcanvasStatusBadge" class="adm-badge fw-bold"></span>
                </div>
            </div>
        </div>
        
        <h6 class="text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Order Items</h6>
        <div class="table-responsive rounded border border-secondary border-opacity-25 mb-4">
            <table class="adm-table mb-0">
                <thead style="background: var(--bg-3);">
                    <tr>
                        <th class="px-4 py-3">Product</th>
                        <th class="py-3">Price</th>
                        <th class="py-3 text-center">Qty</th>
                        <th class="py-3 text-end px-4">Total</th>
                    </tr>
                </thead>
                <tbody id="offcanvasOrderItems">
                    <!-- Items inserted via JS -->
                </tbody>
                <tfoot class="border-top border-secondary border-opacity-25" style="background: var(--bg-3);">
                    <tr>
                        <td colspan="3" class="text-end px-4 py-3 fw-bold text-main">Total Amount:</td>
                        <td class="text-end px-4 py-3 fw-bold text-accent" id="offcanvasOrderTotal"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
let orderOffcanvas;
document.addEventListener('DOMContentLoaded', () => {
    orderOffcanvas = new bootstrap.Offcanvas(document.getElementById('orderOffcanvas'));
});

function viewOrder(id) {
    const fd = new FormData();
    fd.append('action', 'get_order');
    fd.append('id', id);
    
    fetch('/UniMart/api/admin_orders.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                const items = data.items;
                
                document.getElementById('offcanvasCustomerName').textContent = order.customer_name;
                document.getElementById('offcanvasCustomerEmail').textContent = order.email;
                document.getElementById('offcanvasOrderId').textContent = '#UM-' + String(order.id).padStart(5, '0');
                document.getElementById('offcanvasOrderDate').textContent = order.created_at_formatted;
                document.getElementById('offcanvasOrderTotal').textContent = order.total_formatted;
                
                // Set static status badge for view mode
                let badgeClass = order.status === 'completed' ? 'adm-badge-success' : (order.status === 'pending' ? 'adm-badge-warning' : 'adm-badge-danger');
                document.getElementById('offcanvasStatusBadge').className = `adm-badge ${badgeClass} fw-bold`;
                document.getElementById('offcanvasStatusBadge').textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);
                
                // Items
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
                document.getElementById('offcanvasOrderItems').innerHTML = itemsHtml;
                
                orderOffcanvas.show();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An error occurred while fetching order details.', 'error');
        });
}

function updateStatusQuick(orderId, newStatus, selectElement) {
    selectElement.disabled = true;
    const originalClass = selectElement.className;
    selectElement.className = 'form-select form-select-sm fw-bold border-0 shadow-none text-muted bg-secondary';
    
    const fd = new FormData();
    fd.append('action', 'update_status');
    fd.append('id', orderId);
    fd.append('status', newStatus);
    
    fetch('/UniMart/api/admin_orders.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            selectElement.disabled = false;
            if (data.success) {
                // Update badge color classes based on new status
                let newClass = 'form-select form-select-sm fw-bold border-0 shadow-none ';
                if(newStatus === 'completed') newClass += 'bg-success text-success';
                else if(newStatus === 'pending') newClass += 'bg-warning text-warning';
                else newClass += 'bg-danger text-danger';
                
                selectElement.className = newClass;
                selectElement.style.setProperty('--bs-bg-opacity', '.15');
                showToast('Order status updated instantly!', 'success');
            } else {
                showToast(data.message || 'Failed to update status', 'error');
                selectElement.className = originalClass; // Revert
            }
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>
