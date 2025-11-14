<?php
/**
 * Environment Configuration
 * Auto-detect Local vs Production
 */

// Detect environment based on server name
function getEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Local environment indicators
    $localHosts = ['localhost', '127.0.0.1', '::1', 'localhost:8080'];
    
    if (in_array($host, $localHosts) || strpos($host, '.local') !== false) {
        return 'local';
    }
    
    return 'production';
}

$environment = getEnvironment();

// ============================================
// LOCAL ENVIRONMENT CONFIGURATION
// ============================================
if ($environment === 'local') {
    
    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'khalid_portfolio');        // Local database name
    define('DB_USER', 'root');                 // Local username
    define('DB_PASS', '');                     // Local password (empty for XAMPP/WAMP)
    define('DB_CHARSET', 'utf8mb4');
    
    // Site Configuration
    define('SITE_URL', 'http://localhost/my-profile');  // Local URL
    define('UPLOAD_PATH', __DIR__ . '/../uploads/');
    define('UPLOAD_URL', SITE_URL . '/uploads/');
    
    // Debug Mode
    define('DEBUG_MODE', true);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Session Configuration
    define('SESSION_LIFETIME', 7200); // 2 hours for local
}

// ============================================
// PRODUCTION ENVIRONMENT CONFIGURATION
// ============================================
else {
    
    // Database Configuration - HOSTINGER
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u734000704_khalid_profile');    // ← GANTI: Production database name
    define('DB_USER', 'u734000704_root');        // ← GANTI: Production username
    define('DB_PASS', 'Dbkhalidprofile321.'); // ← GANTI: Production password
    define('DB_CHARSET', 'utf8mb4');
    
    // Site Configuration
    define('SITE_URL', 'https://khalidsaifullah.me'); // ← GANTI: Production URL
    define('UPLOAD_PATH', __DIR__ . '/../uploads/');
    define('UPLOAD_URL', SITE_URL . '/uploads/');
    
    // Debug Mode - DISABLED in production
    define('DEBUG_MODE', false);
    ini_set('display_errors', 0);
    error_reporting(0);
    
    // Session Configuration
    define('SESSION_LIFETIME', 3600); // 1 hour for production
}

// ============================================
// SHARED CONFIGURATION (Both environments)
// ============================================

// Environment info
define('ENVIRONMENT', $environment);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// ============================================
// DATABASE CONNECTION CLASS
// ============================================

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Log successful connection in local
            if (DEBUG_MODE) {
                error_log("Database connected successfully to " . DB_NAME);
            }
            
        } catch (PDOException $e) {
            // Show detailed error in local, generic in production
            if (DEBUG_MODE) {
                die("Connection failed: " . $e->getMessage());
            } else {
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection error. Please contact administrator.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Note: getDB() function moved to includes/db.php

// Helper function to check environment
function isLocal() {
    return ENVIRONMENT === 'local';
}

function isProduction() {
    return ENVIRONMENT === 'production';
}

// Display environment info (only in debug mode)
if (DEBUG_MODE && php_sapi_name() !== 'cli') {
    // Uncomment to see environment info
    // echo "<!-- Environment: " . ENVIRONMENT . " | DB: " . DB_NAME . " -->";
}
