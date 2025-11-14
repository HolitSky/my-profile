<?php
/**
 * Clear OPcache
 * Use this after deploy to ensure new code is loaded
 * Protected by IP whitelist
 */

// Check access permission (IP whitelist)
require_once __DIR__ . '/security-config.php';
checkUtilityAccess();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clear Cache</title>
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
            max-width: 600px;
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
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }
        .cache-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .cache-info h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .cache-stat {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .cache-stat:last-child {
            border-bottom: none;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧹 Clear Cache</h1>
            <p>Clear OPcache after deployment</p>
        </div>

        <div class="content">
            <?php
            $action = $_GET['action'] ?? '';
            
            if ($action === 'clear') {
                // Clear OPcache
                if (function_exists('opcache_reset')) {
                    if (opcache_reset()) {
                        echo '<div class="alert success">';
                        echo '<h3>✅ Success!</h3>';
                        echo '<p>OPcache has been cleared successfully.</p>';
                        echo '<p><small>New code changes will now be visible.</small></p>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert error">';
                        echo '<h3>❌ Error!</h3>';
                        echo '<p>Failed to clear OPcache.</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert error">';
                    echo '<h3>❌ Not Available</h3>';
                    echo '<p>OPcache is not enabled on this server.</p>';
                    echo '</div>';
                }
            }
            
            // Show OPcache status
            if (function_exists('opcache_get_status')) {
                $status = opcache_get_status();
                
                if ($status !== false) {
                    echo '<div class="cache-info">';
                    echo '<h3>📊 OPcache Status</h3>';
                    
                    echo '<div class="cache-stat">';
                    echo '<strong>Status:</strong>';
                    echo '<span>' . ($status['opcache_enabled'] ? '✅ Enabled' : '❌ Disabled') . '</span>';
                    echo '</div>';
                    
                    if (isset($status['memory_usage'])) {
                        $used = round($status['memory_usage']['used_memory'] / 1024 / 1024, 2);
                        $free = round($status['memory_usage']['free_memory'] / 1024 / 1024, 2);
                        $total = $used + $free;
                        
                        echo '<div class="cache-stat">';
                        echo '<strong>Memory Used:</strong>';
                        echo '<span>' . $used . ' MB / ' . $total . ' MB</span>';
                        echo '</div>';
                    }
                    
                    if (isset($status['opcache_statistics'])) {
                        $stats = $status['opcache_statistics'];
                        
                        echo '<div class="cache-stat">';
                        echo '<strong>Cached Scripts:</strong>';
                        echo '<span>' . $stats['num_cached_scripts'] . '</span>';
                        echo '</div>';
                        
                        echo '<div class="cache-stat">';
                        echo '<strong>Hits:</strong>';
                        echo '<span>' . number_format($stats['hits']) . '</span>';
                        echo '</div>';
                        
                        echo '<div class="cache-stat">';
                        echo '<strong>Misses:</strong>';
                        echo '<span>' . number_format($stats['misses']) . '</span>';
                        echo '</div>';
                        
                        if ($stats['hits'] + $stats['misses'] > 0) {
                            $hitRate = round(($stats['hits'] / ($stats['hits'] + $stats['misses'])) * 100, 2);
                            echo '<div class="cache-stat">';
                            echo '<strong>Hit Rate:</strong>';
                            echo '<span>' . $hitRate . '%</span>';
                            echo '</div>';
                        }
                    }
                    
                    echo '</div>';
                } else {
                    echo '<div class="alert info">';
                    echo '<p>OPcache is enabled but status is not available.</p>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert info">';
                echo '<p>OPcache functions are not available.</p>';
                echo '</div>';
            }
            ?>

            <div style="text-align: center; margin-top: 30px;">
                <?php if ($action !== 'clear'): ?>
                    <a href="?action=clear" class="btn btn-primary" onclick="return confirm('Clear OPcache now?')">
                        🧹 Clear Cache Now
                    </a>
                <?php else: ?>
                    <a href="clear-cache.php" class="btn btn-success">
                        🔄 Refresh Status
                    </a>
                <?php endif; ?>
                <a href="security-dashboard.php" class="btn btn-primary">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
