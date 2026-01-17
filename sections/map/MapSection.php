<?php
/*
PROGRAM NAME: Map View Section (map-section.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It serves as the main map view section of the application, allowing users to visually explore food stalls within the bazaar.
The file is designed as a partial and is included within a larger page layout that handles routing, data fetching, and overall page structure.
It relies on preloaded data such as stall information, categories, and configuration constants, as well as utility helpers for safe output rendering.

DATE CREATED: November 30, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to render an interactive map interface that displays food stalls as pins based on their physical locations.
It provides category-based filtering, visual cues through color-coded pins, and a tooltip system that allows users to preview stall information.
This module enhances user navigation and discovery by combining visual mapping with dynamic filtering.

DATA STRUCTURES:
- $categoryColors (associative array): Maps food categories to specific hex color codes used for map pins.
- $defaultPinColor (string): Fallback color for pins when a category color is not defined.
- $categoryIcons (associative array): Maps food categories to emoji icons for filter buttons.
- $allCategories (array): List of all available food categories.
- $stallsWithLocation (array of associative arrays): Contains stall data including:
  - id
  - name
  - description
  - rating
  - categories
  - latitude (percentage-based)
  - longitude (percentage-based)
- Helpers (class): Provides helper functions such as escape() for safe HTML output.

ALGORITHM / LOGIC:
1. Define category-to-color mappings for consistent pin visualization.
2. Set a default pin color for unmatched or undefined categories.
3. Render the hero section introducing the map feature.
4. Display category filter buttons:
   a. Highlight the active category.
   b. Generate filter links dynamically based on available categories.
   c. Assign emoji icons to each category.
5. Render the map container and background image.
6. Loop through $stallsWithLocation:
   a. Determine the display category for each stall.
   b. Override the pin color if a category filter is active.
   c. Assign pin position using percentage-based latitude and longitude.
   d. Attach stall metadata using data attributes for tooltip interaction.
7. Render map pins with Font Awesome icons.
8. Include a hidden tooltip component for displaying stall details on interaction.

NOTES:
- This file is a view partial and assumes all required variables are defined prior to inclusion.
- Helpers::escape() is used to prevent XSS vulnerabilities in user-facing text.
- Pin positioning uses percentage values to maintain responsiveness across screen sizes.
- Tooltip behavior and interactivity are handled by external JavaScript logic.
- Category filtering is achieved through URL query parameters.
- Future enhancements may include zooming, clustering pins, or real-time stall updates.
*/

use BuzzarFeed\Utils\Helpers;

$categoryColors = [
    'Beverages' => '#dd452a',
    'Rice Meals' => '#ffae1b',
    'Snacks' => '#ffbda8',
    'Street Food' => '#c6b2e5',
    'Fast Food' => '#4caf50',
    'Pastries' => '#fee717',
    'Others' => '#9e9e9e',
];

// Default color if no match found
$defaultPinColor = '#ed6027';
?>
<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Where the Flavor Lives</h1>
        <p>Locate stalls, filter by food type, and never miss a hidden gem at "the bazaar"</p>
    </section>

    <!-- Category Filters -->
    <section class="filters-section" id="map-view">
        <div class="category-filters">
            <a href="?category=all#map-view"
                class="filter-btn <?= empty($category) || $category === 'all' ? 'active' : '' ?>">
                <span class="filter-icon">🍽️</span>
                All stalls
            </a>
            <?php
            $categoryIcons = [
                'All Stalls' => '🍽️',
                'Beverages' => '🥤',
                'Rice Meals' => '🍛',
                'Snacks' => '🍿',
                'Street Food' => '🌮',
                'Fast Food' => '🍔',
                'Pastries' => '🥐',
                'Others' => '🍴'
            ];
            foreach ($allCategories as $cat):
                if ($cat === 'All stalls')
                    continue;
                $icon = $categoryIcons[$cat] ?? '🍴';
                ?>
                <a href="?category=<?= urlencode($cat) ?>#map-view"
                    class="filter-btn <?= $category === $cat ? 'active' : '' ?>">
                    <span class="filter-icon"><?= $icon ?></span>
                    <?= Helpers::escape($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="map-section">
        <div class="map-container-wrapper">
            <h2 class="map-title">Map</h2>
            <div class="map-container" id="mapContainer">
                <img src="<?= IMAGES_URL ?>/maps.png" alt="BGC Night Market Map" class="map-image" id="mapImage">

                <?php foreach ($stallsWithLocation as $stall): ?>
                    <?php
                    // 3. DETERMINE PIN COLOR
                    // Default to the first category of the stall
                    $displayCat = $stall['categories'][0] ?? 'Others';

                    // If a specific filter is active and the stall belongs to it, force that color
                    // (This ensures that if you filter by "Snacks", the pin turns the "Snacks" color)
                    if (!empty($category) && $category !== 'all' && in_array($category, $stall['categories'])) {
                        $displayCat = $category;
                    }

                    // Get the hex code
                    $pinColor = $categoryColors[$displayCat] ?? $defaultPinColor;
                    ?>

                    <div class="map-pin"
                        style="left: <?= $stall['latitude'] ?>%; top: <?= $stall['longitude'] ?>%; color: <?= $pinColor ?>;"
                        data-stall-id="<?= $stall['id'] ?>" data-stall-name="<?= Helpers::escape($stall['name']) ?>"
                        data-stall-desc="<?= Helpers::escape(substr($stall['description'], 0, 100)) ?>"
                        data-stall-rating="<?= $stall['rating'] ?>"
                        data-stall-categories="<?= Helpers::escape(implode(', ', array_slice($stall['categories'], 0, 2))) ?>">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                <?php endforeach; ?>

                <div class="stall-tooltip hidden" id="stallTooltip">
                    <h4 class="tooltip-name"></h4>
                    <div class="tooltip-categories"></div>
                    <div class="tooltip-rating"></div>
                    <p class="tooltip-desc"></p>
                    <button class="tooltip-view-btn" onclick="viewStall()">View Stall</button>
                </div>
            </div>
        </div>
    </section>
</main>