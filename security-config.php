<?php
/**
 * Security Configuration
 * Uses .env file for sensitive data
 */

// Load environment variables
require_once __DIR__ . '/includes/env.php';

// ============================================
// SECURITY SETTINGS (from .env)
// ============================================

// Enable/Disable utility files (from .env)
define('ENABLE_UTILITY_FILES', env_bool('ENABLE_UTILITY_FILES', true));

// Allowed IPs (from .env, comma-separated)
$allowedIPsFromEnv = env_array('ALLOWED_IPS', ['127.0.0.1', '::1']);
$yourIP = env('YOUR_IP');
if ($yourIP && !in_array($yourIP, $allowedIPsFromEnv)) {
    $allowedIPsFromEnv[] = $yourIP;
}
define('ALLOWED_IPS', $allowedIPsFromEnv);

// ============================================
// AUTO ENVIRONMENT DETECTION
// ============================================

function checkIsLocalhost() {
    $whitelist = ['127.0.0.1', '::1', 'localhost'];
    return in_array($_SERVER['REMOTE_ADDR'] ?? '', $whitelist);
}

function checkIsAllowedIP() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($clientIP, ALLOWED_IPS);
}

function checkIsProduction() {
    return !checkIsLocalhost();
}

// ============================================
// PROTECTION CHECK
// ============================================

function checkUtilityAccess($filename = '') {
    // If utility files disabled, block all access
    if (!ENABLE_UTILITY_FILES) {
        http_response_code(403);
        die('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Access Forbidden</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f5f5f5;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    margin: 0;
                }
                .error-box {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-align: center;
                    max-width: 500px;
                }
                h1 { color: #dc3545; margin-bottom: 20px; }
                p { color: #666; line-height: 1.6; }
                .code { 
                    background: #f8f9fa; 
                    padding: 10px; 
                    border-radius: 5px; 
                    margin: 20px 0;
                    font-family: monospace;
                }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>🔒 Access Forbidden</h1>
                <p><strong>This utility file is disabled.</strong></p>
                
                <p style="color: #dc3545; margin-top: 20px;">
                    <strong>⚠️ Only enable in development environment!</strong>
                </p>
            </div>
        </body>
        </html>
        ');
    }
    
    // If enabled, check if localhost or allowed IP
    if (!checkIsLocalhost() && !checkIsAllowedIP()) {
        http_response_code(403);
        die('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Access Forbidden</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f5f5f5;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    margin: 0;
                }
                .error-box {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-align: center;
                    max-width: 500px;
                }
                h1 { color: #dc3545; margin-bottom: 20px; }
                p { color: #666; line-height: 1.6; }
                .code {
                    background: #f8f9fa;
                    padding: 10px;
                    border-radius: 5px;
                    margin: 15px 0;
                    font-family: monospace;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>🔒 Access Forbidden</h1>
                <p><strong>This file is only accessible from whitelisted IPs.</strong></p>
                <div class="code">Your IP: ' . htmlspecialchars($_SERVER['REMOTE_ADDR']) . '</div>
                <p style="color: #dc3545; margin-top: 20px;">
                    <strong>Access denied for security reasons.</strong>
                </p>
                <p style="font-size: 12px; color: #999; margin-top: 20px;">
                    Go Away!
                </p>
            </div>
        </body>
        </html>
        ');
    }
    
    // Access granted
    return true;
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function getSecurityStatus() {
    return [
        'utility_files_enabled' => ENABLE_UTILITY_FILES,
        'is_localhost' => checkIsLocalhost(),
        'is_production' => checkIsProduction(),
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'mode' => ENABLE_UTILITY_FILES ? 'Development' : 'Production'
    ];
}
