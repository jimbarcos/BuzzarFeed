<?php
/**
 * BuzzarFeed - About Page
 * 
 * Learn about BuzzarFeed and meet the team
 * 
 * @package BuzzarFeed
 * @version 1.0
 */

require_once __DIR__ . '/bootstrap.php';

use BuzzarFeed\Utils\Helpers;
use BuzzarFeed\Utils\Session;

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
    <meta name="description" content="<?= Helpers::escape($pageDescription) ?>">
    <title><?= Helpers::escape($pageTitle) ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= IMAGES_URL ?>/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Architecture -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/variables.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/button.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/dropdown.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/styles.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/about.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <?php include __DIR__ . '/sections/about/HeroSection.php'; ?>
        <hr class="section-divider" />
        <?php include __DIR__ . '/sections/about/AboutSection.php'; ?>
        <hr class="section-divider" />
        <?php include __DIR__ . '/sections/about/TeamSection.php'; ?>
        <?php include __DIR__ . '/sections/about/CTASection.php'; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script type="module" src="<?= JS_URL ?>/app.js"></script>
</body>
</html>