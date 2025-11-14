<?php
/**
 * Password Hash Generator
 * Generate bcrypt hash for admin password
 * 
 * Protected by security-config.php
 */

// Check access permission
require_once __DIR__ . '/security-config.php';
checkUtilityAccess();

$password = 'Khalidprofile321.';
$hash = password_hash($password, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
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
            color: #667eea;
        }
        .code {
            background: #2d2d2d;
            color: #50fa7b;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Hash Generator</h1>
        
        <div class="info">
            <strong>Password:</strong> <?= htmlspecialchars($password) ?>
        </div>
        
        <div class="code">
            <?= $hash ?>
        </div>
        
        <div class="info">
            <strong>How to use:</strong><br>
            1. Copy the hash above<br>
            2. Update <code>database/seeders.sql</code><br>
            3. Replace the password hash in INSERT statement<br>
            4. Run migrations
        </div>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong><br>
            DELETE THIS FILE after getting the hash!
        </div>
        
        <h3>SQL Update Query:</h3>
        <div class="code">
UPDATE admin_users 
SET password = '<?= $hash ?>' 
WHERE username = 'admin_khalid';
        </div>
    </div>
</body>
</html>
