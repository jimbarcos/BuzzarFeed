<?php
/*
PROGRAM NAME: About Page (about.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It generates the "About Us" page for the BuzzarFeed website, providing information about the platform, its purpose, and the team behind it.
It relies on the bootstrap file to initialize the environment, load configurations, start sessions, and provide utility helpers for rendering.

DATE CREATED: November 20, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to display the "About Us" page content in a structured and dynamic manner.
It includes sections for a hero/banner, an overview of BuzzarFeed, the team members, and a call-to-action.
It also ensures proper HTML metadata, links to CSS and JavaScript resources, and includes reusable header and footer components.

DATA STRUCTURES:
- $pageTitle (string): The title of the page for the <title> tag.
- $pageDescription (string): The description for SEO purposes in <meta name="description">.
- $teamMembers (array of associative arrays): List of team members with 'name' and 'position' keys.
- Session (class): Handles session initialization via Session::start().
- Helpers (class): Provides helper functions such as escape() for safe HTML output.

ALGORITHM / LOGIC:
1. Include `bootstrap.php` to initialize the environment and load helpers.
2. Start the session using `Session::start()`.
3. Define page metadata variables:
   a. $pageTitle – The page title displayed in the browser tab.
   b. $pageDescription – SEO-friendly description of the page.
4. Define $teamMembers array containing team member names and positions.
5. Begin HTML document with proper <!DOCTYPE html> and <html> tag.
6. In <head>:
   a. Set charset and viewport for responsive design.
   b. Set <meta name="description"> using Helpers::escape().
   c. Set <title> using Helpers::escape().
   d. Load favicon, Google Fonts, Font Awesome, and CSS stylesheets.
7. In <body>:
   a. Include header template.
   b. Render main content:
      i. Hero section.
      ii. About section.
      iii. Team section.
      iv. Call-to-Action section.
   c. Include footer template.
8. Load main JavaScript module (`app.js`) at the bottom of the body.

NOTES:
- Team members are defined in a PHP array to allow easy updates or dynamic rendering in the TeamSection.
- Helpers::escape() is used to prevent XSS attacks in page title and description.
- Sections are included as separate PHP partials for modularity and maintainability.
- CSS is split into variables, base styles, components, and page-specific styles for clarity.
- Future enhancements could include fetching team members from a database instead of a hard-coded array, or adding animations and interactive elements in the Hero and CTA sections.
*/

require_once __DIR__ . '/bootstrap.php';

use BuzzarFeed\Utils\Helpers;
use BuzzarFeed\Utils\Session;

Session::start();

$pageTitle = "About Us - BuzzarFeed";
$pageDescription = "Everything you need to know about BuzzarFeed - Your digital guide to the flavors of the BGC Night Market Bazaar";
 
$teamMembers = [
    ['name' => 'Chrysler Dele B. Ordas', 'position' => 'Project Manager'],
    ['name' => 'Jimalyn B. Del Rosario', 'position' => 'Business Analyst'],
    ['name' => 'Princess Jane C. Drama', 'position' => 'UI/UX Designer'],
    ['name' => 'Jim Aerol Barcos', 'position' => 'Backend Developer'],
    ['name' => 'Acelle Krislette L. Rosales', 'position' => 'Backend Developer'],
    ['name' => 'Regina S. Bonifacio', 'position' => 'Backend Developer'],
    ['name' => 'John Lloyd S. Legaspi', 'position' => 'Frontend Developer'],
    ['name' => 'Kyla Mae N. Valoria', 'position' => 'Frontend Developer'],
    ['name' => 'Xavier B. Rolle', 'position' => 'Quality Assurance Engineer'],
    ['name' => 'Ernest Matthew L. Maravilla', 'position' => 'Documentation Specialist'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= Helpers::escape($pageDescription) ?>">
    <title><?= Helpers::escape($pageTitle) ?></title>
    
    <link rel="icon" type="image/png" href="<?= IMAGES_URL ?>/favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        <hr class="section-divider" />
        <?php include __DIR__ . '/sections/about/CTASection.php'; ?>
    </main>
    
    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script type="module" src="<?= JS_URL ?>/app.js"></script>
</body>
</html>