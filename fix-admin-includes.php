<?php
/**
 * Fix Admin Files - Replace includes/db.php with config/config.php
 */

$adminDir = __DIR__ . '/admin';
$files = glob($adminDir . '/*.php');

$fixed = [];
$skipped = [];

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip this script
    if ($filename === 'fix-admin-includes.php') {
        continue;
    }
    
    $content = file_get_contents($file);
    $original = $content;
    
    // Replace includes/db.php with config/config.php
    $content = preg_replace(
        "/require_once\s+__DIR__\s*\.\s*['\"]\/\.\.\/includes\/db\.php['\"]\s*;/",
        "require_once __DIR__ . '/../config/config.php';",
        $content
    );
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        $fixed[] = $filename;
    } else {
        $skipped[] = $filename;
    }
}

echo "<h1>✅ Admin Files Fixed</h1>";
echo "<h3>Fixed Files (" . count($fixed) . "):</h3>";
echo "<ul>";
foreach ($fixed as $file) {
    echo "<li>✅ $file</li>";
}
echo "</ul>";

echo "<h3>Skipped Files (" . count($skipped) . "):</h3>";
echo "<ul>";
foreach ($skipped as $file) {
    echo "<li>⏭️ $file (no changes needed)</li>";
}
echo "</ul>";

echo "<p><strong>Done! All admin files now load config.php instead of db.php directly.</strong></p>";
echo "<p><strong>⚠️ DELETE THIS FILE after use!</strong></p>";
?>
