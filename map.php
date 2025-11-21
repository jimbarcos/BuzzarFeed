<?php
/**
 * BuzzarFeed - Map Page
 * 
 * Interactive map showing all approved food stalls
 * 
 * @package BuzzarFeed
 * @version 1.0
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

// --- MOCK DATA: Stalls (Simulating Database) ---
$mockStalls = [
    [
        'id' => 1,
        'name' => 'Mama Lou\'s Grill',
        'description' => 'Best BBQ in town with secret sauce. We serve fresh cuts daily.',
        'latitude' => 20, 
        'longitude' => 40,
        'categories' => ['Street Food', 'Rice Meals'],
        'rating' => 4.5,
        'reviews' => 12,
        'hours' => '10:00 AM - 9:00 PM',
        'image' => 'stall1.jpg' // Ensure you have a placeholder or leave empty
    ],
    [
        'id' => 2,
        'name' => 'Sweet Corner',
        'description' => 'Traditional pastries and sweet drinks.',
        'latitude' => 60,
        'longitude' => 30,
        'categories' => ['Pastries', 'Beverages'],
        'rating' => 4.8,
        'reviews' => 8,
        'hours' => '8:00 AM - 6:00 PM',
        'image' => ''
    ],
    [
        'id' => 3,
        'name' => 'Burger Bros',
        'description' => 'Juicy smashed burgers.',
        'latitude' => 40,
        'longitude' => 70,
        'categories' => ['Fast Food'],
        'rating' => 4.2,
        'reviews' => 20,
        'hours' => '12:00 PM - 10:00 PM',
        'image' => ''
    ]
];

// Get filter parameters
$category = Helpers::get('category', '');

// Fetch stalls based on filters
if (!empty($category) && $category !== 'all') {
    $stalls = array_filter($mockStalls, function($stall) use ($category) {
        return in_array($category, $stall['categories']);
    });
} else {
    $stalls = $mockStalls;
}

// Define standard food categories
$standardCategories = ['Beverages', 'Street Food', 'Rice Meals', 'Fast Food', 'Snacks', 'Pastries', 'Others'];
$allCategories = $standardCategories;

// Filter stalls that have map coordinates
$stallsWithLocation = array_filter($stalls, function($stall) {
    return !empty($stall['latitude']) && !empty($stall['longitude']);
});

// Get nearby stalls (random 3 for now, can be enhanced with geolocation)
$nearbyStalls = array_slice($stalls, 0, 3);
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
