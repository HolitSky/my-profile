<?php
/**
 * Quick Error Checker
 * Shows PHP errors and checks what's broken
 */

// Enable ALL error display
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🔍 Error Checker</h1>";
echo "<pre>";

// Test 1: Basic PHP
echo "=== Test 1: Basic PHP ===\n";
echo "✅ PHP is working\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Test 2: Load env.php
echo "=== Test 2: Load env.php ===\n";
try {
    require_once __DIR__ . '/includes/env.php';
    echo "✅ env.php loaded\n\n";
} catch (Throwable $e) {
    echo "❌ ERROR loading env.php:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
    die("STOP: Can't load env.php\n");
}

// Test 3: Load config.php
echo "=== Test 3: Load config.php ===\n";
try {
    require_once __DIR__ . '/config/config.php';
    echo "✅ config.php loaded\n\n";
} catch (Throwable $e) {
    echo "❌ ERROR loading config.php:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
    die("STOP: Can't load config.php\n");
}

// Test 4: Check if getDB exists
echo "=== Test 4: Check getDB() ===\n";
if (function_exists('getDB')) {
    echo "✅ getDB() function exists\n\n";
} else {
    echo "❌ getDB() function NOT found\n";
    echo "This is the problem!\n\n";
    die("STOP: getDB() not found\n");
}

// Test 5: Test database connection
echo "=== Test 5: Test Database Connection ===\n";
try {
    $db = getDB();
    echo "✅ Database connection successful\n";
    echo "Connection type: " . get_class($db) . "\n\n";
} catch (Throwable $e) {
    echo "❌ ERROR connecting to database:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
    die("STOP: Can't connect to database\n");
}

// Test 6: Load security-config.php
echo "=== Test 6: Load security-config.php ===\n";
try {
    require_once __DIR__ . '/security-config.php';
    echo "✅ security-config.php loaded\n\n";
} catch (Throwable $e) {
    echo "❌ ERROR loading security-config.php:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
}

// Test 7: Load admin config
echo "=== Test 7: Load admin/config/auth.php ===\n";
try {
    if (file_exists(__DIR__ . '/config/auth.php')) {
        require_once __DIR__ . '/config/auth.php';
        echo "✅ auth.php loaded\n\n";
    } else {
        echo "⚠️ auth.php not found (might be in different location)\n\n";
    }
} catch (Throwable $e) {
    echo "❌ ERROR loading auth.php:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
}

// Test 8: Check includes/db.php directly
echo "=== Test 8: Check includes/db.php ===\n";
if (file_exists(__DIR__ . '/includes/db.php')) {
    echo "✅ includes/db.php exists\n";
    $content = file_get_contents(__DIR__ . '/includes/db.php');
    
    // Check for circular require
    if (strpos($content, "require_once __DIR__ . '/../config/config.php'") !== false) {
        echo "❌ WARNING: includes/db.php still has circular require!\n";
        echo "This will cause infinite loop!\n";
        echo "Remove this line: require_once __DIR__ . '/../config/config.php'\n\n";
    } else {
        echo "✅ No circular require found\n\n";
    }
    
    // Check for buffered query
    if (strpos($content, 'PDO::MYSQL_ATTR_USE_BUFFERED_QUERY') !== false) {
        echo "✅ Buffered query option found\n\n";
    } else {
        echo "⚠️ Buffered query option NOT found\n\n";
    }
} else {
    echo "❌ includes/db.php NOT found!\n\n";
}

// Test 9: Check config.php loads db.php
echo "=== Test 9: Check config.php loads db.php ===\n";
if (file_exists(__DIR__ . '/config/config.php')) {
    $content = file_get_contents(__DIR__ . '/config/config.php');
    
    if (strpos($content, "require_once __DIR__ . '/../includes/db.php'") !== false) {
        echo "✅ config.php loads includes/db.php\n\n";
    } else {
        echo "❌ WARNING: config.php does NOT load includes/db.php!\n";
        echo "Add this at end of config.php:\n";
        echo "require_once __DIR__ . '/../includes/db.php';\n\n";
    }
} else {
    echo "❌ config/config.php NOT found!\n\n";
}

echo "=== All Tests Complete ===\n";
echo "If you see this, basic system is working.\n";
echo "Check above for any ❌ errors.\n\n";

echo "⚠️ DELETE THIS FILE after debugging!\n";

echo "</pre>";
?>
