<?php
/*
PROGRAM NAME: Explore Stalls Section (explore-stalls.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It functions as a view partial within the Map page, responsible for displaying a curated list of food stalls that users can explore.
The section complements the interactive map by presenting stall information in a card-based layout.
All required data (stall details, categories, ratings, and images) are assumed to be prepared and passed by the parent controller or page.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 14, 2025

PURPOSE:
The purpose of this program is to visually present available food stalls in an organized and user-friendly grid layout.
It allows users to quickly browse stalls, view essential details such as categories, ratings, operating hours, and descriptions, and navigate to individual stall detail pages.
An empty state is shown when no stalls are available to improve user experience.

DATA STRUCTURES:
- $exploreStalls (array of associative arrays): Collection of stalls containing:
  - id
  - name
  - image (optional)
  - description
  - rating (float)
  - reviews (integer)
  - categories (array)
  - hours (string)
- Helpers (class): Provides utility methods such as:
  - escape() for safe HTML output
  - formatCategoryName() for user-friendly category labels

ALGORITHM / LOGIC:
1. Render a visual divider separating this section from the map content.
2. Display the section title ("Explore Food Stalls").
3. Check if $exploreStalls is empty:
   a. If true, display an empty-state message with an icon.
   b. If false, continue rendering stall cards.
4. Loop through each stall in $exploreStalls:
   a. Wrap each stall in a clickable card linking to the stall detail page.
   b. Display the stall image if available; otherwise, show a placeholder icon.
   c. Render up to two category tags per stall.
   d. Display stall name.
   e. Render star-based ratings:
      - Full stars for whole numbers.
      - Half star for decimal ratings.
      - Empty stars up to a maximum of five.
   f. Display numeric rating and review count if available.
   g. Show operating hours.
   h. Display a short stall description.
5. Ensure all user-facing content is safely escaped.

NOTES:
- This file is a presentation-only partial and contains no data-fetching logic.
- Helpers::escape() is consistently applied to prevent XSS vulnerabilities.
- Ratings are visually represented using Font Awesome icons.
- Only the first two categories are displayed to maintain a clean card layout.
- The stall detail navigation relies on query parameters passed via the URL.
- Future enhancements may include pagination, lazy loading, or client-side filtering and sorting.
*/

use BuzzarFeed\Utils\Helpers;
?>
<hr class="section-divider">

<!-- Explore Stalls Section -->
<section class="explore-section">
    <h2 class="explore-title">Explore Food Stalls</h2>

    <?php if (empty($exploreStalls)): ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <p>No stalls available at the moment.</p>
        </div>
    <?php else: ?>
        <div class="explore-grid">
            <?php foreach ($exploreStalls as $stall): ?>
                <a href="stall-detail.php?id=<?= $stall['id'] ?>" class="stall-card">
                    <?php if (!empty($stall['image'])): ?>
                        <img src="<?= BASE_URL . Helpers::escape($stall['image']) ?>" alt="<?= Helpers::escape($stall['name']) ?>"
                            class="stall-image">
                    <?php else: ?>
                        <div class="stall-image-placeholder">
                            <i class="fas fa-utensils"></i>
                        </div>
                    <?php endif; ?>

                    <div class="stall-content">
                        <div class="stall-categories">
                            <?php foreach (array_slice($stall['categories'], 0, 2) as $cat): ?>
                                <span class="category-tag"><?= Helpers::escape(Helpers::formatCategoryName($cat)) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <h3 class="stall-name"><?= Helpers::escape($stall['name']) ?></h3>

                        <div class="stall-rating">
                            <div class="stars">
                                <?php
                                $fullStars = floor($stall['rating']);
                                $hasHalfStar = ($stall['rating'] - $fullStars) >= 0.5;

                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fas fa-star star"></i>';
                                }
                                if ($hasHalfStar) {
                                    echo '<i class="fas fa-star-half-alt star"></i>';
                                }
                                for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++) {
                                    echo '<i class="far fa-star star empty"></i>';
                                }
                                ?>
                            </div>
                            <span class="rating-text">
                                <?= $stall['rating'] > 0 ? number_format($stall['rating'], 1) : 'No ratings' ?>
                                <?php if ($stall['reviews'] > 0): ?>
                                    (<?= $stall['reviews'] ?> Reviews)
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="stall-hours">
                            <i class="far fa-clock"></i>
                            <span><?= Helpers::escape($stall['hours']) ?></span>
                        </div>

                        <p class="stall-description">
                            <?= Helpers::escape($stall['description']) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
