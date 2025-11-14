<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

$db = getDB();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Get max sort_order
        $maxOrder = $db->query("SELECT MAX(sort_order) as max_order FROM education")->fetch();
        $sortOrder = ($maxOrder['max_order'] ?? 0) + 1;
        
        $stmt = $db->prepare("INSERT INTO education (degree, institution, location, start_date, end_date, is_current, description, gpa, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitizeInput($_POST['degree']),
            sanitizeInput($_POST['institution']),
            sanitizeInput($_POST['location']),
            $_POST['start_date'],
            $_POST['is_current'] ? null : $_POST['end_date'],
            isset($_POST['is_current']) ? 1 : 0,
            $_POST['description'],
            sanitizeInput($_POST['gpa'] ?? ''),
            $sortOrder
        ]);
        $message = 'Education added!';
        $messageType = 'success';
    } elseif ($action === 'edit') {
        $isCurrent = isset($_POST['is_current']) ? 1 : 0;
        $endDate = $isCurrent ? null : ($_POST['end_date'] ?? null);
        
        $stmt = $db->prepare("UPDATE education SET degree = ?, institution = ?, location = ?, start_date = ?, end_date = ?, is_current = ?, description = ?, gpa = ? WHERE id = ?");
        $stmt->execute([
            sanitizeInput($_POST['degree']),
            sanitizeInput($_POST['institution']),
            sanitizeInput($_POST['location']),
            $_POST['start_date'],
            $endDate,
            $isCurrent,
            $_POST['description'],
            sanitizeInput($_POST['gpa'] ?? ''),
            (int)$_POST['id']
        ]);
        $message = 'Education updated!';
        $messageType = 'success';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM education WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = 'Education deleted!';
        $messageType = 'success';
    } elseif ($action === 'reorder') {
        // Update sort order for drag & drop
        $orders = json_decode($_POST['orders'], true);
        foreach ($orders as $order) {
            $stmt = $db->prepare("UPDATE education SET sort_order = ? WHERE id = ?");
            $stmt->execute([$order['position'], $order['id']]);
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

$education = $db->query("SELECT * FROM education ORDER BY sort_order ASC, start_date DESC")->fetchAll();

$pageTitle = 'Education Management';
$activePage = 'education';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Education</button>';
ob_start();
?>

<style>
.draggable-item {
    cursor: move;
    transition: all 0.3s ease;
}

.draggable-item:hover {
    background-color: #f8f9fa;
}

.draggable-item::before {
    content: '⋮⋮';
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 18px;
    font-weight: bold;
}

.draggable-item {
    position: relative;
    padding-left: 35px !important;
}
</style>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <h5 class="mb-4">Education History (<?= count($education) ?>)</h5>
    
    <?php if (empty($education)): ?>
        <div class="text-center py-5">
            <i class="bi bi-mortarboard" style="font-size: 48px; color: #ccc;"></i>
            <p class="text-muted mt-3">No education added yet.</p>
        </div>
    <?php else: ?>
        <div class="list-group" id="educationList">
            <?php foreach ($education as $edu): ?>
                <div class="list-group-item draggable-item" data-id="<?= $edu['id'] ?>" draggable="true">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?= htmlspecialchars($edu['degree']) ?></h6>
                            <p class="mb-1 text-muted">
                                <i class="bi bi-building"></i> <?= htmlspecialchars($edu['institution']) ?>
                                <?php if ($edu['location']): ?>
                                    | <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($edu['location']) ?>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i>
                                <?= date('Y', strtotime($edu['start_date'])) ?> - 
                                <?= $edu['is_current'] ? 'Present' : date('Y', strtotime($edu['end_date'])) ?>
                            </small>
                            <?php if ($edu['description']): ?>
                                <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="ms-3">
                            <button class="btn btn-sm btn-warning edit-btn" data-item='<?= htmlspecialchars(json_encode($edu), ENT_QUOTES) ?>'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $edu['id'] ?>" data-title="<?= htmlspecialchars($edu['degree']) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Degree *</label>
                            <input type="text" class="form-control" name="degree" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Institution *</label>
                            <input type="text" class="form-control" name="institution" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="add_end_date">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_current" id="add_is_current" onchange="toggleEndDate('add')">
                                <label class="form-check-label" for="add_is_current">Currently studying</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GPA</label>
                        <input type="text" class="form-control" name="gpa" placeholder="e.g., 3.53">
                        <small class="text-muted">Optional - Grade Point Average</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Enter achievements, activities, or relevant coursework..."></textarea>
                        <small class="text-muted">Enter each point on a new line. Will be displayed as bullet points.</small>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Degree *</label>
                            <input type="text" class="form-control" name="degree" id="edit_degree" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Institution *</label>
                            <input type="text" class="form-control" name="institution" id="edit_institution" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="edit_location">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="edit_end_date">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_current" id="edit_is_current" onchange="toggleEndDate('edit')">
                                <label class="form-check-label" for="edit_is_current">Currently studying</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GPA</label>
                        <input type="text" class="form-control" name="gpa" id="edit_gpa" placeholder="e.g., 3.53">
                        <small class="text-muted">Optional - Grade Point Average</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="5" placeholder="Enter achievements, activities, or relevant coursework..."></textarea>
                        <small class="text-muted">Enter each point on a new line. Will be displayed as bullet points.</small>
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
// Edit button click handler
document.addEventListener('click', function(e) {
    if (e.target.closest('.edit-btn')) {
        const btn = e.target.closest('.edit-btn');
        const item = JSON.parse(btn.dataset.item);
        editItem(item);
    }
    
    if (e.target.closest('.delete-btn')) {
        const btn = e.target.closest('.delete-btn');
        deleteItem(btn.dataset.id, btn.dataset.title);
    }
});

function editItem(item) {
    console.log('Editing education:', item);
    
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_degree').value = item.degree;
    document.getElementById('edit_institution').value = item.institution;
    document.getElementById('edit_location').value = item.location || '';
    document.getElementById('edit_start_date').value = item.start_date;
    document.getElementById('edit_end_date').value = item.end_date || '';
    document.getElementById('edit_is_current').checked = item.is_current == 1;
    document.getElementById('edit_gpa').value = item.gpa || '';
    document.getElementById('edit_description').value = item.description || '';
    
    toggleEndDate('edit');
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

function deleteItem(id, title) {
    if (confirm(`Delete "${title}"?`)) {
        document.getElementById('delete_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function toggleEndDate(prefix) {
    const checkbox = document.getElementById(prefix + '_is_current');
    const endDate = document.getElementById(prefix + '_end_date');
    endDate.disabled = checkbox.checked;
    if (checkbox.checked) endDate.value = '';
}

// Drag & Drop functionality
let draggedElement = null;

const list = document.getElementById('educationList');
if (list) {
    const items = list.querySelectorAll('.draggable-item');
    
    items.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
        });
        
        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            saveOrder();
        });
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            const afterElement = getDragAfterElement(list, e.clientY);
            if (afterElement == null) {
                list.appendChild(draggedElement);
            } else {
                list.insertBefore(draggedElement, afterElement);
            }
        });
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function saveOrder() {
    const items = document.querySelectorAll('.draggable-item');
    const orders = [];
    
    items.forEach((item, index) => {
        orders.push({
            id: parseInt(item.dataset.id),
            position: index + 1
        });
    });
    
    // Send to server
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reorder&orders=' + encodeURIComponent(JSON.stringify(orders))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order saved successfully');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
