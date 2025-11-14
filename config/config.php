<?php
/**
 * Environment Configuration
 * Uses .env file for sensitive data
 */

// Load environment variables
require_once __DIR__ . '/../includes/env.php';

// Detect environment
function getEnvironment() {
    // Check .env first
    $envFromFile = env('APP_ENV');
    if ($envFromFile) {
        return $envFromFile;
    }
    
    // Auto-detect based on host
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
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
    
    // Database Configuration (from .env or defaults)
    define('DB_HOST', env('DB_HOST', 'localhost'));
    define('DB_NAME', env('DB_NAME', 'khalid_portfolio'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
    define('DB_CHARSET', 'utf8mb4');
    
    // Site Configuration
    define('SITE_URL', env('APP_URL', 'http://localhost/my-profile'));
    define('UPLOAD_PATH', __DIR__ . '/../uploads/');
    define('UPLOAD_URL', SITE_URL . '/uploads/');
    
    // Debug Mode
    define('DEBUG_MODE', env_bool('APP_DEBUG', true));
    ini_set('display_errors', DEBUG_MODE ? 1 : 0);
    error_reporting(DEBUG_MODE ? E_ALL : 0);
    
    // Session Configuration
    define('SESSION_LIFETIME', env_int('SESSION_LIFETIME', 7200));
}

// ============================================
// PRODUCTION ENVIRONMENT CONFIGURATION
// ============================================
else {
    
    // Database Configuration (from .env - REQUIRED in production!)
    define('DB_HOST', env('DB_HOST', 'localhost'));
    define('DB_NAME', env('DB_NAME'));  // Must be set in .env
    define('DB_USER', env('DB_USER'));  // Must be set in .env
    define('DB_PASS', env('DB_PASS'));  // Must be set in .env
    define('DB_CHARSET', 'utf8mb4');
    
    // Site Configuration (from .env or default)
    define('SITE_URL', env('APP_URL', 'https://khalidsaifullah.me'));
    define('UPLOAD_PATH', __DIR__ . '/../uploads/');
    define('UPLOAD_URL', SITE_URL . '/uploads/');
    
    // Debug Mode (from .env, default false in production)
    define('DEBUG_MODE', env_bool('APP_DEBUG', false));
    ini_set('display_errors', DEBUG_MODE ? 1 : 0);
    error_reporting(DEBUG_MODE ? E_ALL : 0);
    
    // Session Configuration
    define('SESSION_LIFETIME', env_int('SESSION_LIFETIME', 3600));
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

// ============================================
// LOAD DATABASE FUNCTIONS
// ============================================
// Load getDB() function and other database utilities
require_once __DIR__ . '/../includes/db.php';
