<?php
/**
 * IP Whitelist Checker
 * Debug tool to check if your IP is whitelisted
 * This file is NOT protected - accessible by anyone
 */

// Load environment variables
require_once __DIR__ . '/includes/env.php';

// Get client IP
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$forwardedIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
$realIP = $_SERVER['HTTP_X_REAL_IP'] ?? null;

// Get allowed IPs from .env
$allowedIPsFromEnv = env_array('ALLOWED_IPS', ['127.0.0.1', '::1']);
$yourIP = env('YOUR_IP');
if ($yourIP && !in_array($yourIP, $allowedIPsFromEnv)) {
    $allowedIPsFromEnv[] = $yourIP;
}

// Check if IP is allowed
$isLocalhost = in_array($clientIP, ['127.0.0.1', '::1', 'localhost']);
$isAllowed = in_array($clientIP, $allowedIPsFromEnv);
$utilityEnabled = env_bool('ENABLE_UTILITY_FILES', true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Whitelist Checker</title>
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
            max-width: 800px;
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
        .status-box {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid;
            text-align: center;
        }
        .status-allowed {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .status-denied {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .info-card h4 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .info-card p {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            word-break: break-all;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin: 2px;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 15px 0;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        ol {
            margin-left: 20px;
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 IP Whitelist Checker</h1>
            <p>Check if your IP is allowed to access utility files</p>
        </div>

        <div class="content">
            <!-- Access Status -->
            <div class="status-box <?= ($isLocalhost || $isAllowed) && $utilityEnabled ? 'status-allowed' : 'status-denied' ?>">
                <div class="status-icon">
                    <?= ($isLocalhost || $isAllowed) && $utilityEnabled ? '✅' : '❌' ?>
                </div>
                <h2>
                    <?php if (($isLocalhost || $isAllowed) && $utilityEnabled): ?>
                        Access Granted
                    <?php else: ?>
                        Access Denied
                    <?php endif; ?>
                </h2>
                <p>
                    <?php if (!$utilityEnabled): ?>
                        Utility files are disabled
                    <?php elseif ($isLocalhost): ?>
                        You are accessing from localhost
                    <?php elseif ($isAllowed): ?>
                        Your IP is whitelisted
                    <?php else: ?>
                        Your IP is not in the whitelist
                    <?php endif; ?>
                </p>
            </div>

            <!-- IP Information -->
            <h3>📊 Your IP Information</h3>
            <div class="info-grid">
                <div class="info-card">
                    <h4>Your IP Address</h4>
                    <p><?= htmlspecialchars($clientIP) ?></p>
                </div>
                
                <?php if ($forwardedIP): ?>
                <div class="info-card">
                    <h4>Forwarded IP</h4>
                    <p><?= htmlspecialchars($forwardedIP) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($realIP): ?>
                <div class="info-card">
                    <h4>Real IP</h4>
                    <p><?= htmlspecialchars($realIP) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="info-card">
                    <h4>Localhost?</h4>
                    <p>
                        <?php if ($isLocalhost): ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-danger">No</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="info-card">
                    <h4>Whitelisted?</h4>
                    <p>
                        <?php if ($isAllowed): ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-danger">No</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="info-card">
                    <h4>Utility Files</h4>
                    <p>
                        <?php if ($utilityEnabled): ?>
                            <span class="badge badge-success">Enabled</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Disabled</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- .env Configuration -->
            <h3>⚙️ .env Configuration</h3>
            <div class="code">
YOUR_IP=<?= htmlspecialchars($yourIP ?: '(not set)') ?><br>
ALLOWED_IPS=<?= htmlspecialchars(implode(',', $allowedIPsFromEnv)) ?><br>
ENABLE_UTILITY_FILES=<?= $utilityEnabled ? 'true' : 'false' ?>
            </div>

            <!-- Whitelisted IPs -->
            <h3>✅ Whitelisted IPs</h3>
            <div style="margin: 15px 0;">
                <?php foreach ($allowedIPsFromEnv as $ip): ?>
                    <span class="badge <?= $ip === $clientIP ? 'badge-success' : 'badge-warning' ?>">
                        <?= htmlspecialchars($ip) ?>
                        <?= $ip === $clientIP ? ' (YOU)' : '' ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <!-- Instructions -->
            <?php if (!$isAllowed && !$isLocalhost): ?>
            <div class="alert alert-warning">
                <h4>🔧 How to Add Your IP to Whitelist:</h4>
                <ol>
                    <li>Copy your IP address: <strong><?= htmlspecialchars($clientIP) ?></strong></li>
                    <li>Open <code>.env</code> file on server</li>
                    <li>Update the configuration:
                        <div class="code" style="margin: 10px 0;">
YOUR_IP=<?= htmlspecialchars($clientIP) ?><br>
ALLOWED_IPS=127.0.0.1,::1,<?= htmlspecialchars($clientIP) ?>
                        </div>
                    </li>
                    <li>Save the file</li>
                    <li>Clear OPcache: <a href="/clear-cache.php" target="_blank">clear-cache.php</a></li>
                    <li>Refresh this page</li>
                </ol>
            </div>
            <?php endif; ?>

            <?php if (!$utilityEnabled): ?>
            <div class="alert alert-warning">
                <h4>⚠️ Utility Files Are Disabled</h4>
                <p>To enable utility files, update your <code>.env</code>:</p>
                <div class="code">
ENABLE_UTILITY_FILES=true
                </div>
                <p style="margin-top: 10px;">
                    <strong>Note:</strong> Only enable in development environment!
                </p>
            </div>
            <?php endif; ?>

            <?php if (($isLocalhost || $isAllowed) && $utilityEnabled): ?>
            <div class="alert alert-info">
                <h4>✅ You Can Access These Files:</h4>
                <p>
                    <a href="/test-db.php" class="btn">test-db.php</a>
                    <a href="/clear-cache.php" class="btn">clear-cache.php</a>
                    <a href="/force-refresh.php" class="btn">force-refresh.php</a>
                    <a href="/security-dashboard.php" class="btn">security-dashboard.php</a>
                </p>
            </div>
            <?php endif; ?>

            <!-- Delete Warning -->
            <div class="alert alert-warning" style="margin-top: 30px;">
                <strong>⚠️ SECURITY WARNING</strong><br>
                This file is accessible by anyone (not protected).<br>
                <strong style="color: #dc3545; font-size: 16px;">DELETE THIS FILE AFTER DEBUGGING!</strong>
            </div>
        </div>
    </div>
</body>
</html>
