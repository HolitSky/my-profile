<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle = 'Database Seeders';
$activePage = 'security';
$message = '';
$messageType = '';
$logs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_seeders'])) {
    try {
        $db = getDB();
        $seedersFile = __DIR__ . '/../database/seeders.sql';
        
        if (!file_exists($seedersFile)) {
            throw new Exception('Seeders file not found: ' . $seedersFile);
        }
        
        $sql = file_get_contents($seedersFile);
        $logs[] = ['type' => 'info', 'message' => 'Reading seeders file...'];
        
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $successCount = 0;
        $errorCount = 0;
        $insertCount = 0;
        
        $logs[] = ['type' => 'info', 'message' => 'Starting seeding process...'];
        
        foreach ($statements as $statement) {
            if (empty($statement)) {
                continue;
            }
            
            try {
                $db->exec($statement);
                $successCount++;
                
                // Log INSERT
                if (stripos($statement, 'INSERT INTO') !== false) {
                    preg_match('/INSERT INTO `?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    
                    // Count how many rows
                    preg_match_all('/\),\s*\(/s', $statement, $rowMatches);
                    $rowCount = count($rowMatches[0]) + 1; // +1 for first row
                    
                    $logs[] = ['type' => 'success', 'message' => "✓ Inserted $rowCount row(s) into: $tableName"];
                    $insertCount += $rowCount;
                }
                
                // Log TRUNCATE
                if (stripos($statement, 'TRUNCATE') !== false) {
                    preg_match('/TRUNCATE TABLE? `?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    $logs[] = ['type' => 'info', 'message' => "Truncated table: $tableName"];
                }
                
                // Log DELETE
                if (stripos($statement, 'DELETE FROM') !== false) {
                    preg_match('/DELETE FROM `?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    $logs[] = ['type' => 'info', 'message' => "Deleted data from: $tableName"];
                }
                
            } catch (PDOException $e) {
                $errorCount++;
                
                // Extract table name from error
                $tableName = 'unknown';
                if (stripos($statement, 'INSERT INTO') !== false) {
                    preg_match('/INSERT INTO `?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                }
                
                $logs[] = ['type' => 'error', 'message' => "✗ Error seeding $tableName: " . $e->getMessage()];
            }
        }
        
        if ($errorCount === 0) {
            $message = "Seeders completed successfully! Inserted $insertCount rows.";
            $messageType = 'success';
            $logs[] = ['type' => 'success', 'message' => "✓ All seeders completed! Total rows: $insertCount"];
        } else {
            $message = "Seeders completed with errors. Success: $successCount, Errors: $errorCount";
            $messageType = 'warning';
            $logs[] = ['type' => 'error', 'message' => "⚠ Seeding finished with $errorCount error(s)"];
        }
        
    } catch (Exception $e) {
        $message = 'Seeding failed: ' . $e->getMessage();
        $messageType = 'danger';
        $logs[] = ['type' => 'error', 'message' => '✗ Fatal error: ' . $e->getMessage()];
    }
}

ob_start();
?>

<style>
.log-entry {
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 8px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    border-left: 4px solid;
}
.log-entry.info {
    background: #e7f3ff;
    border-color: #0c0f38;
    color: #004085;
}
.log-entry.success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}
.log-entry.error {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}
.seeder-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}
</style>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2">🌱 Database Seeders</h5>
            <p class="text-muted mb-0">Populate database with sample data</p>
        </div>
        <a href="security.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Security
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <strong><?= htmlspecialchars($message) ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($logs)): ?>
        <div class="mb-4">
            <h6 class="mb-3">Seeding Logs</h6>
            <?php foreach ($logs as $log): ?>
                <div class="log-entry <?= $log['type'] ?>">
                    <?php if ($log['type'] === 'success'): ?>
                        <i class="bi bi-check-circle-fill"></i>
                    <?php elseif ($log['type'] === 'error'): ?>
                        <i class="bi bi-x-circle-fill"></i>
                    <?php else: ?>
                        <i class="bi bi-info-circle-fill"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($log['message']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="seeder-box">
        <h6 class="mb-3">Run Seeders</h6>
        <p class="text-muted">
            This will populate your database with sample/default data from the seeders file.
        </p>
        
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Warning:</strong>
            <ul class="mb-0 mt-2">
                <li>This will insert data into existing tables</li>
                <li>Some seeders may truncate/delete existing data first</li>
                <li>Make sure you have run migrations before seeding</li>
                <li>Recommended for fresh installations or testing</li>
            </ul>
        </div>

        <form method="POST" onsubmit="return confirm('Are you sure you want to run seeders? This may overwrite existing data!')">
            <button type="submit" name="run_seeders" class="btn btn-success">
                <i class="bi bi-arrow-repeat"></i> Run Seeders
            </button>
        </form>
    </div>

    <div class="seeder-box">
        <h6 class="mb-3">📋 Data to be Seeded</h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><i class="bi bi-person-fill"></i> Admin User (default credentials)</li>
                    <li><i class="bi bi-file-text"></i> About Section</li>
                    <li><i class="bi bi-star-fill"></i> Skills (sample skills)</li>
                    <li><i class="bi bi-briefcase-fill"></i> Experience (work history)</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><i class="bi bi-mortarboard-fill"></i> Education (academic history)</li>
                    <li><i class="bi bi-folder-fill"></i> Portfolio (sample projects)</li>
                    <li><i class="bi bi-gear-fill"></i> Services</li>
                    <li><i class="bi bi-envelope-fill"></i> Contact Information</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-lightbulb-fill"></i>
        <strong>Tip:</strong> Run migrations first, then run seeders to populate with sample data.
        <br><br>
        <strong>Default Admin Credentials:</strong><br>
        Username: <code>admin</code><br>
        Password: <code>admin123</code>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-shield-lock-fill"></i>
        <strong>Security Note:</strong> This tool is only accessible to logged-in administrators.
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
