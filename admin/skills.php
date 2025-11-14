<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = sanitizeInput($_POST['name']);
        $icon = '';
        
        // Handle icon upload
        if (isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/icon/';
            $fileName = basename($_FILES['icon_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['icon_file']['tmp_name'], $targetPath)) {
                $icon = './assets/images/icon/' . $fileName;
            }
        } elseif (!empty($_POST['icon'])) {
            $icon = sanitizeInput($_POST['icon']);
        }
        
        if (!empty($icon)) {
            $stmt = $db->prepare("INSERT INTO skills (name, icon) VALUES (?, ?)");
            if ($stmt->execute([$name, $icon])) {
                $message = 'Technology added successfully!';
                $messageType = 'success';
            }
        } else {
            $message = 'Please upload an icon or provide icon path';
            $messageType = 'danger';
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitizeInput($_POST['name']);
        $icon = sanitizeInput($_POST['icon']);
        
        // Handle icon upload
        if (isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/icon/';
            $fileName = basename($_FILES['icon_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['icon_file']['tmp_name'], $targetPath)) {
                $icon = './assets/images/icon/' . $fileName;
            }
        }
        
        $stmt = $db->prepare("UPDATE skills SET name = ?, icon = ? WHERE id = ?");
        if ($stmt->execute([$name, $icon, $id])) {
            $message = 'Technology updated successfully!';
            $messageType = 'success';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM skills WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Technology deleted successfully!';
            $messageType = 'success';
        }
    } elseif ($action === 'update_order') {
        $order = json_decode($_POST['order'], true);
        if ($order) {
            foreach ($order as $index => $id) {
                $stmt = $db->prepare("UPDATE skills SET sort_order = ? WHERE id = ?");
                $stmt->execute([$index + 1, $id]);
            }
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

// Get all skills
$skills = $db->query("SELECT * FROM skills ORDER BY sort_order ASC, id DESC")->fetchAll();

$pageTitle = 'Technologies Management';
$activePage = 'skills';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Technology</button>';
ob_start();
?>

<style>
    .tech-icon {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }
    .sortable-list tbody tr {
        cursor: move;
        transition: background-color 0.2s;
    }
    .sortable-list tbody tr:hover {
        background-color: #f8f9fa;
    }
    .sortable-list tbody tr.dragging {
        opacity: 0.5;
        background-color: #e9ecef;
    }
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        margin-right: 8px;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
</style>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

                    <div class="content-card">
                        <h5 class="mb-4">Technologies I Work With (<?= count($skills) ?>)</h5>
                        
                        <?php if (empty($skills)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-code-square" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-3">No technologies added yet. Click "Add Technology" to get started.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> <strong>Tip:</strong> Drag and drop rows to reorder technologies
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover sortable-list">
                                    <thead>
                                        <tr>
                                            <th width="50"></th>
                                            <th width="80">Icon</th>
                                            <th>Technology Name</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-tbody">
                                        <?php foreach ($skills as $skill): ?>
                                            <tr data-id="<?= $skill['id'] ?>" draggable="true">
                                                <td class="text-center">
                                                    <i class="bi bi-grip-vertical drag-handle"></i>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Adjust path for admin folder
                                                    $iconPath = $skill['icon'];
                                                    if (strpos($iconPath, './assets') === 0) {
                                                        $iconPath = '../' . substr($iconPath, 2);
                                                    }
                                                    ?>
                                                    <img src="<?= htmlspecialchars($iconPath) ?>" alt="<?= htmlspecialchars($skill['name']) ?>" class="tech-icon">
                                                </td>
                                                <td><strong><?= htmlspecialchars($skill['name']) ?></strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" onclick="editSkill(<?= htmlspecialchars(json_encode($skill)) ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteSkill(<?= $skill['id'] ?>, '<?= htmlspecialchars($skill['name']) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Technology</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Technology Name *</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. React, Laravel, MySQL" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon Upload *</label>
                            <input type="file" class="form-control" name="icon_file" accept="image/svg+xml,image/png,image/jpeg" id="add_icon_file">
                            <small class="text-muted">Upload SVG, PNG, or JPG (SVG recommended)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Icon Path</label>
                            <input type="text" class="form-control" name="icon" placeholder="./assets/images/icon/react-icon.svg" id="add_icon_path">
                            <small class="text-muted">Leave empty if uploading file above</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Technology</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Technology</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Technology Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Icon</label>
                            <div>
                                <img id="icon_preview" src="" alt="Icon preview" class="tech-icon">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload New Icon</label>
                            <input type="file" class="form-control" name="icon_file" accept="image/svg+xml,image/png,image/jpeg" id="edit_icon_file">
                            <small class="text-muted">Leave empty to keep current icon</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Icon Path</label>
                            <input type="text" class="form-control" name="icon" id="edit_icon">
                            <small class="text-muted">Current path (will be replaced if uploading new file)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Technology</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSkill(skill) {
            document.getElementById('edit_id').value = skill.id;
            document.getElementById('edit_name').value = skill.name;
            document.getElementById('edit_icon').value = skill.icon;
            
            // Adjust path for preview
            let iconPath = skill.icon;
            if (iconPath.startsWith('./assets')) {
                iconPath = '../' + iconPath.substring(2);
            }
            document.getElementById('icon_preview').src = iconPath;
            
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        }

        function deleteSkill(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Live icon preview
        document.getElementById('edit_icon').addEventListener('input', function() {
            let iconPath = this.value;
            if (iconPath.startsWith('./assets')) {
                iconPath = '../' + iconPath.substring(2);
            }
            document.getElementById('icon_preview').src = iconPath;
        });

        // Drag and Drop functionality
        const tbody = document.getElementById('sortable-tbody');
        if (tbody) {
            let draggedElement = null;

            tbody.addEventListener('dragstart', function(e) {
                if (e.target.tagName === 'TR') {
                    draggedElement = e.target;
                    e.target.classList.add('dragging');
                }
            });

            tbody.addEventListener('dragend', function(e) {
                if (e.target.tagName === 'TR') {
                    e.target.classList.remove('dragging');
                }
            });

            tbody.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = getDragAfterElement(tbody, e.clientY);
                if (afterElement == null) {
                    tbody.appendChild(draggedElement);
                } else {
                    tbody.insertBefore(draggedElement, afterElement);
                }
            });

            tbody.addEventListener('drop', function(e) {
                e.preventDefault();
                saveOrder();
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('tr:not(.dragging)')];
                
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
                const rows = tbody.querySelectorAll('tr');
                const order = Array.from(rows).map(row => row.dataset.id);
                
                fetch('skills.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=update_order&order=' + JSON.stringify(order)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Order updated successfully');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
