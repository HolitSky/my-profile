<?php
/**
 * Force Refresh - Clear All Caches
 * Use this when frontend doesn't update after admin changes
 * Protected by IP whitelist
 */

// Check access permission (IP whitelist)
require_once __DIR__ . '/security-config.php';
checkUtilityAccess();

$results = [];

// 1. Clear OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        $results[] = ['type' => 'success', 'message' => '✅ OPcache cleared'];
    } else {
        $results[] = ['type' => 'error', 'message' => '❌ OPcache clear failed'];
    }
} else {
    $results[] = ['type' => 'info', 'message' => 'ℹ️ OPcache not available'];
}

// 2. Clear APCu cache (if available)
if (function_exists('apcu_clear_cache')) {
    if (apcu_clear_cache()) {
        $results[] = ['type' => 'success', 'message' => '✅ APCu cache cleared'];
    }
}

// 3. Test API endpoint
$apiUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/api/';
$apiResponse = @file_get_contents($apiUrl);
if ($apiResponse !== false) {
    $data = json_decode($apiResponse, true);
    if ($data) {
        $results[] = ['type' => 'success', 'message' => '✅ API endpoint working'];
        $results[] = ['type' => 'info', 'message' => 'About: ' . ($data['about']['name'] ?? 'N/A')];
        $results[] = ['type' => 'info', 'message' => 'Contact Email: ' . ($data['contact']['email'] ?? 'N/A')];
        $results[] = ['type' => 'info', 'message' => 'GitHub: ' . ($data['contact']['github'] ?? 'N/A')];
    } else {
        $results[] = ['type' => 'error', 'message' => '❌ API returned invalid JSON'];
    }
} else {
    $results[] = ['type' => 'error', 'message' => '❌ Cannot reach API endpoint'];
}

// 4. Check if cms-integration.js is loading
$jsFile = __DIR__ . '/assets/js/cms-integration.js';
if (file_exists($jsFile)) {
    $results[] = ['type' => 'success', 'message' => '✅ cms-integration.js exists'];
    $results[] = ['type' => 'info', 'message' => 'Size: ' . number_format(filesize($jsFile)) . ' bytes'];
    $results[] = ['type' => 'info', 'message' => 'Modified: ' . date('Y-m-d H:i:s', filemtime($jsFile))];
} else {
    $results[] = ['type' => 'error', 'message' => '❌ cms-integration.js not found'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Force Refresh</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
        .result {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 5px solid;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .result.success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .result.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        .result.info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
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
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        .instructions {
            background: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #ffc107;
            margin-top: 20px;
        }
        .instructions h3 {
            margin-bottom: 15px;
        }
        .instructions ol {
            margin-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Force Refresh</h1>
            <p>Clear all caches and test frontend integration</p>
        </div>

        <div class="content">
            <h2 style="margin-bottom: 20px;">📊 Results:</h2>
            
            <?php foreach ($results as $result): ?>
                <div class="result <?= $result['type'] ?>">
                    <span><?= $result['message'] ?></span>
                </div>
            <?php endforeach; ?>

            <div class="instructions">
                <h3>📝 Next Steps:</h3>
                <ol>
                    <li><strong>Clear browser cache:</strong> Press <code>Ctrl + Shift + R</code> (Windows) or <code>Cmd + Shift + R</code> (Mac)</li>
                    <li><strong>Open DevTools:</strong> Press <code>F12</code></li>
                    <li><strong>Check Console:</strong> Look for errors in Console tab</li>
                    <li><strong>Check Network:</strong> Verify <code>/api/</code> request returns updated data</li>
                    <li><strong>Test API directly:</strong> Visit <a href="/api/" target="_blank">/api/</a> to see raw JSON</li>
                </ol>
            </div>

            <div class="actions">
                <a href="force-refresh.php" class="btn btn-primary">🔄 Refresh Again</a>
                <a href="clear-cache.php" class="btn btn-success">🧹 Clear OPcache Only</a>
                <a href="/" class="btn btn-primary">🏠 View Frontend</a>
                <a href="/admin/" class="btn btn-primary">⚙️ Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
