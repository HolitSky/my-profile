<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle image upload with WebP conversion
function handleImageUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $uploadDir = __DIR__ . '/../assets/images/';
    $optimizedDir = __DIR__ . '/../assets/images/optimized/';
    
    // Create directories if not exist
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    if (!is_dir($optimizedDir)) mkdir($optimizedDir, 0755, true);
    
    $fileName = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return null;
    }
    
    // Convert to WebP using GD (fallback if sharp not available)
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $webpPath = $optimizedDir . $baseName . '.webp';
    
    try {
        if ($fileExt === 'jpg' || $fileExt === 'jpeg') {
            $image = imagecreatefromjpeg($filePath);
        } elseif ($fileExt === 'png') {
            $image = imagecreatefrompng($filePath);
        } else {
            return ['image' => './assets/images/' . $fileName, 'webp' => null];
        }
        
        if ($image) {
            imagewebp($image, $webpPath, 80);
            imagedestroy($image);
        }
        
        return [
            'image' => './assets/images/' . $fileName,
            'webp' => './assets/images/optimized/' . $baseName . '.webp'
        ];
    } catch (Exception $e) {
        return ['image' => './assets/images/' . $fileName, 'webp' => null];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        // Handle image upload
        $imagePaths = null;
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $imagePaths = handleImageUpload($_FILES['image_file']);
        }
        
        $image = $imagePaths ? $imagePaths['image'] : sanitizeInput($_POST['image'] ?? '');
        $imageWebp = $imagePaths ? $imagePaths['webp'] : sanitizeInput($_POST['image_webp'] ?? '');
        
        if ($action === 'add') {
            // Get max sort_order
            $maxOrder = $db->query("SELECT MAX(sort_order) as max_order FROM portfolio")->fetch();
            $sortOrder = ($maxOrder['max_order'] ?? 0) + 1;
            
            $stmt = $db->prepare("INSERT INTO portfolio (title, category, description, image, image_webp, demo_url, github_url, technologies, featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                sanitizeInput($_POST['title']),
                sanitizeInput($_POST['category']),
                $_POST['description'],
                $image,
                $imageWebp,
                sanitizeInput($_POST['demo_url'] ?? ''),
                sanitizeInput($_POST['github_url'] ?? ''),
                sanitizeInput($_POST['technologies'] ?? ''),
                isset($_POST['featured']) ? 1 : 0,
                $sortOrder
            ]);
            $message = 'Project added successfully!';
        } else {
            // If no new image uploaded, keep existing
            if (!$imagePaths) {
                $image = sanitizeInput($_POST['existing_image']);
                $imageWebp = sanitizeInput($_POST['existing_image_webp']);
            }
            
            $stmt = $db->prepare("UPDATE portfolio SET title = ?, category = ?, description = ?, image = ?, image_webp = ?, demo_url = ?, github_url = ?, technologies = ?, featured = ? WHERE id = ?");
            $stmt->execute([
                sanitizeInput($_POST['title']),
                sanitizeInput($_POST['category']),
                $_POST['description'],
                $image,
                $imageWebp,
                sanitizeInput($_POST['demo_url'] ?? ''),
                sanitizeInput($_POST['github_url'] ?? ''),
                sanitizeInput($_POST['technologies'] ?? ''),
                isset($_POST['featured']) ? 1 : 0,
                (int)$_POST['id']
            ]);
            $message = 'Project updated successfully!';
        }
        $messageType = 'success';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM portfolio WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $message = 'Project deleted successfully!';
        $messageType = 'success';
    } elseif ($action === 'reorder') {
        $orders = json_decode($_POST['orders'], true);
        foreach ($orders as $order) {
            $stmt = $db->prepare("UPDATE portfolio SET sort_order = ? WHERE id = ?");
            $stmt->execute([$order['position'], $order['id']]);
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

$projects = $db->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id DESC")->fetchAll();

$pageTitle = 'Portfolio Management';
$activePage = 'portfolio';
$headerButton = '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle"></i> Add Project</button>';
ob_start();
?>

<style>
.draggable-item {
    cursor: move;
    transition: all 0.3s ease;
}
.draggable-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.draggable-item::before {
    content: '⋮⋮';
    position: absolute;
    left: 10px;
    top: 10px;
    color: #999;
    font-size: 18px;
    font-weight: bold;
    z-index: 10;
}
.card {
    position: relative;
    padding-left: 30px;
}
</style>

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
        <div class="row g-3" id="portfolioList">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-4 draggable-item" data-id="<?= $project['id'] ?>" draggable="true">
                    <div class="card h-100">
                        <?php if ($project['image']): ?>
                            <img src="../<?= htmlspecialchars($project['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($project['title']) ?>" style="height: 200px; object-fit: cover;">
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
                                <button class="btn btn-sm btn-warning edit-btn" data-item='<?= htmlspecialchars(json_encode($project), ENT_QUOTES) ?>'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $project['id'] ?>" data-title="<?= htmlspecialchars($project['title']) ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php if ($project['demo_url']): ?>
                                    <a href="<?= htmlspecialchars($project['demo_url']) ?>" target="_blank" class="btn btn-sm btn-info">
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
            <form method="POST" enctype="multipart/form-data">
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
                                <option value="Mobile Development">Mobile Development</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Project description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Image (JPG/PNG - will auto-convert to WebP)</label>
                        <input type="file" class="form-control" name="image_file" accept="image/jpeg,image/jpg,image/png">
                        <small class="text-muted">Or enter image URL below</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image URL (if not uploading)</label>
                        <input type="text" class="form-control" name="image" placeholder="./assets/images/project.png">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Demo URL</label>
                            <input type="url" class="form-control" name="demo_url" placeholder="https://demo.example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" class="form-control" name="github_url" placeholder="https://github.com/user/repo">
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
                    <button type="submit" class="btn btn-primary">Add Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                <input type="hidden" name="existing_image_webp" id="edit_existing_image_webp">
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
                                <option value="Mobile Development">Mobile Development</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div id="current_image_preview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload New Image (optional)</label>
                        <input type="file" class="form-control" name="image_file" accept="image/jpeg,image/jpg,image/png">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Demo URL</label>
                            <input type="url" class="form-control" name="demo_url" id="edit_demo_url">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GitHub URL</label>
                            <input type="url" class="form-control" name="github_url" id="edit_github_url">
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
                    <button type="submit" class="btn btn-primary">Update Project</button>
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
    console.log('Editing project:', item);
    
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_category').value = item.category;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_existing_image').value = item.image || '';
    document.getElementById('edit_existing_image_webp').value = item.image_webp || '';
    document.getElementById('edit_demo_url').value = item.demo_url || '';
    document.getElementById('edit_github_url').value = item.github_url || '';
    document.getElementById('edit_technologies').value = item.technologies || '';
    document.getElementById('edit_featured').checked = item.featured == 1;
    
    // Show current image preview
    const preview = document.getElementById('current_image_preview');
    if (item.image) {
        preview.innerHTML = `<img src="../${item.image}" class="img-thumbnail" style="max-height: 100px;">`;
    } else {
        preview.innerHTML = '<p class="text-muted">No image</p>';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

function deleteItem(id, title) {
    if (confirm(`Delete "${title}"?`)) {
        document.getElementById('delete_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Drag & Drop functionality
let draggedElement = null;

const list = document.getElementById('portfolioList');
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
