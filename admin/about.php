<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = $_POST['description']; // Keep HTML
    
    // Check if about exists
    $existing = $db->query("SELECT id FROM about LIMIT 1")->fetch();
    
    if ($existing) {
        $stmt = $db->prepare("UPDATE about SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $existing['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO about (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $description]);
    }
    
    $message = 'About section updated successfully!';
    $messageType = 'success';
}

// Get current about data
$about = $db->query("SELECT * FROM about LIMIT 1")->fetch();

$pageTitle = 'About Management';
$activePage = 'about';
ob_start();
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <h5 class="mb-4">Edit About Section</h5>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($about['title'] ?? 'About me') ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="10" required><?= htmlspecialchars($about['description'] ?? '') ?></textarea>
            <small class="text-muted">You can use HTML tags for formatting.</small>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Save Changes
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
