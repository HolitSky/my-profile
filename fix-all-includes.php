<?php
/**
 * Fix All Files - Replace includes/db.php with config/config.php
 */

$directories = [
    __DIR__ . '/admin',
    __DIR__,
];

$fixed = [];
$skipped = [];
$errors = [];

foreach ($directories as $dir) {
    $files = glob($dir . '/*.php');
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Skip this script and some special files
        if (in_array($filename, ['fix-all-includes.php', 'config.php', 'db.php', 'env.php'])) {
            continue;
        }
        
        $content = file_get_contents($file);
        $original = $content;
        
        // Pattern 1: require_once __DIR__ . '/../includes/db.php';
        $content = str_replace(
            "require_once __DIR__ . '/../includes/db.php';",
            "require_once __DIR__ . '/../config/config.php';",
            $content
        );
        
        // Pattern 2: require_once __DIR__ . '/includes/db.php';
        $content = str_replace(
            "require_once __DIR__ . '/includes/db.php';",
            "require_once __DIR__ . '/config/config.php';",
            $content
        );
        
        if ($content !== $original) {
            if (file_put_contents($file, $content)) {
                $fixed[] = str_replace(__DIR__ . '/', '', $file);
            } else {
                $errors[] = str_replace(__DIR__ . '/', '', $file);
            }
        } else {
            $skipped[] = str_replace(__DIR__ . '/', '', $file);
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix All Includes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 { color: #28a745; }
        .section {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .fixed {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .skipped {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .errors {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 5px 0;
        }
        .count {
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>✅ All Files Fixed!</h1>
    
    <?php if (count($fixed) > 0): ?>
    <div class="section fixed">
        <h3>✅ Fixed Files (<span class="count"><?= count($fixed) ?></span>):</h3>
        <ul>
            <?php foreach ($fixed as $file): ?>
            <li>✅ <?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (count($errors) > 0): ?>
    <div class="section errors">
        <h3>❌ Errors (<span class="count"><?= count($errors) ?></span>):</h3>
        <ul>
            <?php foreach ($errors as $file): ?>
            <li>❌ <?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (count($skipped) > 0): ?>
    <div class="section skipped">
        <h3>⏭️ Skipped Files (<span class="count"><?= count($skipped) ?></span>):</h3>
        <p>These files didn't need changes (already correct or no includes/db.php found)</p>
        <ul>
            <?php foreach (array_slice($skipped, 0, 10) as $file): ?>
            <li>⏭️ <?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
            <?php if (count($skipped) > 10): ?>
            <li><em>... and <?= count($skipped) - 10 ?> more</em></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="section fixed">
        <h3>📋 Summary:</h3>
        <ul>
            <li><strong>Fixed:</strong> <?= count($fixed) ?> files</li>
            <li><strong>Errors:</strong> <?= count($errors) ?> files</li>
            <li><strong>Skipped:</strong> <?= count($skipped) ?> files</li>
        </ul>
    </div>
    
    <div class="section fixed">
        <h3>✅ Next Steps:</h3>
        <ol>
            <li><strong>Test API:</strong> <a href="/api/index.php" target="_blank">/api/index.php</a></li>
            <li><strong>Test Admin:</strong> <a href="/admin/dashboard.php" target="_blank">/admin/dashboard.php</a></li>
            <li><strong>Test Frontend:</strong> <a href="/" target="_blank">Homepage</a></li>
            <li><strong>Clear Cache:</strong> <a href="/clear-cache.php" target="_blank">/clear-cache.php</a></li>
            <li><strong>DELETE THIS FILE!</strong> (fix-all-includes.php)</li>
        </ol>
    </div>
    
    <p><strong>⚠️ IMPORTANT: Delete this file after use!</strong></p>
    <p><code>rm fix-all-includes.php</code></p>
</body>
</html>
