<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

initSession();

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Generate CAPTCHA
function generateCaptcha() {
    $num1 = rand(10, 20);
    $num2 = rand(1, 10);
    $operation = rand(0, 1) ? '+' : '-';
    $result = ($operation == '+') ? $num1 + $num2 : $num1 - $num2;
    $captcha_text = "$num1 $operation $num2 = ?";
    
    // Create image (smaller height)
    $image = imagecreatetruecolor(200, 50);
    $bg = imagecolorallocate($image, 12, 15, 56); // #0c0f38
    $fg = imagecolorallocate($image, 255, 255, 255);
    $line = imagecolorallocate($image, 100, 100, 100);
    imagefill($image, 0, 0, $bg);
    
    // Add noise lines
    for ($i = 0; $i < 3; $i++) {
        imageline($image, rand(0, 200), rand(0, 50), rand(0, 200), rand(0, 50), $line);
    }
    
    // Add text (adjusted position)
    imagestring($image, 5, 40, 17, $captcha_text, $fg);
    
    // Add noise pixels
    for ($i = 0; $i < 120; $i++) {
        imagesetpixel($image, rand(0, 200), rand(0, 50), $fg);
    }
    
    // Output image
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);
    
    $base64 = 'data:image/png;base64,' . base64_encode($imageData);
    
    // Store CAPTCHA result in session
    $_SESSION['captcha_answer'] = strval($result);
    $_SESSION['captcha_image'] = $base64;
    
    return $base64;
}

// Initialize CAPTCHA on first load or refresh
if (!isset($_SESSION['captcha_answer']) || isset($_GET['refresh_captcha'])) {
    generateCaptcha();
}

// Handle CAPTCHA refresh via AJAX
if (isset($_GET['refresh_captcha'])) {
    header('Content-Type: application/json');
    echo json_encode(['captcha' => $_SESSION['captcha_image']]);
    exit;
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

// Rate limiting: Track failed login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Reset attempts after 15 minutes
if (time() - $_SESSION['last_attempt_time'] > 900) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Check CSRF token first
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error = 'Invalid security token. Please refresh the page.';
        $_SESSION['login_attempts']++;
    }
    // Check rate limiting
    elseif ($_SESSION['login_attempts'] >= 7) {
        $error = 'Too many failed attempts. Please try again in 15 minutes.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Username and password are required';
        $_SESSION['login_attempts']++;
    } elseif (empty($captcha) || $captcha !== $_SESSION['captcha_answer']) {
        $error = 'Invalid CAPTCHA answer';
        $_SESSION['login_attempts']++;
        generateCaptcha(); // Refresh CAPTCHA on error
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Success: Reset attempts and login
                $_SESSION['login_attempts'] = 0;
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                session_regenerate_id(true);
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password';
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                generateCaptcha(); // Refresh CAPTCHA on error
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again.';
            $_SESSION['login_attempts']++;
            generateCaptcha();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CMS Profile</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #0c0f38;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px;
        }
        /* Video Background Overlay */
        .video-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 0;
            opacity: 0.35;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(12, 15, 56, 0.65);
            z-index: 1;
        }
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            padding: 25px 30px;
            max-width: 420px;
            width: 100%;
            position: relative;
            z-index: 2;
            margin: auto;
        }
        /* Responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            .login-card {
                padding: 20px;
                border-radius: 10px;
            }
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }
        .login-header h1 {
            color: #0c0f38;
            font-size: 24px;
            margin-bottom: 3px;
            font-weight: 700;
        }
        .login-header p {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }
        /* Responsive header */
        @media (max-width: 576px) {
            .login-header img {
                width: 45px;
                height: 45px;
                margin-bottom: 8px;
            }
            .login-header h1 {
                font-size: 22px;
            }
            .login-header {
                margin-bottom: 15px;
            }
        }
        .btn-login {
            background: #0c0f38;
            border: none;
            padding: 10px;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 15px;
        }
        .btn-login:hover {
            background: #1a1f5c;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(12, 15, 56, 0.4);
        }
        .captcha-container {
            position: relative;
            margin-bottom: 10px;
        }
        .captcha-image {
            width: 100%;
            height: 50px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #0c0f38;
        }
        .refresh-captcha {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        .refresh-captcha:hover {
            background: white;
            transform: translateY(-50%) rotate(180deg);
        }
        .security-badge {
            background: #e7f3ff;
            border-left: 3px solid #0c0f38;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 11px;
        }
        .security-badge ul {
            margin-bottom: 0;
            padding-left: 18px;
        }
        .security-badge li {
            font-size: 11px;
            margin-bottom: 2px;
        }
        .attempts-warning {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .form-label {
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            font-size: 14px;
            padding: 8px 12px;
        }
        .mb-3 {
            margin-bottom: 12px !important;
        }
        .alert {
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        small.text-muted {
            font-size: 11px;
        }
    </style>
</head>
<body>
    <!-- Video Background Overlay -->
    <video class="video-overlay" muted autoplay loop playsinline>
        <source src="../assets/images/video/video4.mp4" type="video/mp4">
    </video>
    
    <div class="login-card">
        <div class="login-header">
            <img src="../assets/images/logo.svg" alt="Logo">
            <h1>Admin Login</h1>
            <p>CMS Profile</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($_SESSION['login_attempts'] > 0 && $_SESSION['login_attempts'] < 7): ?>
            <div class="attempts-warning">
                <i class="bi bi-shield-exclamation"></i>
                <strong>Warning:</strong> <?= $_SESSION['login_attempts'] ?> failed attempt(s). 
                <?= 7 - $_SESSION['login_attempts'] ?> remaining.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person-fill"></i> Username
                </label>
                <input type="text" class="form-control" id="username" name="username" required autofocus autocomplete="username">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock-fill"></i> Password
                </label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <div class="mb-3">
                <label for="captcha" class="form-label">
                    <i class="bi bi-shield-check"></i> Security Check
                </label>
                <div class="captcha-container">
                    <img src="<?= $_SESSION['captcha_image'] ?>" alt="CAPTCHA" class="captcha-image" id="captchaImage">
                    <button type="button" class="refresh-captcha" onclick="refreshCaptcha()" title="Refresh CAPTCHA">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <input type="text" class="form-control mt-2" id="captcha" name="captcha" placeholder="Enter the answer" required autocomplete="off" inputmode="numeric">
                <small class="text-muted">Solve the math problem above</small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
        
        <div class="security-badge">
            <i class="bi bi-shield-lock-fill"></i>
            <strong>Security Features:</strong>
            <ul class="mb-0 mt-2" style="font-size: 12px;">
                <li>CSRF token protection</li>
                <li>Rate limiting (7 attempts per 15 min)</li>
                <li>Math CAPTCHA verification</li>
                <li>Password hashing (bcrypt)</li>
                <li>SQL injection prevention</li>
            </ul>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function refreshCaptcha() {
        const captchaImage = document.getElementById('captchaImage');
        const captchaInput = document.getElementById('captcha');
        
        // Show loading state
        captchaImage.style.opacity = '0.5';
        
        // Fetch new CAPTCHA
        fetch('login.php?refresh_captcha=1')
            .then(response => response.json())
            .then(data => {
                captchaImage.src = data.captcha;
                captchaImage.style.opacity = '1';
                captchaInput.value = '';
                captchaInput.focus();
            })
            .catch(error => {
                console.error('Error refreshing CAPTCHA:', error);
                captchaImage.style.opacity = '1';
            });
    }
    </script>
</body>
</html>
