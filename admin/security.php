<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Check access permission
require_once __DIR__ . '/../security-config.php';

$status = getSecurityStatus();
$configFile = __DIR__ . '/../security-config.php';
$action = $_GET['action'] ?? '';
$message = '';
$messageType = '';

// Handle toggle action
if ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = file_get_contents($configFile);
    
    if (ENABLE_UTILITY_FILES) {
        // Disable (Production Mode)
        $content = str_replace(
            "define('ENABLE_UTILITY_FILES', true);",
            "define('ENABLE_UTILITY_FILES', false);",
            $content
        );
        $message = 'Production Mode Enabled! Utility files are now blocked.';
        $messageType = 'success';
    } else {
        // Enable (Development Mode)
        $content = str_replace(
            "define('ENABLE_UTILITY_FILES', false);",
            "define('ENABLE_UTILITY_FILES', true);",
            $content
        );
        $message = 'Development Mode Enabled! Utility files are now accessible.';
        $messageType = 'success';
    }
    
    file_put_contents($configFile, $content);
    header('Location: security.php?msg=' . urlencode($messageType . '|' . $message));
    exit;
}

// Get message from redirect
if (isset($_GET['msg'])) {
    list($messageType, $message) = explode('|', $_GET['msg'], 2);
}

$pageTitle = 'Security Settings';
$activePage = 'security';
ob_start();
?>

<style>
.status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
}
.badge-dev {
    background: #28a745;
    color: white;
}
.badge-prod {
    background: #dc3545;
    color: white;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}
.info-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #0c0f38;
}
.info-item h4 {
    color: #0c0f38;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 8px;
    font-weight: 600;
}
.info-item p {
    color: #333;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}
.file-list {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}
.file-list li {
    padding: 12px 15px;
    margin: 8px 0;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 3px solid #dee2e6;
}
.status-enabled {
    color: #28a745;
    font-weight: 600;
}
.status-disabled {
    color: #dc3545;
    font-weight: 600;
}
.mode-section {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin: 20px 0;
}
.mode-section h3 {
    margin-bottom: 15px;
    color: #333;
    font-size: 20px;
}
.mode-section p {
    margin: 10px 0;
    color: #666;
}
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}
.quick-link {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
    border: 2px solid #e9ecef;
}
.quick-link:hover {
    border-color: #0c0f38;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-decoration: none;
}
.quick-link h4 {
    color: #0c0f38;
    margin-bottom: 10px;
    font-size: 16px;
}
.quick-link p {
    color: #666;
    font-size: 13px;
    margin: 0;
}
</style>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <strong><?= htmlspecialchars($message) ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2">🔒 Security Settings</h5>
            <p class="text-muted mb-0">Manage utility files access control</p>
        </div>
        <span class="status-badge <?= $status['utility_files_enabled'] ? 'badge-dev' : 'badge-prod' ?>">
            <?= $status['mode'] ?> MODE
        </span>
    </div>

    <!-- Current Status -->
    <div class="mb-4">
        <h6 class="mb-3">📊 Current Status</h6>
        <div class="info-grid">
            <div class="info-item">
                <h4>Mode</h4>
                <p><?= $status['mode'] ?></p>
            </div>
            <div class="info-item">
                <h4>Utility Files</h4>
                <p><?= $status['utility_files_enabled'] ? '✅ Enabled' : '❌ Disabled' ?></p>
            </div>
            <div class="info-item">
                <h4>Environment</h4>
                <p><?= $status['is_localhost'] ? 'Localhost' : 'Remote' ?></p>
            </div>
            <div class="info-item">
                <h4>Your IP</h4>
                <p style="font-size: 14px;"><?= $status['remote_addr'] ?></p>
            </div>
        </div>
    </div>

    <!-- Protected Files -->
    <div class="mb-4">
        <h6 class="mb-3">🛡️ Protected Files Status</h6>
        <ul class="file-list">
            <li>
                <span><i class="bi bi-file-code"></i> test-db.php</span>
                <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                    <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                </span>
            </li>
            <li>
                <span><i class="bi bi-file-code"></i> generate-password.php</span>
                <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                    <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                </span>
            </li>
            <li>
                <span><i class="bi bi-file-code"></i> database/run-migrations.php</span>
                <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                    <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                </span>
            </li>
            <li>
                <span><i class="bi bi-file-code"></i> toggle-protection.php</span>
                <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                    <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                </span>
            </li>
        </ul>
    </div>

    <!-- Toggle Mode -->
    <div class="mb-4">
        <h6 class="mb-3">🔄 Toggle Security Mode</h6>
        
        <?php if ($status['utility_files_enabled']): ?>
        <div class="mode-section" style="border-left: 4px solid #28a745;">
            <h3>🟢 Development Mode (Active)</h3>
            <p>Utility files are currently accessible from localhost.</p>
            <p><strong>Switch to Production Mode to block all utility files.</strong></p>
            <form method="POST" action="?action=toggle" onsubmit="return confirm('Switch to Production Mode? This will block all utility files!')" class="mt-3">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-shield-x"></i> Enable Production Mode
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="mode-section" style="border-left: 4px solid #dc3545;">
            <h3>🔴 Production Mode (Active)</h3>
            <p>All utility files are currently blocked.</p>
            <p><strong>Switch to Development Mode to enable localhost access.</strong></p>
            <form method="POST" action="?action=toggle" onsubmit="return confirm('Switch to Development Mode? This will enable utility files access from localhost.')" class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-shield-check"></i> Enable Development Mode
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Important:</strong><br>
            • Use <strong>Development Mode</strong> when working locally<br>
            • Use <strong>Production Mode</strong> when deploying to Hostinger<br>
            • Toggle is instant, no server restart needed
        </div>
    </div>

    <!-- Quick Links -->
    <div>
        <h6 class="mb-3">🔗 Quick Access Tools</h6>
        <div class="quick-links">
            <a href="test-db.php" class="quick-link">
                <h4><i class="bi bi-database-check"></i> Test Database</h4>
                <p>Check connection</p>
            </a>
            <a href="generate-password.php" class="quick-link">
                <h4><i class="bi bi-key-fill"></i> Generate Password</h4>
                <p>Hash generator</p>
            </a>
            <a href="run-migrations.php" class="quick-link">
                <h4><i class="bi bi-arrow-repeat"></i> Run Migrations</h4>
                <p>Create tables</p>
            </a>
            <a href="run-seeders.php" class="quick-link">
                <h4><i class="bi bi-database-fill-add"></i> Run Seeders</h4>
                <p>Populate data</p>
            </a>
        </div>
        <div class="alert alert-info mt-3">
            <i class="bi bi-lightbulb-fill"></i>
            <strong>Setup Guide:</strong> 
            <ol class="mb-0 mt-2" style="padding-left: 20px;">
                <li>Test Database connection</li>
                <li>Run Migrations (create tables)</li>
                <li>Run Seeders (populate sample data)</li>
                <li>Start managing your content!</li>
            </ol>
        </div>
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Admin Only:</strong> These tools are only accessible to logged-in administrators.
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
