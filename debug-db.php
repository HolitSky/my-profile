<?php
/**
 * Debug Database Connection
 * Shows detailed error messages
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Database Connection</h1>";
echo "<pre>";

// Step 1: Check if .env exists
echo "=== Step 1: Check .env file ===\n";
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ .env file exists\n";
} else {
    echo "❌ .env file NOT found\n";
}
echo "\n";

// Step 2: Load env.php
echo "=== Step 2: Load env.php ===\n";
try {
    require_once __DIR__ . '/includes/env.php';
    echo "✅ env.php loaded\n";
} catch (Exception $e) {
    echo "❌ Error loading env.php: " . $e->getMessage() . "\n";
    die();
}
echo "\n";

// Step 3: Check env values
echo "=== Step 3: Check .env values ===\n";
echo "APP_ENV: " . (env('APP_ENV') ?: '(not set)') . "\n";
echo "DB_HOST: " . (env('DB_HOST') ?: '(not set)') . "\n";
echo "DB_NAME: " . (env('DB_NAME') ?: '(not set)') . "\n";
echo "DB_USER: " . (env('DB_USER') ?: '(not set)') . "\n";
echo "DB_PASS: " . (env('DB_PASS') ? str_repeat('*', strlen(env('DB_PASS'))) : '(not set)') . "\n";
echo "\n";

// Step 4: Load config
echo "=== Step 4: Load config.php ===\n";
try {
    require_once __DIR__ . '/config/config.php';
    echo "✅ config.php loaded\n";
} catch (Exception $e) {
    echo "❌ Error loading config.php: " . $e->getMessage() . "\n";
    die();
}
echo "\n";

// Step 5: Check constants
echo "=== Step 5: Check defined constants ===\n";
echo "ENVIRONMENT: " . (defined('ENVIRONMENT') ? ENVIRONMENT : '(not defined)') . "\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '(not defined)') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '(not defined)') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : '(not defined)') . "\n";
echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : '(empty)') : '(not defined)') . "\n";
echo "\n";

// Step 6: Test getDB function
echo "=== Step 6: Test getDB() function ===\n";
if (function_exists('getDB')) {
    echo "✅ getDB() function exists\n";
    try {
        $db = getDB();
        echo "✅ getDB() executed successfully\n";
        echo "Connection type: " . get_class($db) . "\n";
    } catch (Exception $e) {
        echo "❌ Error calling getDB(): " . $e->getMessage() . "\n";
        echo "Error code: " . $e->getCode() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        die();
    }
} else {
    echo "❌ getDB() function NOT found\n";
    die();
}
echo "\n";

// Step 7: Test database query
echo "=== Step 7: Test database query ===\n";
try {
    $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
    echo "✅ Query successful\n";
    echo "Current database: " . $dbName . "\n";
    
    $version = $db->query("SELECT VERSION()")->fetchColumn();
    echo "MySQL version: " . $version . "\n";
} catch (Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "\n";
    die();
}
echo "\n";

// Step 8: List tables
echo "=== Step 8: List tables ===\n";
try {
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✅ Found " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  - $table ($count rows)\n";
        }
    } else {
        echo "⚠️ No tables found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error listing tables: " . $e->getMessage() . "\n";
}
echo "\n";

// Step 9: Check security config
echo "=== Step 9: Check security config ===\n";
try {
    require_once __DIR__ . '/security-config.php';
    echo "✅ security-config.php loaded\n";
    
    echo "ENABLE_UTILITY_FILES: " . (defined('ENABLE_UTILITY_FILES') ? (ENABLE_UTILITY_FILES ? 'true' : 'false') : '(not defined)') . "\n";
    
    if (defined('ALLOWED_IPS')) {
        echo "ALLOWED_IPS: " . implode(', ', ALLOWED_IPS) . "\n";
    } else {
        echo "ALLOWED_IPS: (not defined)\n";
    }
    
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    echo "Your IP: " . $clientIP . "\n";
    
    if (function_exists('checkIsLocalhost')) {
        echo "Is localhost: " . (checkIsLocalhost() ? 'Yes' : 'No') . "\n";
    }
    
    if (function_exists('checkIsAllowedIP')) {
        echo "Is allowed IP: " . (checkIsAllowedIP() ? 'Yes' : 'No') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading security-config.php: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== All checks complete! ===\n";
echo "If you see this message, everything is working.\n";
echo "\n";
echo "⚠️ DELETE THIS FILE after debugging!\n";

echo "</pre>";
?>
