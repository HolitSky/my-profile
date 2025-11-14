<?php
/**
 * Database Connection Test
 * 
 * Use this file to test your database connection
 * DELETE THIS FILE after successful setup!
 * Protected by IP whitelist
 */

// Check access permission (IP whitelist)
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
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Connection Test</h1>
        
        <?php
        $errors = [];
        $success = false;
        $tables = [];
        
        try {
            // Test connection
            $db = getDB();
            $success = true;
            
            // Get database info
            $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
            
            // Get tables
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Get table counts
            $counts = [];
            foreach ($tables as $table) {
                $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                $counts[$table] = $count;
            }
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        // Display results
        if ($success) {
            echo '<div class="success">';
            echo '<strong>✅ Connection Successful!</strong><br>';
            echo 'Database connection is working properly.';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>📊 Database Information</strong><br>';
            echo 'Host: ' . DB_HOST . '<br>';
            echo 'Database: ' . DB_NAME . '<br>';
            echo 'User: ' . DB_USER . '<br>';
            echo 'Connected to: ' . $dbName;
            echo '</div>';
            
            if (count($tables) > 0) {
                echo '<h2>Database Tables</h2>';
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
                    if (in_array($expectedTable, $tables)) {
                        $count = $counts[$expectedTable];
                        echo '<tr>';
                        echo '<td>' . $expectedTable . '</td>';
                        echo '<td>' . $count . '</td>';
                        echo '<td><span class="badge badge-success">✓ Exists</span></td>';
                        echo '</tr>';
                    } else {
                        echo '<tr>';
                        echo '<td>' . $expectedTable . '</td>';
                        echo '<td>-</td>';
                        echo '<td><span class="badge badge-danger">✗ Missing</span></td>';
                        echo '</tr>';
                    }
                }
                
                echo '</tbody></table>';
                
                // Check if all tables exist
                $missingTables = array_diff($expectedTables, $tables);
                if (count($missingTables) > 0) {
                    echo '<div class="warning">';
                    echo '<strong>⚠️ Warning:</strong> Some tables are missing!<br>';
                    echo 'Missing tables: ' . implode(', ', $missingTables) . '<br>';
                    echo 'Please import database/schema.sql via phpMyAdmin.';
                    echo '</div>';
                } else {
                    echo '<div class="success">';
                    echo '<strong>✅ All tables exist!</strong><br>';
                    echo 'Your database is properly configured.';
                    echo '</div>';
                }
            } else {
                echo '<div class="warning">';
                echo '<strong>⚠️ No tables found!</strong><br>';
                echo 'Please import database/schema.sql via phpMyAdmin.';
                echo '</div>';
            }
            
            echo '<div class="info">';
            echo '<strong>🎯 Next Steps:</strong><br>';
            echo '1. If all tables exist, you can proceed to login<br>';
            echo '2. Visit: <a href="/admin/login.php">/admin/login.php</a><br>';
            echo '3. Default credentials: admin / admin123<br>';
            echo '4. <strong>DELETE THIS FILE (test-connection.php) after testing!</strong>';
            echo '</div>';
            
        } else {
            echo '<div class="error">';
            echo '<strong>❌ Connection Failed!</strong><br>';
            echo 'Could not connect to database.<br><br>';
            echo '<strong>Error:</strong> ' . implode('<br>', $errors);
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>🔧 Troubleshooting:</strong><br>';
            echo '1. Check your credentials in config/database.php<br>';
            echo '2. Verify database exists in phpMyAdmin<br>';
            echo '3. Ensure database user has proper privileges<br>';
            echo '4. Check if MySQL service is running';
            echo '</div>';
        }
        ?>
        
        <div class="info">
            <strong>⚠️ Security Notice:</strong><br>
            This file shows sensitive database information. 
            <strong>DELETE this file immediately after testing!</strong>
        </div>
    </div>
</body>
</html>
