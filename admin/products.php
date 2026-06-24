<?php 
require_once 'includes/header.php'; 

// Fetch all products
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = $searchQuery !== '' ? "WHERE p.name LIKE :search OR c.name LIKE :search" : "";

$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id
    $whereClause
    ORDER BY p.id DESC
");

if ($searchQuery !== '') {
    $stmt->execute(['search' => "%{$searchQuery}%"]);
} else {
    $stmt->execute();
}
$products = $stmt->fetchAll();

// Fetch all categories for the dropdown
$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="outfit fw-bold text-main mb-0">Products</h1>
        <p class="text-muted mb-0">Manage your store's inventory</p>
    </div>
    <div class="d-flex gap-2">
        <a href="export_csv.php?type=products" class="adm-btn adm-btn-ghost"><i class="fas fa-file-export me-2"></i> Export CSV</a>
        <button class="btn btn-glow" onclick="openProductModal()">
            <i class="fas fa-plus me-2"></i> Add Product
        </button>
    </div>
</div>

<div class="adm-panel p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="adm-table">
            <thead style="background: var(--bg-3);">
                <tr>
                    <th class="px-4 sortable" title="Click to sort">ID</th>
                    <th class="sortable" title="Click to sort">Product</th>
                    <th class="sortable" title="Click to sort">Category</th>
                    <th class="sortable" title="Click to sort">Price</th>
                    <th class="sortable" title="Click to sort">Stock</th>
                    <th class="text-end px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                <tr>
                    <td class="px-4" style="color:var(--text-muted);font-weight:600;">#UM-<?= str_pad($product['id'],5,'0',STR_PAD_LEFT) ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div style="width: 36px; height: 36px; border-radius: 8px; overflow: hidden; background: var(--bg-3); display: flex; align-items: center; justify-content: center; margin-right: 12px; border: 1px solid var(--glass-border);">
                                <img src="<?= getProductImage($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span style="font-weight:600; color:var(--text-main); font-family:'Outfit',sans-serif;"><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                    </td>
                    <td><span class="adm-badge adm-badge-warning" style="background:var(--bg-3);color:var(--text-muted);border:1px solid var(--glass-border);"><?= htmlspecialchars($product['category_name']) ?></span></td>
                    <td style="color:var(--accent);font-weight:600;"><?= formatPrice($product['price']) ?></td>
                    <td>
                        <?php if($product['stock'] > 10): ?>
                            <span class="adm-badge adm-badge-success"><i class="fas fa-check-circle"></i> <?= $product['stock'] ?> In Stock</span>
                        <?php elseif($product['stock'] > 0): ?>
                            <span class="adm-badge adm-badge-warning"><i class="fas fa-exclamation-triangle"></i> <?= $product['stock'] ?> Low Stock</span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-danger"><i class="fas fa-times-circle"></i> Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end px-4">
                        <button class="adm-btn adm-btn-ghost" style="padding:4px 10px; font-size:0.75rem;" title="Edit" onclick='openProductModal(<?= json_encode($product) ?>)'><i class="fas fa-edit m-0"></i></button>
                        <button class="adm-btn adm-btn-danger" style="padding:4px 10px; font-size:0.75rem;" title="Delete" onclick="deleteProduct(<?= $product['id'] ?>)"><i class="fas fa-trash-alt m-0"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($products)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content adm-panel border-0 p-0" style="background: var(--card-bg);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
                <h5 class="modal-title outfit fw-bold text-main" id="productModalTitle">Add Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="productForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="prodAction" value="create">
                    <input type="hidden" name="id" id="prodId" value="">
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="adm-label">Product Name</label>
                            <input type="text" name="name" id="prodName" class="adm-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="adm-label">Category</label>
                            <select name="category_id" id="prodCategory" class="adm-form-control" required style="background-color: var(--bg-main);">
                                <option value="" disabled selected>Select a category</option>
                                <?php foreach($cats as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="adm-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" id="prodPrice" class="adm-form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="adm-label">Stock Quantity</label>
                            <input type="number" name="stock" id="prodStock" class="adm-form-control" value="0" required>
                        </div>
                        <div class="col-12">
                            <label class="adm-label">Description</label>
                            <textarea name="description" id="prodDesc" class="adm-form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="adm-label">Product Image (Optional)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="imagePreviewContainer" style="width: 60px; height: 60px; border-radius: 8px; border: 1px dashed var(--border-c); display: flex; align-items: center; justify-content: center; overflow: hidden; background: var(--bg-3); flex-shrink: 0;">
                                    <i class="fas fa-image text-muted" id="imagePreviewPlaceholder"></i>
                                    <img id="productImagePreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image" id="prodImage" class="adm-form-control" accept="image/*">
                                    <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">Leave empty to keep current image when editing. Max 2MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary border-opacity-25">
                        <button type="button" class="adm-btn adm-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="adm-btn adm-btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let prodModal;
document.addEventListener('DOMContentLoaded', () => {
    prodModal = new bootstrap.Modal(document.getElementById('productModal'));
    
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/UniMart/api/admin_products.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Product saved successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => showToast('An error occurred', 'error'));
    });
    
    // Image Preview Logic
    document.getElementById('prodImage').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('productImagePreview');
        const placeholder = document.getElementById('imagePreviewPlaceholder');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            resetImagePreview();
        }
    });
});

function resetImagePreview() {
    document.getElementById('productImagePreview').src = '';
    document.getElementById('productImagePreview').style.display = 'none';
    document.getElementById('imagePreviewPlaceholder').style.display = 'block';
}

function openProductModal(prod = null) {
    if (prod) {
        document.getElementById('productModalTitle').textContent = 'Edit Product';
        document.getElementById('prodAction').value = 'update';
        document.getElementById('prodId').value = prod.id;
        document.getElementById('prodName').value = prod.name;
        document.getElementById('prodCategory').value = prod.category_id;
        document.getElementById('prodPrice').value = prod.price;
        document.getElementById('prodStock').value = prod.stock;
        document.getElementById('prodDesc').value = prod.description;
    } else {
        document.getElementById('productModalTitle').textContent = 'Add Product';
        document.getElementById('prodAction').value = 'create';
        document.getElementById('prodId').value = '';
        document.getElementById('prodName').value = '';
        document.getElementById('prodCategory').value = '';
        document.getElementById('prodPrice').value = '';
        document.getElementById('prodStock').value = '0';
        document.getElementById('prodDesc').value = '';
    }
    document.getElementById('prodImage').value = ''; // Reset file input
    resetImagePreview(); // Clear preview
    prodModal.show();
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        
        fetch('/UniMart/api/admin_products.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Product deleted!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
