<?php
/**
 * BuzzarFeed - Map Page
 * Phase 1: Layout Setup
 */

// --- TEMPORARY: Mock Dependencies (Delete when bootstrap.php is ready) ---
define('IMAGES_URL', 'assets/images');
define('CSS_URL', 'assets/css');
define('JS_URL', 'assets/js');
define('BASE_URL', 'index.php');

class Helpers {
    public static function escape($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
    public static function get($key, $default = '') {
        return $_GET[$key] ?? $default;
    }
}
// -----------------------------------------------------------------------------------

$pageTitle = "Stall Map - BuzzarFeed";
$pageDescription = "Find food stalls on the interactive map";

// Initial Mock Category
$category = Helpers::get('category', '');
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
    <link rel="preconnect" href="[https://fonts.googleapis.com](https://fonts.googleapis.com)">
    <link rel="preconnect" href="[https://fonts.gstatic.com](https://fonts.gstatic.com)" crossorigin>
    <link href="[https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap)" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css)">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/variables.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/button.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/components/dropdown.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/styles.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/map.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <h1>Where the Flavor Lives</h1>
            <p>Locate stalls, filter by food type, and never miss a hidden gem at "the bazaar"</p>
        </section>
        
        <!-- Placeholders for next phases -->
        <div style="text-align:center; padding: 50px; background: #f5f5f5; color: #999;">
            [Filters, Map, and Nearby Stalls will be implemented here]
        </div>
    </main>
    
    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script type="module" src="<?= JS_URL ?>/app.js"></script>
</body>
</html>
