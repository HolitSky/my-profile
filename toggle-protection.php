<?php
/**
 * DEPRECATED: Use security-dashboard.php instead!
 * 
 * This file is outdated. Please use:
 * http://localhost/my-profile/security-dashboard.php
 */

// Redirect to new dashboard
header('Location: security-dashboard.php');
exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Protection Toggle</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            max-width: 700px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .mode-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid;
        }
        .mode-dev {
            border-color: #28a745;
        }
        .mode-prod {
            border-color: #dc3545;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin: 10px 5px;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background: #667eea;
            color: white;
        }
        .status-enabled {
            color: #28a745;
            font-weight: bold;
        }
        .status-disabled {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Security Protection Toggle</h1>
            <p>Manage utility files access control</p>
        </div>

        <div class="content">
            <?php
            if ($action === 'enable-dev') {
                // Enable development mode (allow localhost access)
                $content = file_get_contents($htaccessFile);
                
                // Uncomment localhost rules
                $content = str_replace(
                    ['# <Files "test-db.php">', '# <Files "generate-password.php">', '# <Files "run-migrations.php">'],
                    ['<Files "test-db.php">', '<Files "generate-password.php">', '<Files "run-migrations.php">'],
                    $content
                );
                
                file_put_contents($htaccessFile, $content);
                
                echo '<div class="alert success">';
                echo '<strong>✅ Development Mode Enabled!</strong><br>';
                echo 'Utility files are now accessible from localhost.';
                echo '</div>';
                
            } elseif ($action === 'enable-prod') {
                // Enable production mode (block all access)
                $content = file_get_contents($htaccessFile);
                
                // Comment out localhost rules and uncomment production blocks
                $content = preg_replace(
                    '/# (<Files "(test-db|generate-password|run-migrations)\.php">)/',
                    '$1',
                    $content
                );
                
                file_put_contents($htaccessFile, $content);
                
                echo '<div class="alert success">';
                echo '<strong>✅ Production Mode Enabled!</strong><br>';
                echo 'All utility files are now completely blocked.';
                echo '</div>';
            }
            
            // Check current mode
            $htaccessContent = file_get_contents($htaccessFile);
            $isDevMode = strpos($htaccessContent, 'Allow from 127.0.0.1') !== false;
            $isProdBlocked = strpos($htaccessContent, '# <Files "test-db.php">') === false;
            
            ?>
            
            <div class="alert info">
                <h3>📊 Current Status</h3>
                <table>
                    <tr>
                        <th>File</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>test-db.php</td>
                        <td class="<?= $isDevMode ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $isDevMode ? '✓ Localhost Only' : '✗ Blocked' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>generate-password.php</td>
                        <td class="<?= $isDevMode ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $isDevMode ? '✓ Localhost Only' : '✗ Blocked' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>database/run-migrations.php</td>
                        <td class="<?= $isDevMode ? 'status-enabled' : 'status-disabled' ?>">
                            <?= $isDevMode ? '✓ Localhost Only' : '✗ Blocked' ?>
                        </td>
                    </tr>
                </table>
            </div>

            <h3>🎯 Select Mode:</h3>

            <div class="mode-card mode-dev">
                <h4>🟢 Development Mode</h4>
                <p><strong>Status:</strong> <?= $isDevMode ? 'Active' : 'Inactive' ?></p>
                <p>Utility files accessible from localhost only.</p>
                <ul style="margin: 10px 0 10px 20px;">
                    <li>✓ test-db.php accessible</li>
                    <li>✓ generate-password.php accessible</li>
                    <li>✓ run-migrations.php accessible</li>
                    <li>✓ Only from 127.0.0.1 / localhost</li>
                </ul>
                <?php if (!$isDevMode): ?>
                <a href="?action=enable-dev" class="btn btn-success" onclick="return confirm('Enable development mode?')">
                    Enable Development Mode
                </a>
                <?php endif; ?>
            </div>

            <div class="mode-card mode-prod">
                <h4>🔴 Production Mode</h4>
                <p><strong>Status:</strong> <?= !$isDevMode ? 'Active' : 'Inactive' ?></p>
                <p>All utility files completely blocked.</p>
                <ul style="margin: 10px 0 10px 20px;">
                    <li>✗ test-db.php blocked</li>
                    <li>✗ generate-password.php blocked</li>
                    <li>✗ run-migrations.php blocked</li>
                    <li>✓ Maximum security</li>
                </ul>
                <?php if ($isDevMode): ?>
                <a href="?action=enable-prod" class="btn btn-danger" onclick="return confirm('Enable production mode? This will block all utility files!')">
                    Enable Production Mode
                </a>
                <?php endif; ?>
            </div>

            <div class="alert warning">
                <h4>⚠️ Important Notes:</h4>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li><strong>Development Mode:</strong> Use when developing locally</li>
                    <li><strong>Production Mode:</strong> Use when deploying to Hostinger</li>
                    <li>This file (toggle-protection.php) is also protected</li>
                    <li>Config and database folders are always protected</li>
                </ul>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="test-db.php" class="btn btn-primary">🧪 Test Database</a>
                <a href="admin/login.php" class="btn btn-success">🔐 Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
