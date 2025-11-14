<?php
/**
 * Simple .env File Loader
 * No external dependencies required
 * 
 * Usage:
 *   require_once __DIR__ . '/includes/env.php';
 *   $value = env('KEY_NAME', 'default_value');
 */

class Env {
    private static $loaded = false;
    private static $values = [];

    /**
     * Load .env file
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }

        if (!file_exists($path)) {
            // .env file not found, use environment variables only
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes
                $value = trim($value, '"\'');
                
                // Store in array
                self::$values[$key] = $value;
                
                // Also set as environment variable
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        // Auto-load if not loaded
        if (!self::$loaded) {
            self::load();
        }

        // Check our array first
        if (isset(self::$values[$key])) {
            return self::$values[$key];
        }

        // Check $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Check getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        // Return default
        return $default;
    }

    /**
     * Get as boolean
     */
    public static function getBool($key, $default = false) {
        $value = self::get($key, $default);
        
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['true', '1', 'yes', 'on']);
    }

    /**
     * Get as integer
     */
    public static function getInt($key, $default = 0) {
        return (int) self::get($key, $default);
    }

    /**
     * Get as array (comma-separated)
     */
    public static function getArray($key, $default = []) {
        $value = self::get($key);
        
        if (empty($value)) {
            return $default;
        }

        if (is_array($value)) {
            return $value;
        }

        return array_map('trim', explode(',', $value));
    }
}

/**
 * Helper function to get environment variable
 */
function env($key, $default = null) {
    return Env::get($key, $default);
}

/**
 * Helper function to get boolean environment variable
 */
function env_bool($key, $default = false) {
    return Env::getBool($key, $default);
}

/**
 * Helper function to get integer environment variable
 */
function env_int($key, $default = 0) {
    return Env::getInt($key, $default);
}

/**
 * Helper function to get array environment variable
 */
function env_array($key, $default = []) {
    return Env::getArray($key, $default);
}

// Auto-load .env file
Env::load();
