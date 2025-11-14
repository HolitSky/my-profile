<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

$pageTitle = 'Password Generator';
$activePage = 'security';
$hashedPassword = '';
$plainPassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plainPassword = $_POST['password'] ?? '';
    if (!empty($plainPassword)) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
    }
}

ob_start();
?>

<style>
.password-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #0c0f38;
    margin: 15px 0;
}
.password-box label {
    font-weight: 600;
    color: #0c0f38;
    margin-bottom: 8px;
    display: block;
}
.password-display {
    background: white;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    word-break: break-all;
    margin-top: 8px;
}
.copy-btn {
    margin-top: 10px;
}
</style>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2">🔐 Password Hash Generator</h5>
            <p class="text-muted mb-0">Generate bcrypt password hashes for admin users</p>
        </div>
        <a href="security.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Security
        </a>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-key-fill"></i> Enter Password
            </label>
            <input type="text" class="form-control" id="password" name="password" 
                   placeholder="Enter password to hash" required 
                   value="<?= htmlspecialchars($plainPassword) ?>">
            <small class="text-muted">Enter the plain text password you want to hash</small>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-arrow-repeat"></i> Generate Hash
        </button>
    </form>

    <?php if ($hashedPassword): ?>
        <div class="password-box mt-4">
            <label>Plain Password:</label>
            <div class="password-display">
                <?= htmlspecialchars($plainPassword) ?>
            </div>
        </div>

        <div class="password-box">
            <label>Bcrypt Hash:</label>
            <div class="password-display" id="hashedPassword">
                <?= htmlspecialchars($hashedPassword) ?>
            </div>
            <button type="button" class="btn btn-sm btn-success copy-btn" onclick="copyHash()">
                <i class="bi bi-clipboard"></i> Copy Hash
            </button>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Usage:</strong> Copy the hash above and use it in your database seeder or update query.
            <br><br>
            <strong>Example SQL:</strong>
            <pre class="mb-0 mt-2" style="background: white; padding: 10px; border-radius: 5px;">UPDATE admin_users SET password = '<?= htmlspecialchars($hashedPassword) ?>' WHERE username = 'admin';</pre>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Instructions:</strong>
            <ul class="mb-0 mt-2">
                <li>Enter a plain text password above</li>
                <li>Click "Generate Hash" to create a bcrypt hash</li>
                <li>Copy the generated hash</li>
                <li>Use it in your database or seeder file</li>
            </ul>
        </div>
    <?php endif; ?>

    <div class="alert alert-info mt-4">
        <i class="bi bi-shield-lock-fill"></i>
        <strong>Security Note:</strong> This tool is only accessible to logged-in administrators. 
        Never share password hashes publicly.
    </div>
</div>

<script>
function copyHash() {
    const hashText = document.getElementById('hashedPassword').textContent.trim();
    navigator.clipboard.writeText(hashText).then(() => {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
        }, 2000);
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
