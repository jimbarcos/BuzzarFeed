<?php
/*
PROGRAM NAME: Featured Stalls Section Component (FeaturedStallsSection.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It is a reusable home page component designed to highlight selected or recommended food stalls within the BGC Night Market.
The component extends the BaseComponent class and follows a component-based architecture to ensure modularity, consistency, and ease of reuse across the system.
It integrates common UI components such as buttons and cards, and relies on helper utilities for secure content rendering.

DATE CREATED: November 23, 2025
LAST MODIFIED: November 29, 2025

PURPOSE:
The purpose of this program is to display a curated set of featured food stalls in a visually engaging grid layout.
It provides users with quick access to key stall information such as name, operating hours, description, and imagery.
The section also includes a call-to-action that allows users to browse all available stalls, encouraging deeper exploration of the platform.

DATA STRUCTURES:
- $stalls (array of associative arrays): Collection of featured stall data containing:
  - id
  - name
  - image (logo path)
  - description
  - hours
- $title (string): Section title displayed at the top of the component.
- $location (string): Location subtitle indicating the market area.
- Button (component): Used for "Browse" and "View details" actions.
- Helpers (class): Provides escape() for secure HTML output.

ALGORITHM / LOGIC:
1. Initialize the component and retrieve props with default fallback values.
2. Set the section title and market location.
3. Create a reusable "Browse" button for navigation to the stalls listing page.
4. Render the section header including title, location, and browse button.
5. Check if featured stalls data is empty:
   a. If empty, render placeholder stall cards.
   b. If not empty, loop through and render each featured stall card.
6. For each stall card:
   a. Determine the correct image source or display a placeholder.
   b. Render stall name, operating hours, and short description.
   c. Render a "View details" button linking to the stall detail page.
7. Return the composed HTML output.

NOTES:
- This component does not perform database queries or API calls.
- All stall data is injected via component props.
- Helpers::escape() is used to prevent XSS vulnerabilities.
- Image paths are resolved using BASE_URL and fallback placeholders when unavailable.
- Debug logging is present to assist with image path validation during development.
- Future enhancements may include carousel support, dynamic ranking, or lazy-loaded images.
*/

namespace BuzzarFeed\Sections\Home;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Components\Common\Button;
use BuzzarFeed\Components\Common\Card;
use BuzzarFeed\Utils\Helpers;

class FeaturedStallsSection extends BaseComponent {

    /**
     * @var array Featured stalls data
     */
    protected array $stalls = [];

    /**
     * @var string Section title
     */
    protected string $title = '';

    /**
     * @var string Section location
     */
    protected string $location = '';

    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();

        $this->stalls = $this->prop('stalls', []);
        $this->title = $this->prop('title', '<span class="highlight-green">Explore</span> featured<br>stalls');
        $this->location = $this->prop('location', 'Terra 28th, 28th St. corner 7th Ave. BGC');
    }

    /**
     * Render the section
     *
     * @return string Rendered HTML
     */
    public function render(): string {
        $browseButton = new Button([
            'text' => 'Browse',
            'href' => 'stalls.php',
            'variant' => Button::VARIANT_SECONDARY,
            'class' => 'btn-browse'
        ]);

        $html = '
        <section id="featured-stalls" class="featured-stalls">
            <div class="container">
                <div class="section-header-stalls">
                    <h2 class="section-title">' . $this->title . '</h2>
                    <p class="section-subtitle">
                        <i class="fas fa-map-marker-alt"></i> ' . Helpers::escape($this->location) . '
                    </p>
                    ' . $browseButton->render() . '
                </div>
                <div class="stalls-grid">';

        // Render stall cards
        if (empty($this->stalls)) {
            // Default placeholder stalls
            $html .= $this->renderPlaceholderStalls();
        } else {
            foreach ($this->stalls as $stall) {
                $html .= $this->renderStallCard($stall);
            }
        }

        $html .= '
                </div>
            </div>
        </section>';

        return $html;
    }

    /**
     * Render a stall card
     *
     * @param array $stall Stall data
     * @return string Rendered HTML
     */
    protected function renderStallCard(array $stall): string {
        $viewButton = new Button([
            'text' => 'View details',
            'href' => 'stall-detail.php?id=' . ($stall['id'] ?? ''),
            'class' => 'stall-card-action'
        ]);

        // Determine image source - use image field (which maps from logo_path)
        $imageHtml = '';
        $imagePath = $stall['image'] ?? null;
        
        // Debug: log what we're getting
        error_log("Featured Stall: " . ($stall['name'] ?? 'Unknown') . " | Image Path: " . var_export($imagePath, true));
        
        if (!empty($imagePath)) {
            // Prepend BASE_URL to the logo path from database
            $fullImagePath = BASE_URL . $imagePath;
            error_log("Full Image Path: " . $fullImagePath);
            $imageHtml = '<img src="' . Helpers::escape($fullImagePath) . '" alt="' . Helpers::escape($stall['name']) . '" class="stall-image" style="width: 100%; height: 100%; object-fit: cover; background: #f0f0f0;">';
        } else {
            // No logo available - show a neutral placeholder
            error_log("No image path for stall: " . ($stall['name'] ?? 'Unknown'));
            $imageHtml = '<div style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;"><i class="fas fa-store" style="font-size: 3rem;"></i></div>';
        }

        return '
        <article class="stall-card">
            <div class="stall-card-header">
                ' . $imageHtml . '
            </div>
            <div class="stall-card-overlay">
                <h3 class="stall-label-name">' . Helpers::escape($stall['name'] ?? '') . '</h3>
                <p class="stall-label-hours">
                    <i class="far fa-clock"></i> ' . Helpers::escape($stall['hours'] ?? '') . '
                </p>
                <p class="stall-label-description">
                    ' . Helpers::escape($stall['description'] ?? '') . '
                </p>
            </div>
            ' . $viewButton->render() . '
        </article>';
    }

    
}
