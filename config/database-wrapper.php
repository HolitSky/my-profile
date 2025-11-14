<?php
/**
 * Database Configuration Wrapper
 * 
 * This file provides backward compatibility.
 * It loads the new config.php which has auto environment detection.
 * 
 * USAGE:
 * If you want to keep using require 'config/database.php' in your files,
 * rename this file to 'database.php'
 */

// Load new configuration system
require_once __DIR__ . '/config.php';

// All constants are now defined:
// - DB_HOST
// - DB_NAME
// - DB_USER
// - DB_PASS
// - DB_CHARSET
// - SITE_URL
// - UPLOAD_PATH
// - UPLOAD_URL
// - SESSION_LIFETIME
// - ENVIRONMENT
// - DEBUG_MODE

// Database connection class and getDB() function are also available
