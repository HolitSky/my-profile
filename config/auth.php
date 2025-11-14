<?php
/**
 * Authentication Helper Functions
 */

// Start session if not already started
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    initSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Require login (redirect if not logged in)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

// Login user
function loginUser($userId, $username) {
    initSession();
    $_SESSION['admin_id'] = $userId;
    $_SESSION['admin_username'] = $username;
    $_SESSION['login_time'] = time();
}

// Logout user
function logoutUser() {
    initSession();
    session_unset();
    session_destroy();
}

// Get current admin user
function getCurrentUser() {
    initSession();
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username']
        ];
    }
    return null;
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// CSRF Token functions
function generateCSRFToken() {
    initSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    initSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// JSON Response helper
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Error response
function errorResponse($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'error' => $message], $statusCode);
}

// Success response
function successResponse($data, $message = 'Success') {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}
