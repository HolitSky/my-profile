<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $birthday = $_POST['birthday'];
    $location = sanitizeInput($_POST['location']);
    $linkedin = sanitizeInput($_POST['linkedin']);
    $instagram = sanitizeInput($_POST['instagram']);
    $github = sanitizeInput($_POST['github'] ?? '');
    
    // Check if contact exists
    $existing = $db->query("SELECT id FROM contact_info LIMIT 1")->fetch();
    
    if ($existing) {
        $stmt = $db->prepare("UPDATE contact_info SET email = ?, phone = ?, birthday = ?, location = ?, linkedin = ?, instagram = ?, github = ? WHERE id = ?");
        $stmt->execute([$email, $phone, $birthday, $location, $linkedin, $instagram, $github, $existing['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO contact_info (email, phone, birthday, location, linkedin, instagram, github) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$email, $phone, $birthday, $location, $linkedin, $instagram, $github]);
    }
    
    $message = 'Contact information updated successfully!';
    $messageType = 'success';
}

// Get current contact data
$contact = $db->query("SELECT * FROM contact_info LIMIT 1")->fetch();

$pageTitle = 'Contact Information';
$activePage = 'contact';
ob_start();
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <h5 class="mb-4">Edit Contact Information</h5>
    
    <form method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone *</label>
                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Birthday</label>
                <input type="date" class="form-control" name="birthday" value="<?= $contact['birthday'] ?? '' ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Location *</label>
                <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($contact['location'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">LinkedIn URL</label>
            <input type="url" class="form-control" name="linkedin" value="<?= htmlspecialchars($contact['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Instagram URL</label>
            <input type="url" class="form-control" name="instagram" value="<?= htmlspecialchars($contact['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
        </div>
        
        <div class="mb-3">
            <label class="form-label">GitHub URL</label>
            <input type="url" class="form-control" name="github" value="<?= htmlspecialchars($contact['github'] ?? '') ?>" placeholder="https://github.com/username">
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
