<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$db = getDB();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO services (title, description, icon) VALUES (?, ?, ?)");
        $stmt->execute([
            sanitizeInput($_POST['title']),
            $_POST['description'],
            sanitizeInput($_POST['icon'])
        ]);
        $message = 'Service added!';
        $messageType = 'success';
    } elseif ($action === 'edit') {
        $stmt = $db->prepare("UPDATE services SET title = ?, description = ?, icon = ? WHERE id = ?");
        $stmt->execute([
            sanitizeInput($_POST['title']),
            $_POST['description'],
            sanitizeInput($_POST['icon']),
            (int)$_POST['id']
        ]);
        $message = 'Service updated!';
        $messageType = 'success';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = 'Service deleted!';
        $messageType = 'success';
    }
}

$services = $db->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();

$pageTitle = 'Services Management';
$activePage = 'services';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Service</button>';
ob_start();
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <h5 class="mb-4">Services (<?= count($services) ?>)</h5>
    
    <?php if (empty($services)): ?>
        <div class="text-center py-5">
            <i class="bi bi-gear" style="font-size: 48px; color: #ccc;"></i>
            <p class="text-muted mt-3">No services added yet.</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title"><?= htmlspecialchars($service['title']) ?></h6>
                                    <p class="card-text text-muted small"><?= htmlspecialchars($service['description']) ?></p>
                                    <?php if ($service['icon']): ?>
                                        <small class="text-muted">Icon: <?= htmlspecialchars($service['icon']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="ms-3">
                                    <button class="btn btn-sm btn-warning" onclick='editItem(<?= json_encode($service) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem(<?= $service['id'] ?>, '<?= htmlspecialchars($service['title']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Service Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Name</label>
                        <input type="text" class="form-control" name="icon" placeholder="icon-design.svg">
                        <small class="text-muted">Icon file name from assets/images/</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Service Title *</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Name</label>
                        <input type="text" class="form-control" name="icon" id="edit_icon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form method="POST" id="deleteForm" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete_id">
</form>

<script>
function editItem(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_description').value = item.description;
    document.getElementById('edit_icon').value = item.icon || '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteItem(id, title) {
    if (confirm(`Delete "${title}"?`)) {
        document.getElementById('delete_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
