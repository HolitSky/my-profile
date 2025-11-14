<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$currentUser = getCurrentUser();
$db = getDB();

// Get statistics
$stats = [
    'skills' => $db->query("SELECT COUNT(*) as count FROM skills")->fetch()['count'],
    'experience' => $db->query("SELECT COUNT(*) as count FROM experience")->fetch()['count'],
    'education' => $db->query("SELECT COUNT(*) as count FROM education")->fetch()['count'],
    'portfolio' => $db->query("SELECT COUNT(*) as count FROM portfolio")->fetch()['count'],
];

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
ob_start();
?>

<style>
    .stat-card {
        border-radius: 15px;
        padding: 25px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
</style>

<div class="mb-4">
    <h2>Welcome to Your CMS Profile</h2>
    <p class="text-muted">Logged in as <strong><?= htmlspecialchars($currentUser['username']) ?></strong></p>
</div>
                    
                    <!-- Statistics Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['skills'] ?></h3>
                                        <p class="text-muted mb-0">Skills</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['experience'] ?></h3>
                                        <p class="text-muted mb-0">Experience</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['education'] ?></h3>
                                        <p class="text-muted mb-0">Education</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                        <i class="bi bi-folder-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['portfolio'] ?></h3>
                                        <p class="text-muted mb-0">Projects</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="stat-card">
                                <h5 class="mb-3">Quick Actions</h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="about.php" class="btn btn-outline-primary">
                                        <i class="bi bi-person"></i> Edit About
                                    </a>
                                    <a href="skills.php" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Add Skill
                                    </a>
                                    <a href="experience.php" class="btn btn-outline-warning">
                                        <i class="bi bi-plus-circle"></i> Add Experience
                                    </a>
                                    <a href="portfolio.php" class="btn btn-outline-info">
                                        <i class="bi bi-plus-circle"></i> Add Project
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Getting Started</h6>
                                <ul class="mb-0">
                                    <li>Update your <strong>About</strong> section to personalize your portfolio</li>
                                    <li>Add your <strong>Skills</strong> with proficiency levels</li>
                                    <li>Add your work <strong>Experience</strong> and <strong>Education</strong></li>
                                    <li>Upload your <strong>Portfolio</strong> projects</li>
                                    <li>All changes will be reflected on your live website automatically</li>
                                </ul>
                            </div>
                        </div>
                    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
?>
