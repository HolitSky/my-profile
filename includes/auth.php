<?php
/**
 * Authentication Functions
 * Handles user authentication, session management, and security
 */

require_once __DIR__ . '/db.php';

/**
 * Initialize Session
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        session_start();
    }
}

/**
 * Check if user is logged in
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    initSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require Login
 * Redirect to login page if not authenticated
 */
function requireLogin() {
    initSession();
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Login User
 * @param string $username Username
 * @param string $password Password
 * @return array Result with success status and message
 */
function loginUser($username, $password) {
    $db = getDB();
    
    // Check rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    $rateLimitResult = checkRateLimit($ip);
    
    if (!$rateLimitResult['allowed']) {
        return [
            'success' => false,
            'message' => 'Too many login attempts. Please try again in ' . $rateLimitResult['wait_time'] . ' minutes.'
        ];
    }
    
    // Get user from database
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        initSession();
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        // Clear login attempts
        clearLoginAttempts($ip);
        
        // Update last login
        $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        return [
            'success' => true,
            'message' => 'Login successful!'
        ];
    } else {
        // Failed login
        recordLoginAttempt($ip, $username);
        
        return [
            'success' => false,
            'message' => 'Invalid username or password.'
        ];
    }
}

/**
 * Logout User
 */
function logoutUser() {
    initSession();
    $_SESSION = [];
    session_destroy();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

/**
 * Check Rate Limit
 * @param string $ip IP address
 * @return array Result with allowed status and wait time
 */
function checkRateLimit($ip) {
    $db = getDB();
    $maxAttempts = 7;
    $timeWindow = 15; // minutes
    
    $stmt = $db->prepare("
        SELECT COUNT(*) as attempt_count 
        FROM login_attempts 
        WHERE ip_address = ? 
        AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)
    ");
    $stmt->execute([$ip, $timeWindow]);
    $result = $stmt->fetch();
    
    if ($result['attempt_count'] >= $maxAttempts) {
        // Get time of first attempt in window
        $stmt = $db->prepare("
            SELECT attempt_time 
            FROM login_attempts 
            WHERE ip_address = ? 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)
            ORDER BY attempt_time ASC 
            LIMIT 1
        ");
        $stmt->execute([$ip, $timeWindow]);
        $firstAttempt = $stmt->fetch();
        
        $waitTime = $timeWindow - (int)((time() - strtotime($firstAttempt['attempt_time'])) / 60);
        
        return [
            'allowed' => false,
            'wait_time' => max(1, $waitTime)
        ];
    }
    
    return ['allowed' => true];
}

/**
 * Record Login Attempt
 * @param string $ip IP address
 * @param string $username Username attempted
 */
function recordLoginAttempt($ip, $username) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username, attempt_time) VALUES (?, ?, NOW())");
    $stmt->execute([$ip, $username]);
}

/**
 * Clear Login Attempts
 * @param string $ip IP address
 */
function clearLoginAttempts($ip) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $stmt->execute([$ip]);
}

/**
 * Generate CSRF Token
 * @return string CSRF token
 */
function generateCSRFToken() {
    initSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    initSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get Current Admin User
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}
