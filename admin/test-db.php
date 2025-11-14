<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

$pageTitle = 'Database Test';
$activePage = 'security';
ob_start();

$testResults = [];
$overallStatus = 'success';

// Test 1: Database Connection
try {
    $db = getDB();
    $testResults[] = [
        'name' => 'Database Connection',
        'status' => 'success',
        'message' => 'Successfully connected to database'
    ];
} catch (Exception $e) {
    $testResults[] = [
        'name' => 'Database Connection',
        'status' => 'error',
        'message' => 'Failed: ' . $e->getMessage()
    ];
    $overallStatus = 'error';
}

// Test 2: Check Tables
if ($overallStatus === 'success') {
    try {
        $tables = ['admin_users', 'about', 'skills', 'experience', 'education', 'portfolio', 'services', 'contact_info'];
        $existingTables = [];
        
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
            }
        }
        
        if (count($existingTables) === count($tables)) {
            $testResults[] = [
                'name' => 'Database Tables',
                'status' => 'success',
                'message' => 'All ' . count($tables) . ' tables exist'
            ];
        } else {
            $testResults[] = [
                'name' => 'Database Tables',
                'status' => 'warning',
                'message' => count($existingTables) . ' of ' . count($tables) . ' tables found'
            ];
            $overallStatus = 'warning';
        }
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Database Tables',
            'status' => 'error',
            'message' => 'Failed: ' . $e->getMessage()
        ];
        $overallStatus = 'error';
    }
}

// Test 3: Check Admin User
if ($overallStatus !== 'error') {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM admin_users");
        $count = $stmt->fetch()['count'];
        
        $testResults[] = [
            'name' => 'Admin Users',
            'status' => 'success',
            'message' => $count . ' admin user(s) found'
        ];
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Admin Users',
            'status' => 'error',
            'message' => 'Failed: ' . $e->getMessage()
        ];
        $overallStatus = 'error';
    }
}

// Get database info
$dbInfo = [];
try {
    $dbInfo['host'] = DB_HOST;
    $dbInfo['database'] = DB_NAME;
    $dbInfo['user'] = DB_USER;
    $dbInfo['charset'] = 'utf8mb4';
} catch (Exception $e) {
    $dbInfo['error'] = $e->getMessage();
}
?>

<style>
.test-result {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid;
}
.test-result.success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}
.test-result.warning {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}
.test-result.error {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}
.test-result h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
}
.test-result p {
    margin: 0;
    font-size: 14px;
}
.info-table {
    width: 100%;
    margin-top: 20px;
}
.info-table tr {
    border-bottom: 1px solid #dee2e6;
}
.info-table td {
    padding: 12px 8px;
}
.info-table td:first-child {
    font-weight: 600;
    color: #0c0f38;
    width: 150px;
}
</style>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2">🧪 Database Connection Test</h5>
            <p class="text-muted mb-0">Test database connectivity and configuration</p>
        </div>
        <a href="security.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Security
        </a>
    </div>

    <?php if ($overallStatus === 'success'): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <strong>All tests passed!</strong> Database is properly configured.
        </div>
    <?php elseif ($overallStatus === 'warning'): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Some issues detected.</strong> Please review the results below.
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-x-circle-fill"></i>
            <strong>Tests failed!</strong> Please check your database configuration.
        </div>
    <?php endif; ?>

    <h6 class="mb-3">Test Results</h6>
    <?php foreach ($testResults as $result): ?>
        <div class="test-result <?= $result['status'] ?>">
            <h6>
                <?php if ($result['status'] === 'success'): ?>
                    <i class="bi bi-check-circle-fill"></i>
                <?php elseif ($result['status'] === 'warning'): ?>
                    <i class="bi bi-exclamation-triangle-fill"></i>
                <?php else: ?>
                    <i class="bi bi-x-circle-fill"></i>
                <?php endif; ?>
                <?= $result['name'] ?>
            </h6>
            <p><?= $result['message'] ?></p>
        </div>
    <?php endforeach; ?>

    <h6 class="mb-3 mt-4">Database Configuration</h6>
    <table class="info-table">
        <tr>
            <td>Host</td>
            <td><?= htmlspecialchars($dbInfo['host'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <td>Database</td>
            <td><?= htmlspecialchars($dbInfo['database'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><?= htmlspecialchars($dbInfo['user'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <td>Charset</td>
            <td><?= htmlspecialchars($dbInfo['charset'] ?? 'N/A') ?></td>
        </tr>
    </table>

    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle-fill"></i>
        <strong>Note:</strong> This test is only accessible to logged-in administrators.
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
