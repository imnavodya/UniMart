<?php 
require_once 'includes/header.php'; 

$stmt = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="outfit fw-bold text-main mb-0">Categories</h1>
        <p class="text-muted mb-0">Manage product categories</p>
    </div>
    <button class="btn btn-glow" onclick="openCategoryModal()">
        <i class="fas fa-plus me-2"></i> Add Category
    </button>
</div>

<div class="adm-panel p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="adm-table">
            <thead>
                <tr>
                    <th class="px-4">ID</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th class="text-end px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $category): ?>
                <tr>
                    <td class="px-4" style="color:var(--text-muted);font-weight:600;">#CAT-<?= str_pad($category['id'],3,'0',STR_PAD_LEFT) ?></td>
                    <td>
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-glow); color: var(--primary); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border);">
                            <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:600; color:var(--text-main); font-family:'Outfit',sans-serif;"><?= htmlspecialchars($category['name']) ?></span>
                    </td>
                    <td class="text-end px-4">
                        <button class="adm-btn adm-btn-ghost" style="padding:4px 10px; font-size:0.75rem;" title="Edit" onclick='openCategoryModal(<?= json_encode($category) ?>)'><i class="fas fa-edit m-0"></i></button>
                        <button class="adm-btn adm-btn-danger" style="padding:4px 10px; font-size:0.75rem;" title="Delete" onclick="deleteCategory(<?= $category['id'] ?>)"><i class="fas fa-trash-alt m-0"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($categories)): ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--text-muted);padding:40px;">No categories found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content adm-panel border-0 p-0" style="background: var(--card-bg);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
                <h5 class="modal-title outfit fw-bold text-main" id="categoryModalTitle">Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="categoryForm">
                    <input type="hidden" name="action" id="catAction" value="create">
                    <input type="hidden" name="id" id="catId" value="">
                    
                    <div class="mb-3">
                        <label class="adm-label">Category Name</label>
                        <input type="text" name="name" id="catName" class="adm-form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="adm-label">Icon Class (FontAwesome)</label>
                        <input type="text" name="icon" id="catIcon" class="adm-form-control" value="fas fa-folder" required>
                        <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">Example: <code>fas fa-laptop</code>, <code>fas fa-box</code></div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="adm-btn adm-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="adm-btn adm-btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let catModal;
document.addEventListener('DOMContentLoaded', () => {
    catModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/UniMart/api/admin_categories.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => alert('An error occurred'));
    });
});

function openCategoryModal(cat = null) {
    if (cat) {
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        document.getElementById('catAction').value = 'update';
        document.getElementById('catId').value = cat.id;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catIcon').value = cat.icon;
    } else {
        document.getElementById('categoryModalTitle').textContent = 'Add Category';
        document.getElementById('catAction').value = 'create';
        document.getElementById('catId').value = '';
        document.getElementById('catName').value = '';
        document.getElementById('catIcon').value = 'fas fa-folder';
    }
    catModal.show();
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category? Products inside it will also be deleted!')) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        
        fetch('/UniMart/api/admin_categories.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message);
            });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
