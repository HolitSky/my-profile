<?php
/**
 * Database Connection Test
 * Test koneksi database untuk Local & Production
 * 
 * Protected by security-config.php
 */

// Check access permission
require_once __DIR__ . '/security-config.php';
checkUtilityAccess();

// Load configuration
require_once __DIR__ . '/config/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
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
            max-width: 900px;
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
        .warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }
        .env-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        .env-local {
            background: #28a745;
            color: white;
        }
        .env-production {
            background: #dc3545;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
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
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            font-size: 14px;
            text-transform: uppercase;
        }
        .info-card p {
            color: #333;
            font-size: 16px;
            font-weight: 600;
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
        .steps {
            counter-reset: step;
            list-style: none;
        }
        .steps li {
            counter-increment: step;
            margin: 15px 0;
            padding-left: 40px;
            position: relative;
        }
        .steps li::before {
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            background: #667eea;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Database Connection Test</h1>
            <p>Testing database connection for Portfolio CMS</p>
            <span class="env-badge <?= ENVIRONMENT === 'local' ? 'env-local' : 'env-production' ?>">
                <?= strtoupper(ENVIRONMENT) ?> ENVIRONMENT
            </span>
        </div>

        <div class="content">
            <?php
            $errors = [];
            $success = false;
            $tables = [];
            $counts = [];
            
            try {
                // Test connection
                $db = getDB();
                $success = true;
                
                // Get database info
                $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
                $dbVersion = $db->query("SELECT VERSION()")->fetchColumn();
                
                // Get tables
                $stmt = $db->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Get table counts
                foreach ($tables as $table) {
                    $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                    $counts[$table] = $count;
                }
                
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            
            // Display results
            if ($success) {
                echo '<div class="alert success">';
                echo '<strong>✅ Connection Successful!</strong><br>';
                echo 'Database connection is working properly.';
                echo '</div>';
                
                // Environment Info
                echo '<h3>📊 Environment Information</h3>';
                echo '<div class="info-grid">';
                
                echo '<div class="info-card">';
                echo '<h4>Environment</h4>';
                echo '<p>' . strtoupper(ENVIRONMENT) . '</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h4>Database Host</h4>';
                echo '<p>' . DB_HOST . '</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h4>Database Name</h4>';
                echo '<p>' . DB_NAME . '</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h4>Database User</h4>';
                echo '<p>' . DB_USER . '</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h4>MySQL Version</h4>';
                echo '<p>' . $dbVersion . '</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h4>PHP Version</h4>';
                echo '<p>' . PHP_VERSION . '</p>';
                echo '</div>';
                
                echo '</div>';
                
                // Tables Info
                if (count($tables) > 0) {
                    echo '<h3>📋 Database Tables</h3>';
                    echo '<table>';
                    echo '<thead><tr><th>Table Name</th><th>Records</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    $expectedTables = [
                        'admin_users',
                        'about',
                        'skills',
                        'experience',
                        'education',
                        'portfolio',
                        'services',
                        'contact_info',
                        'testimonials'
                    ];
                    
                    foreach ($expectedTables as $expectedTable) {
                        echo '<tr>';
                        echo '<td><strong>' . $expectedTable . '</strong></td>';
                        
                        if (in_array($expectedTable, $tables)) {
                            $count = $counts[$expectedTable];
                            echo '<td>' . number_format($count) . ' rows</td>';
                            echo '<td><span class="badge badge-success">✓ Exists</span></td>';
                        } else {
                            echo '<td>-</td>';
                            echo '<td><span class="badge badge-danger">✗ Missing</span></td>';
                        }
                        
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    
                    // Check if all tables exist
                    $missingTables = array_diff($expectedTables, $tables);
                    if (count($missingTables) > 0) {
                        echo '<div class="alert warning">';
                        echo '<strong>⚠️ Warning: Some tables are missing!</strong><br>';
                        echo 'Missing tables: <strong>' . implode(', ', $missingTables) . '</strong><br><br>';
                        echo '<strong>Solution:</strong> Import database/schema.sql via phpMyAdmin';
                        echo '</div>';
                    } else {
                        echo '<div class="alert success">';
                        echo '<strong>✅ Perfect! All required tables exist.</strong><br>';
                        echo 'Your database is properly configured and ready to use.';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert warning">';
                    echo '<strong>⚠️ No tables found in database!</strong><br>';
                    echo 'Please import the database schema.';
                    echo '</div>';
                }
                
                // Next Steps
                echo '<div class="alert info">';
                echo '<h4>🎯 Next Steps:</h4>';
                echo '<ol class="steps">';
                
                if (count($missingTables) > 0 || count($tables) === 0) {
                    echo '<li>Import <code>database/schema.sql</code> via phpMyAdmin</li>';
                }
                
                echo '<li>Visit admin panel: <a href="/admin/login.php" target="_blank">/admin/login.php</a></li>';
                echo '<li>Login with: <strong>admin</strong> / <strong>admin123</strong></li>';
                echo '<li>Change default password immediately!</li>';
                echo '<li><strong style="color: #dc3545;">DELETE THIS FILE (test-db.php) after testing!</strong></li>';
                echo '</ol>';
                echo '</div>';
                
            } else {
                // Connection Failed
                echo '<div class="alert error">';
                echo '<strong>❌ Connection Failed!</strong><br>';
                echo 'Could not connect to database.<br><br>';
                echo '<strong>Error Details:</strong><br>';
                echo '<div class="code">' . implode('<br>', array_map('htmlspecialchars', $errors)) . '</div>';
                echo '</div>';
                
                echo '<div class="alert info">';
                echo '<h4>🔧 Troubleshooting Steps:</h4>';
                echo '<ol class="steps">';
                echo '<li>Check your credentials in <code>config/config.php</code></li>';
                echo '<li>Verify database exists in phpMyAdmin</li>';
                echo '<li>Ensure database user has proper privileges</li>';
                echo '<li>Check if MySQL service is running</li>';
                echo '<li>Verify PHP PDO extension is enabled</li>';
                echo '</ol>';
                echo '</div>';
                
                // Show current config (safe version)
                echo '<h4>Current Configuration:</h4>';
                echo '<div class="code">';
                echo 'Environment: ' . ENVIRONMENT . '<br>';
                echo 'DB_HOST: ' . DB_HOST . '<br>';
                echo 'DB_NAME: ' . DB_NAME . '<br>';
                echo 'DB_USER: ' . DB_USER . '<br>';
                echo 'DB_PASS: ' . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : '(empty)') . '<br>';
                echo 'SITE_URL: ' . SITE_URL;
                echo '</div>';
            }
            ?>
            
            <div class="alert warning" style="margin-top: 30px;">
                <strong>⚠️ SECURITY WARNING</strong><br>
                This file displays sensitive database information.<br>
                <strong style="color: #dc3545; font-size: 16px;">DELETE THIS FILE IMMEDIATELY AFTER TESTING!</strong>
            </div>
        </div>

        <div class="footer">
            <p><strong>Portfolio CMS</strong> v1.0.0 | Environment: <?= ENVIRONMENT ?></p>
            <p>Made with ❤️ for Khalid Saifullah</p>
        </div>
    </div>
</body>
</html>
