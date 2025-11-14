<?php
/**
 * GitHub Webhook Auto-Deploy
 * Automatically pull latest code from GitHub when push event occurs
 * 
 * Setup:
 * 1. Set DEPLOY_SECRET in this file
 * 2. Add webhook in GitHub repo settings:
 *    - URL: https://www.khalidsaifullah.me/deploy.php
 *    - Content type: application/json
 *    - Secret: [same as DEPLOY_SECRET]
 *    - Events: Just the push event
 * 3. Make sure SSH key is set up for git pull
 * 
 * Protected by IP whitelist and secret token
 */

// ============================================
// CONFIGURATION
// ============================================

// Secret token for webhook verification (CHANGE THIS!)
// NOTE: This is NOT your GitHub PAT token! This is a webhook secret.
define('DEPLOY_SECRET', 'ed93417c86d1eee139dd20a6afce675c9a74a49737d7124e327bdf0defd9a516');

// Allowed IPs (GitHub webhook IPs + your IP)
define('DEPLOY_ALLOWED_IPS', [
    '127.0.0.1',           // Localhost
    '::1',                 // Localhost IPv6
    '180.252.241.181',     // Your IP
    // GitHub webhook IPs (add as needed)
    '140.82.112.0/20',     // GitHub webhooks
    '143.55.64.0/20',      // GitHub webhooks
    '192.30.252.0/22',     // GitHub webhooks
    '185.199.108.0/22',    // GitHub webhooks
]);

// Project paths
define('PROJECT_PATH', '/home/u734000704/domains/khalidsaifullah.me/public_html');
define('GIT_BRANCH', 'main'); // or 'master'

// Log file
define('DEPLOY_LOG', __DIR__ . '/deploy.log');

// Enable/disable deploy
define('DEPLOY_ENABLED', true);

// Allow manual trigger (without signature) from your IP
// Set to false in production for maximum security
define('ALLOW_MANUAL_TRIGGER', true);

// Your IP for manual trigger (when ALLOW_MANUAL_TRIGGER is true)
// Change this to your current IP address
define('MANUAL_TRIGGER_IP', '180.252.241.181');

// ============================================
// FUNCTIONS
// ============================================

/**
 * Log message to file
 */
function logMessage($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";
    file_put_contents(DEPLOY_LOG, $logEntry, FILE_APPEND);
    return $logEntry;
}

/**
 * Check if IP is allowed
 */
function isIPAllowed($ip) {
    foreach (DEPLOY_ALLOWED_IPS as $allowed) {
        // Check if it's a CIDR range
        if (strpos($allowed, '/') !== false) {
            if (ipInRange($ip, $allowed)) {
                return true;
            }
        } else {
            // Direct IP match
            if ($ip === $allowed) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Check if IP is in CIDR range
 */
function ipInRange($ip, $range) {
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    return ($ip & $mask) == $subnet;
}

/**
 * Verify GitHub webhook signature
 */
function verifyGitHubSignature($payload, $signature) {
    if (empty($signature)) {
        return false;
    }
    
    $hash = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);
    return hash_equals($hash, $signature);
}

/**
 * Execute shell command and return output
 */
function execCommand($command) {
    $output = [];
    $returnVar = 0;
    exec($command . ' 2>&1', $output, $returnVar);
    return [
        'success' => $returnVar === 0,
        'output' => implode("\n", $output),
        'code' => $returnVar
    ];
}

/**
 * Perform git pull
 */
function gitPull() {
    $commands = [
        'cd ' . PROJECT_PATH,
        'git fetch origin ' . GIT_BRANCH,
        'git reset --hard origin/' . GIT_BRANCH,
        'git pull origin ' . GIT_BRANCH
    ];
    
    $fullCommand = implode(' && ', $commands);
    return execCommand($fullCommand);
}

/**
 * Get git status
 */
function gitStatus() {
    $commands = [
        'cd ' . PROJECT_PATH,
        'git log -1 --pretty=format:"%H|%an|%ae|%s|%cd" --date=iso'
    ];
    
    $fullCommand = implode(' && ', $commands);
    $result = execCommand($fullCommand);
    
    if ($result['success']) {
        $parts = explode('|', $result['output']);
        return [
            'commit' => $parts[0] ?? 'unknown',
            'author' => $parts[1] ?? 'unknown',
            'email' => $parts[2] ?? 'unknown',
            'message' => $parts[3] ?? 'unknown',
            'date' => $parts[4] ?? 'unknown'
        ];
    }
    
    return null;
}

// ============================================
// MAIN EXECUTION
// ============================================

// Start logging
logMessage('Deploy request received from ' . $_SERVER['REMOTE_ADDR']);

// Check if deploy is enabled
if (!DEPLOY_ENABLED) {
    http_response_code(403);
    logMessage('Deploy is disabled', 'ERROR');
    die(json_encode(['success' => false, 'message' => 'Deploy is disabled']));
}

// Get client IP
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Check IP whitelist
if (!isIPAllowed($clientIP)) {
    http_response_code(403);
    logMessage('Access denied for IP: ' . $clientIP, 'ERROR');
    die(json_encode(['success' => false, 'message' => 'Access denied']));
}

logMessage('IP check passed: ' . $clientIP);

// Get request payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Verify signature (if from GitHub)
if (!empty($signature)) {
    if (!verifyGitHubSignature($payload, $signature)) {
        http_response_code(403);
        logMessage('Invalid signature', 'ERROR');
        die(json_encode(['success' => false, 'message' => 'Invalid signature']));
    }
    logMessage('Signature verified');
} else {
    // No signature provided
    if (!ALLOW_MANUAL_TRIGGER) {
        http_response_code(403);
        logMessage('Manual trigger disabled', 'ERROR');
        die(json_encode(['success' => false, 'message' => 'Manual trigger disabled. Use GitHub webhook.']));
    }
    
    // Allow only from your IP or localhost for manual testing
    $allowedManualIPs = [MANUAL_TRIGGER_IP, '127.0.0.1', '::1'];
    if (!in_array($clientIP, $allowedManualIPs)) {
        http_response_code(403);
        logMessage('No signature provided from IP: ' . $clientIP, 'ERROR');
        die(json_encode(['success' => false, 'message' => 'Signature required']));
    }
    logMessage('Manual trigger from allowed IP: ' . $clientIP, 'INFO');
}

// Parse payload
$data = json_decode($payload, true);

// Check if it's a push event
if (isset($data['ref'])) {
    $branch = str_replace('refs/heads/', '', $data['ref']);
    logMessage('Push event detected for branch: ' . $branch);
    
    // Only deploy if it's the correct branch
    if ($branch !== GIT_BRANCH) {
        logMessage('Ignoring push to branch: ' . $branch, 'INFO');
        die(json_encode(['success' => true, 'message' => 'Ignored (wrong branch)']));
    }
}

// Perform git pull
logMessage('Starting git pull...', 'INFO');
$result = gitPull();

if ($result['success']) {
    logMessage('Git pull successful', 'SUCCESS');
    logMessage('Output: ' . $result['output'], 'INFO');
    
    // Get current commit info
    $status = gitStatus();
    
    $response = [
        'success' => true,
        'message' => 'Deploy successful',
        'timestamp' => date('Y-m-d H:i:s'),
        'branch' => GIT_BRANCH,
        'commit' => $status
    ];
    
    logMessage('Deploy completed successfully', 'SUCCESS');
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    logMessage('Git pull failed: ' . $result['output'], 'ERROR');
    
    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Deploy failed',
        'error' => $result['output']
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
}

logMessage('Deploy process finished', 'INFO');
logMessage('----------------------------------------');
