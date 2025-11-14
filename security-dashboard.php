<?php
/**
 * Security Dashboard
 * Simple interface untuk manage security settings
 * Protected by IP whitelist - only accessible from allowed IPs
 */

// Check access permission (IP whitelist)
require_once __DIR__ . '/security-config.php';
checkUtilityAccess();

$status = getSecurityStatus();
$configFile = __DIR__ . '/security-config.php';
$action = $_GET['action'] ?? '';
$message = '';

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
        $message = 'success|Production Mode Enabled! Utility files are now blocked.';
    } else {
        // Enable (Development Mode)
        $content = str_replace(
            "define('ENABLE_UTILITY_FILES', false);",
            "define('ENABLE_UTILITY_FILES', true);",
            $content
        );
        $message = 'success|Development Mode Enabled! Utility files are now accessible.';
    }
    
    file_put_contents($configFile, $content);
    header('Location: security-dashboard.php?msg=' . urlencode($message));
    exit;
}

// Get message from redirect
if (isset($_GET['msg'])) {
    list($type, $text) = explode('|', $_GET['msg']);
    $message = ['type' => $type, 'text' => $text];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
        }
        .card-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .card-body {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
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
            border-left: 4px solid #667eea;
        }
        .info-item h4 {
            color: #667eea;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .info-item p {
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
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
        }
        .file-list {
            list-style: none;
            margin: 15px 0;
        }
        .file-list li {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-enabled {
            color: #28a745;
            font-weight: bold;
        }
        .status-disabled {
            color: #dc3545;
            font-weight: bold;
        }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .quick-link {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
        }
        .quick-link:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .quick-link h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Card -->
        <div class="card">
            <div class="card-header">
                <h1>🔒 Security Dashboard</h1>
                <p>Manage utility files access control</p>
                <span class="status-badge <?= $status['utility_files_enabled'] ? 'badge-dev' : 'badge-prod' ?>">
                    <?= $status['mode'] ?> MODE
                </span>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?>">
            <strong><?= htmlspecialchars($message['text']) ?></strong>
        </div>
        <?php endif; ?>

        <!-- Current Status -->
        <div class="card">
            <div class="card-body">
                <h2>📊 Current Status</h2>
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
        </div>

        <!-- Protected Files -->
        <div class="card">
            <div class="card-body">
                <h2>🛡️ Protected Files Status</h2>
                <ul class="file-list">
                    <li>
                        <span>test-db.php</span>
                        <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                        </span>
                    </li>
                    <li>
                        <span>generate-password.php</span>
                        <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                        </span>
                    </li>
                    <li>
                        <span>database/run-migrations.php</span>
                        <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                        </span>
                    </li>
                    <li>
                        <span>security-dashboard.php</span>
                        <span class="<?= $status['utility_files_enabled'] ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $status['utility_files_enabled'] ? '✓ Accessible' : '✗ Blocked' ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Toggle Mode -->
        <div class="card">
            <div class="card-body">
                <h2>🔄 Toggle Security Mode</h2>
                
                <?php if ($status['utility_files_enabled']): ?>
                <div class="mode-section" style="border-left: 4px solid #28a745;">
                    <h3>🟢 Development Mode (Active)</h3>
                    <p>Utility files are currently accessible from localhost.</p>
                    <p style="margin: 15px 0;"><strong>Switch to Production Mode to block all utility files.</strong></p>
                    <form method="POST" action="?action=toggle" onsubmit="return confirm('Switch to Production Mode? This will block all utility files!')">
                        <button type="submit" class="btn btn-danger">
                            🔴 Enable Production Mode
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="mode-section" style="border-left: 4px solid #dc3545;">
                    <h3>🔴 Production Mode (Active)</h3>
                    <p>All utility files are currently blocked.</p>
                    <p style="margin: 15px 0;"><strong>Switch to Development Mode to enable localhost access.</strong></p>
                    <form method="POST" action="?action=toggle" onsubmit="return confirm('Switch to Development Mode? This will enable utility files access from localhost.')">
                        <button type="submit" class="btn btn-success">
                            🟢 Enable Development Mode
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="alert alert-warning" style="margin-top: 20px;">
                    <strong>⚠️ Important:</strong><br>
                    • Use <strong>Development Mode</strong> when working locally<br>
                    • Use <strong>Production Mode</strong> when deploying to Hostinger<br>
                    • Toggle is instant, no server restart needed
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-body">
                <h2>🔗 Quick Links</h2>
                <div class="quick-links">
                    <a href="test-db.php" class="quick-link">
                        <h4>🧪 Test Database</h4>
                        <p>Check connection</p>
                    </a>
                    <a href="generate-password.php" class="quick-link">
                        <h4>🔐 Generate Password</h4>
                        <p>Hash generator</p>
                    </a>
                    <a href="database/run-migrations.php" class="quick-link">
                        <h4>🚀 Run Migrations</h4>
                        <p>Setup database</p>
                    </a>
                    <a href="admin/login.php" class="quick-link">
                        <h4>👤 Admin Panel</h4>
                        <p>Login to CMS</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
