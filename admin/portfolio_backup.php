<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$db = getDB();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $data = [
            sanitizeInput($_POST['title']),
            sanitizeInput($_POST['category']),
            $_POST['description'],
            sanitizeInput($_POST['image']),
            sanitizeInput($_POST['link']),
            sanitizeInput($_POST['github_link']),
            sanitizeInput($_POST['technologies']),
            isset($_POST['featured']) ? 1 : 0
        ];
        
        if ($action === 'add') {
            $stmt = $db->prepare("INSERT INTO portfolio (title, category, description, image, link, github_link, technologies, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($data);
            $message = 'Project added!';
        } else {
            $data[] = (int)$_POST['id'];
            $stmt = $db->prepare("UPDATE portfolio SET title = ?, category = ?, description = ?, image = ?, link = ?, github_link = ?, technologies = ?, featured = ? WHERE id = ?");
            $stmt->execute($data);
            $message = 'Project updated!';
        }
        $messageType = 'success';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM portfolio WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = 'Project deleted!';
        $messageType = 'success';
    }
}

$projects = $db->query("SELECT * FROM portfolio ORDER BY featured DESC, id DESC")->fetchAll();

$pageTitle = 'Portfolio Management';
$activePage = 'portfolio';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Project</button>';
ob_start();
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <h5 class="mb-4">Portfolio Projects (<?= count($projects) ?>)</h5>
    
    <?php if (empty($projects)): ?>
        <div class="text-center py-5">
            <i class="bi bi-folder" style="font-size: 48px; color: #ccc;"></i>
            <p class="text-muted mt-3">No projects added yet.</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if ($project['image']): ?>
                            <img src="<?= htmlspecialchars($project['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($project['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0"><?= htmlspecialchars($project['title']) ?></h6>
                                <?php if ($project['featured']): ?>
                                    <span class="badge bg-warning">Featured</span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text"><small class="text-muted"><?= htmlspecialchars($project['category']) ?></small></p>
                            <?php if ($project['description']): ?>
                                <p class="card-text small"><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>
                            <?php endif; ?>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-warning" onclick='editItem(<?= json_encode($project) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteItem(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php if ($project['link']): ?>
                                    <a href="<?= htmlspecialchars($project['link']) ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                <?php endif; ?>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Project Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" required>
                                <option value="Web Development">Web Development</option>
                                <option value="Mobile App">Mobile App</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control" name="image" placeholder="./assets/images/project.jpg">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project URL</label>
                            <input type="url" class="form-control" name="link" placeholder="https://example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" class="form-control" name="github_link" placeholder="https://github.com/user/repo">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Technologies (comma separated)</label>
                        <input type="text" class="form-control" name="technologies" placeholder="React, Node.js, MongoDB">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="add_featured">
                        <label class="form-check-label" for="add_featured">Featured Project</label>
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
                    <h5 class="modal-title">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Project Title *</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" id="edit_category" required>
                                <option value="Web Development">Web Development</option>
                                <option value="Mobile App">Mobile App</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control" name="image" id="edit_image">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project URL</label>
                            <input type="url" class="form-control" name="link" id="edit_link">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" class="form-control" name="github_link" id="edit_github_link">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Technologies</label>
                        <input type="text" class="form-control" name="technologies" id="edit_technologies">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="edit_featured">
                        <label class="form-check-label" for="edit_featured">Featured Project</label>
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
    document.getElementById('edit_category').value = item.category;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_image').value = item.image || '';
    document.getElementById('edit_link').value = item.link || '';
    document.getElementById('edit_github_link').value = item.github_link || '';
    document.getElementById('edit_technologies').value = item.technologies || '';
    document.getElementById('edit_featured').checked = item.featured == 1;
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
