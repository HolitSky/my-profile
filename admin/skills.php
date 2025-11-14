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
        $percentage = (int)$_POST['percentage'];
        $category = sanitizeInput($_POST['category']);
        
        $stmt = $db->prepare("INSERT INTO skills (name, percentage, category) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $percentage, $category])) {
            $message = 'Skill added successfully!';
            $messageType = 'success';
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitizeInput($_POST['name']);
        $percentage = (int)$_POST['percentage'];
        $category = sanitizeInput($_POST['category']);
        
        $stmt = $db->prepare("UPDATE skills SET name = ?, percentage = ?, category = ? WHERE id = ?");
        if ($stmt->execute([$name, $percentage, $category, $id])) {
            $message = 'Skill updated successfully!';
            $messageType = 'success';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM skills WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Skill deleted successfully!';
            $messageType = 'success';
        }
    }
}

// Get all skills
$skills = $db->query("SELECT * FROM skills ORDER BY sort_order ASC, id DESC")->fetchAll();

$pageTitle = 'Skills Management';
$activePage = 'skills';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Skill</button>';
ob_start();
?>

<style>
    .skill-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .skill-progress {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width 0.3s;
    }
</style>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

                    <div class="content-card">
                        <h5 class="mb-4">All Skills (<?= count($skills) ?>)</h5>
                        
                        <?php if (empty($skills)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-star" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-3">No skills added yet. Click "Add Skill" to get started.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Skill Name</th>
                                            <th>Category</th>
                                            <th>Proficiency</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($skills as $skill): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($skill['name']) ?></strong></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($skill['category']) ?></span></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="skill-bar flex-grow-1 me-2" style="width: 150px;">
                                                            <div class="skill-progress" style="width: <?= $skill['percentage'] ?>%"></div>
                                                        </div>
                                                        <span><?= $skill['percentage'] ?>%</span>
                                                    </div>
                                                </td>
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
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Skill</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" required>
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Database">Database</option>
                                <option value="Tools">Tools</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proficiency (%) *</label>
                            <input type="number" class="form-control" name="percentage" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Skill</button>
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
                        <h5 class="modal-title">Edit Skill</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" id="edit_category" required>
                                <option value="Frontend">Frontend</option>
                                <option value="Backend">Backend</option>
                                <option value="Database">Database</option>
                                <option value="Tools">Tools</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proficiency (%) *</label>
                            <input type="number" class="form-control" name="percentage" id="edit_percentage" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Skill</button>
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
            document.getElementById('edit_category').value = skill.category;
            document.getElementById('edit_percentage').value = skill.percentage;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function deleteSkill(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
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
