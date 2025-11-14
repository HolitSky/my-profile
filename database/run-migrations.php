<?php
/**
 * Migration & Seeder Runner
 * 
 * Run this file to setup database with migrations and seeders
 * 
 * ⚠️ WARNING: This will DROP all existing tables and data!
 * Protected by security-config.php
 */

// Prevent timeout
set_time_limit(300); // 5 minutes
ini_set('max_execution_time', 300);

// Disable output buffering for real-time progress
if (ob_get_level()) ob_end_clean();
ob_implicit_flush(true);

// Check access permission
require_once __DIR__ . '/../security-config.php';
checkUtilityAccess();

require_once __DIR__ . '/../config/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration & Seeder</title>
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
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .log {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 400px;
            overflow-y: auto;
            margin: 20px 0;
        }
        .log-success { color: #50fa7b; }
        .log-error { color: #ff5555; }
        .log-warning { color: #f1fa8c; }
        .log-info { color: #8be9fd; }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Database Migration & Seeder</h1>
            <p>Setup your Portfolio CMS database</p>
        </div>

        <div class="content">
            <?php
            $action = $_GET['action'] ?? '';
            
            if ($action === '') {
                // Show options
                ?>
                <div class="alert warning">
                    <strong>⚠️ WARNING!</strong><br>
                    Running migrations will <strong>DROP ALL EXISTING TABLES</strong> and recreate them.<br>
                    All data will be lost!<br><br>
                    Make sure you have a backup before proceeding.
                </div>

                <div class="alert info">
                    <h4>What will happen:</h4>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Drop all existing tables (if any)</li>
                        <li>Create fresh database schema (9 tables)</li>
                        <li>Seed initial data from your existing HTML</li>
                        <li>Create admin user: <strong>admin_khalid</strong> / <strong>Khalidprofile321.</strong></li>
                    </ol>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="?action=run" class="btn btn-primary" onclick="return confirm('Are you sure? This will delete all existing data!')">
                        🚀 Run Migration & Seeder
                    </a>
                    <a href="../test-db.php" class="btn btn-success">
                        🧪 Test Connection First
                    </a>
                </div>
                <?php
            } elseif ($action === 'run') {
                // Run migrations and seeders
                echo '<div class="log">';
                
                try {
                    $db = getDB();
                    $errors = [];
                    $success = [];
                    
                    // Read migration file
                    echo '<span class="log-info">📖 Reading migration file...</span><br>';
                    $migrationSQL = file_get_contents(__DIR__ . '/migrations.sql');
                    
                    if (!$migrationSQL) {
                        throw new Exception('Could not read migrations.sql');
                    }
                    
                    // Execute migrations
                    echo '<span class="log-info">🔨 Running migrations...</span><br>';
                    
                    // Split SQL into individual statements
                    $statements = array_filter(
                        array_map('trim', explode(';', $migrationSQL)),
                        function($stmt) { return !empty($stmt); }
                    );
                    
                    $migrationCount = 0;
                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            try {
                                $db->exec($statement);
                                $migrationCount++;
                                // Flush output to show progress
                                echo '<span class="log-success">  ✓ Statement ' . $migrationCount . ' executed</span><br>';
                                flush();
                                ob_flush();
                            } catch (PDOException $e) {
                                // Skip if table doesn't exist (for DROP statements)
                                if (strpos($e->getMessage(), "doesn't exist") === false) {
                                    throw $e;
                                }
                            }
                        }
                    }
                    
                    echo '<span class="log-success">✅ Migrations executed successfully! (' . $migrationCount . ' statements)</span><br><br>';
                    
                    // Read seeder file
                    echo '<span class="log-info">📖 Reading seeder file...</span><br>';
                    $seederSQL = file_get_contents(__DIR__ . '/seeders.sql');
                    
                    if (!$seederSQL) {
                        throw new Exception('Could not read seeders.sql');
                    }
                    
                    // Generate password hash for admin
                    echo '<span class="log-info">🔐 Generating password hash...</span><br>';
                    $password = 'Khalidprofile321.';
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    echo '<span class="log-success">✅ Password hash generated!</span><br>';
                    
                    // Replace placeholder hash with real hash
                    $seederSQL = str_replace(
                        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                        $passwordHash,
                        $seederSQL
                    );
                    
                    // Execute seeders
                    echo '<span class="log-info">🌱 Running seeders...</span><br>';
                    
                    // Split SQL into individual statements
                    $seederStatements = array_filter(
                        array_map('trim', explode(';', $seederSQL)),
                        function($stmt) { return !empty($stmt); }
                    );
                    
                    $seederCount = 0;
                    foreach ($seederStatements as $statement) {
                        if (!empty($statement)) {
                            $db->exec($statement);
                            $seederCount++;
                            // Flush output to show progress
                            echo '<span class="log-success">  ✓ Statement ' . $seederCount . ' executed</span><br>';
                            flush();
                            ob_flush();
                        }
                    }
                    
                    echo '<span class="log-success">✅ Seeders executed successfully! (' . $seederCount . ' statements)</span><br><br>';
                    
                    // Verify tables
                    echo '<span class="log-info">🔍 Verifying tables...</span><br>';
                    
                    // Fetch all tables first (close cursor)
                    $stmt = $db->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $stmt->closeCursor(); // Close cursor to allow new queries
                    
                    foreach ($tables as $table) {
                        $stmt = $db->query("SELECT COUNT(*) FROM `$table`");
                        $count = $stmt->fetchColumn();
                        $stmt->closeCursor(); // Close cursor after each query
                        echo '<span class="log-success">  ✓ ' . $table . ' (' . $count . ' records)</span><br>';
                        flush();
                        ob_flush();
                    }
                    
                    echo '<br><span class="log-success">🎉 DATABASE SETUP COMPLETE!</span><br>';
                    
                    echo '</div>';
                    
                    // Show success message
                    echo '<div class="alert success">';
                    echo '<h3>✅ Success!</h3>';
                    echo '<p>Database has been setup successfully with all tables and initial data.</p>';
                    echo '</div>';
                    
                    // Show admin credentials
                    echo '<div class="alert info">';
                    echo '<h4>🔐 Admin Credentials:</h4>';
                    echo '<table>';
                    echo '<tr><th>Username</th><td><strong>admin_khalid</strong></td></tr>';
                    echo '<tr><th>Password</th><td><strong>Khalidprofile321.</strong></td></tr>';
                    echo '<tr><th>Email</th><td>holitsky98@gmail.com</td></tr>';
                    echo '</table>';
                    echo '</div>';
                    
                    // Show next steps
                    echo '<div class="alert warning">';
                    echo '<h4>⚠️ Important Next Steps:</h4>';
                    echo '<ol style="margin-left: 20px; margin-top: 10px;">';
                    echo '<li><strong>DELETE THIS FILE</strong> (run-migrations.php) for security!</li>';
                    echo '<li>Login to admin panel and change your password</li>';
                    echo '<li>Update your profile information</li>';
                    echo '<li>Add your portfolio projects</li>';
                    echo '</ol>';
                    echo '</div>';
                    
                    // Show links
                    echo '<div style="text-align: center; margin-top: 30px;">';
                    echo '<a href="../admin/login.php" class="btn btn-success">🔐 Login to Admin Panel</a>';
                    echo '<a href="../test-db.php" class="btn btn-primary">🧪 Test Database</a>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<span class="log-error">❌ ERROR: ' . $e->getMessage() . '</span><br>';
                    echo '</div>';
                    
                    echo '<div class="alert error">';
                    echo '<h3>❌ Migration Failed!</h3>';
                    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<p>Please check your database connection and try again.</p>';
                    echo '</div>';
                    
                    echo '<div style="text-align: center;">';
                    echo '<a href="?action=" class="btn btn-primary">← Back</a>';
                    echo '<a href="../test-db.php" class="btn btn-success">🧪 Test Connection</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    
    <script>
        // Auto-scroll log to bottom
        function scrollLogToBottom() {
            const log = document.querySelector('.log');
            if (log) {
                log.scrollTop = log.scrollHeight;
            }
        }
        
        // Scroll every 100ms during migration
        const scrollInterval = setInterval(scrollLogToBottom, 100);
        
        // Stop scrolling after 5 minutes
        setTimeout(() => clearInterval(scrollInterval), 300000);
        
        // Disable button after click
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href && this.href.includes('action=run')) {
                    setTimeout(() => {
                        this.style.opacity = '0.5';
                        this.style.pointerEvents = 'none';
                        this.innerHTML = '⏳ Running... Please wait';
                    }, 100);
                }
            });
        });
    </script>
</body>
</html>
