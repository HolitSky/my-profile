<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

$pageTitle = 'Database Migrations';
$activePage = 'security';
$message = '';
$messageType = '';
$logs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migrations'])) {
    try {
        $db = getDB();
        $migrationsFile = __DIR__ . '/../database/migrations.sql';
        
        if (!file_exists($migrationsFile)) {
            throw new Exception('Migrations file not found: ' . $migrationsFile);
        }
        
        $sql = file_get_contents($migrationsFile);
        $logs[] = ['type' => 'info', 'message' => 'Reading migrations file...'];
        
        // Remove comments and split by semicolon
        $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $successCount = 0;
        $errorCount = 0;
        $dropCount = 0;
        $createCount = 0;
        
        $logs[] = ['type' => 'info', 'message' => 'Starting migration process...'];
        
        foreach ($statements as $statement) {
            if (empty($statement)) {
                continue;
            }
            
            try {
                $db->exec($statement);
                $successCount++;
                
                // Log DROP TABLE
                if (stripos($statement, 'DROP TABLE') !== false) {
                    preg_match('/DROP TABLE (?:IF EXISTS )?`?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    $logs[] = ['type' => 'info', 'message' => "Dropped table: $tableName"];
                    $dropCount++;
                }
                
                // Log CREATE TABLE
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    $logs[] = ['type' => 'success', 'message' => "✓ Created table: $tableName"];
                    $createCount++;
                }
            } catch (PDOException $e) {
                $errorCount++;
                
                // Extract table name from error
                $tableName = 'unknown';
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                }
                
                $logs[] = ['type' => 'error', 'message' => "✗ Error creating $tableName: " . $e->getMessage()];
            }
        }
        
        if ($errorCount === 0) {
            $message = "Migrations completed successfully! Created $createCount tables.";
            $messageType = 'success';
            $logs[] = ['type' => 'success', 'message' => "✓ All migrations completed! Dropped: $dropCount, Created: $createCount"];
        } else {
            $message = "Migrations completed with errors. Success: $successCount, Errors: $errorCount";
            $messageType = 'warning';
            $logs[] = ['type' => 'error', 'message' => "⚠ Migration finished with $errorCount error(s)"];
        }
        
    } catch (Exception $e) {
        $message = 'Migration failed: ' . $e->getMessage();
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
.migration-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}
</style>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2">🚀 Database Migrations</h5>
            <p class="text-muted mb-0">Run database migrations to setup tables</p>
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
            <h6 class="mb-3">Migration Logs</h6>
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

    <div class="migration-box">
        <h6 class="mb-3">Run Migrations</h6>
        <p class="text-muted">
            This will execute the SQL migrations file to create all necessary database tables.
        </p>
        
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Warning:</strong>
            <ul class="mb-0 mt-2">
                <li>This will create tables if they don't exist</li>
                <li>Existing tables will not be modified</li>
                <li>Make sure you have a database backup before running</li>
            </ul>
        </div>

        <form method="POST" onsubmit="return confirm('Are you sure you want to run migrations? Make sure you have a backup!')">
            <button type="submit" name="run_migrations" class="btn btn-primary">
                <i class="bi bi-arrow-repeat"></i> Run Migrations
            </button>
        </form>
    </div>

    <div class="migration-box">
        <h6 class="mb-3">📋 Tables to be Created</h6>
        <ul class="list-unstyled">
            <li><i class="bi bi-table"></i> admin_users</li>
            <li><i class="bi bi-table"></i> about</li>
            <li><i class="bi bi-table"></i> skills</li>
            <li><i class="bi bi-table"></i> experience</li>
            <li><i class="bi bi-table"></i> education</li>
            <li><i class="bi bi-table"></i> portfolio</li>
            <li><i class="bi bi-table"></i> services</li>
            <li><i class="bi bi-table"></i> contact_info</li>
        </ul>
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
