<?php
/**
 * BuzzarFeed - About Page
 * 
 * Learn about BuzzarFeed and meet the team
 * 
 * @package BuzzarFeed
 * @version 1.0
 */

// [FE] TODO: Uncomment these once Backend sets up bootstrap
// require_once __DIR__ . '/bootstrap.php';
// use BuzzarFeed\Utils\Helpers;
// use BuzzarFeed\Utils\Session;
// Session::start();

$pageTitle = "About Us - BuzzarFeed";
$pageDescription = "Everything you need to know about BuzzarFeed - Your digital guide to the flavors of the BGC Night Market Bazaar";

// Hardcoded team members 
$teamMembers = [
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruzs', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
    ['name' => 'Juan Dela Cruz', 'position' => 'Member'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav style="padding:20px; text-align:center; background:#f0f0f0;">HEADER COMPONENT</nav>
    
    <main>
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">
                    Everything <span class="icon-stall"><i class="fas fa-store"></i></span> You<br>
                    Need <span class="icon-fire"><i class="fas fa-fire"></i></span> To Know<br>
                    About <span class="highlight">BuzzarFeed</span>
                </h1>
                <p class="hero-subtitle">
                    Welcome to BuzzarFeed — your digital guide to the flavors of the BGC Night Market Bazaar.
                </p>
            </div>
        </section>
        
        <section class="team-section">
            <div class="team-container">
                <div class="team-title-box">
                    <h2 class="team-title">Meet the<br>Team</h2>
                </div>
                
                <div class="team-grid">
                    <?php foreach ($teamMembers as $member): ?>
                        <div class="team-member">
                            <div class="member-photo">
                                <i class="fas fa-smile"></i>
                            </div>
                            <div class="member-info">
                                <p class="member-name"><?= htmlspecialchars($member['name']) ?></p>
                                <p class="member-position"><?= htmlspecialchars($member['position']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer style="padding:20px; text-align:center; background:#f0f0f0;">FOOTER COMPONENT</footer>
</body>
</html>