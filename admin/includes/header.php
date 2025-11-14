<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'CMS Profile' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #0c0f38;
            color: white;
            position: fixed;
            width: 16.666667%;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
        }
        .sidebar-logo img {
            width: 40px;
            height: 40px;
            /* Keep original logo colors */
        }
        .sidebar-logo h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .main-content-wrapper {
            margin-left: 16.666667%;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-4">
                <div class="sidebar-logo">
                    <img src="../assets/images/logo.svg" alt="Logo">
                    <h4>CMS Profile</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>" href="about.php">
                        <i class="bi bi-person"></i> About
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'skills' ? 'active' : '' ?>" href="skills.php">
                        <i class="bi bi-star"></i> Skills
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'experience' ? 'active' : '' ?>" href="experience.php">
                        <i class="bi bi-briefcase"></i> Experience
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'education' ? 'active' : '' ?>" href="education.php">
                        <i class="bi bi-mortarboard"></i> Education
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'portfolio' ? 'active' : '' ?>" href="portfolio.php">
                        <i class="bi bi-folder"></i> Portfolio
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'services' ? 'active' : '' ?>" href="services.php">
                        <i class="bi bi-gear"></i> Services
                    </a>
                    <a class="nav-link <?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>" href="contact.php">
                        <i class="bi bi-envelope"></i> Contact Info
                    </a>
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                    <a class="nav-link" href="../index.html" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> View Site
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-0 main-content-wrapper">
                <!-- Top Navbar -->
                <nav class="navbar navbar-custom px-4">
                    <span class="navbar-brand mb-0 h1"><?= $pageTitle ?? 'CMS Profile' ?></span>
                    <?php if (isset($headerButton)): ?>
                        <?= $headerButton ?>
                    <?php endif; ?>
                </nav>

                <!-- Content -->
                <div class="p-4">
