<?php
/**
 * Database Connection
 * Provides PDO database connection using config settings
 * 
 * NOTE: This file is loaded by config.php, so don't require config.php here!
 */

// Config constants should already be defined by config.php

/**
 * Get Database Connection
 * @return PDO Database connection object
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log error instead of displaying
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'local') {
                die("Database Connection Failed: " . $e->getMessage());
            } else {
                error_log("Database Connection Failed: " . $e->getMessage());
                die("Database connection error. Please contact administrator.");
            }
        }
    }
    
    return $db;
}

/**
 * Sanitize Input
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
