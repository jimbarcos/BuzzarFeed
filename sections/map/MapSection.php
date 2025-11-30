<?php
// Map page sections partial

use BuzzarFeed\Utils\Helpers;
?>
<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Where the Flavor Lives</h1>
        <p>Locate stalls, filter by food type, and never miss a hidden gem at "the bazaar"</p>
    </section>

    <!-- Category Filters -->
    <section class="filters-section">
        <div class="category-filters">
            <a href="?category=all" class="filter-btn <?= empty($category) || $category === 'all' ? 'active' : '' ?>">
                All Stalls
            </a>
            <?php
            // category colors may be defined in map.php
            if (!isset($categoryColors)) {
                $categoryColors = [
                    'Beverages' => '#DD452A',
                    'Rice Meals' => '#FFAE1B',
                    'Snacks' => '#FFBDA8',
                    'Street Food' => '#C6B2E5',
                    'Fast Food' => '#4CAF50',
                    'Pastries' => '#fee717ff',
                    'Others' => '#9E9E9E',
                ];
            }

            foreach ($allCategories as $cat):
                if ($cat === 'All stalls')
                    continue;
                ?>
                <a href="?category=<?= urlencode($cat) ?>" class="filter-btn <?= $category === $cat ? 'active' : '' ?>">
                    <?php if (!empty($categoryColors[$cat])): ?>
                        <span class="filter-icon"></span>
                    <?php endif; ?>
                    <?= Helpers::escape($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="map-section">
        <div class="map-container-wrapper">
            <div class="map-container" id="mapContainer">
                <img src="<?= IMAGES_URL ?>/maps.png" alt="BGC Night Market Map" class="map-image" id="mapImage">

                <?php foreach ($stallsWithLocation as $stall): ?>
                    <?php
                    // Default: Use the first category
                    $primaryCategory = !empty($stall['categories']) && is_array($stall['categories'])
                        ? $stall['categories'][0]
                        : '';

                    $displayCategory = $primaryCategory;

                    if (!empty($category) && $category !== 'all' && is_array($stall['categories'])) {
                        if (in_array($category, $stall['categories'])) {
                            $displayCategory = $category;
                        }
                    }
                    ?>
                    <div class="map-pin" style="left: <?= $stall['latitude'] ?>%; top: <?= $stall['longitude'] ?>%;"
                        data-stall-id="<?= $stall['id'] ?>" data-stall-name="<?= Helpers::escape($stall['name']) ?>"
                        data-stall-desc="<?= Helpers::escape(substr($stall['description'], 0, 100)) ?>"
                        data-stall-rating="<?= $stall['rating'] ?>" data-category="<?= Helpers::escape($displayCategory) ?>"
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