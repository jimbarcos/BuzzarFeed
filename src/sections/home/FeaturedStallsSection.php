<?php
/*
PROGRAM NAME: Featured Stalls Section Component (FeaturedStallsSection.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed Home Sections.
It is responsible for rendering the reusable featured stalls section on the homepage.
It depends on BaseComponent, Button, Card, and Helpers utilities to construct and safely render HTML content.

DATE CREATED: Decemeber 2, 2025
LAST MODIFIED: Decemeber 2, 2025

PURPOSE:
The purpose of this program is to display a grid of featured food stalls with relevant information including:
- Stall name
- Operating hours
- Description
- Action buttons for viewing details
It also provides a call-to-action button to browse all stalls.

DATA STRUCTURES:
- $stalls (array): Array of featured stall data (id, name, hours, description).
- $title (string): Section title, supports HTML for highlights.
- $location (string): Location of the featured stalls, displayed with a map icon.
- Button instances are used for CTA and individual stall actions.
- Helpers: Provides utility functions such as HTML escaping.

ALGORITHM / LOGIC:
1. initialize():
   - Loads component properties ($stalls, $title, $location) from props.
   - Sets default values if none are provided.
2. render():
   - Creates a Browse Button instance for the section CTA.
   - Constructs the main section HTML:
     - Section header with title, location, and Browse button.
     - Grid of stall cards.
   - If no stalls are provided, renders default placeholder stalls.
3. renderStallCard(array $stall):
   - Creates a Button instance for "View details".
   - Builds the HTML for an individual stall card with overlay information.
4. renderPlaceholderStalls():
   - Provides default stall cards to display when $stalls is empty.
   - Uses renderStallCard() for consistency.

NOTES:
- Designed with ISO 9241 principles: modularity and reusability.
- Can be reused on different pages or sections by passing custom stall data, titles, and location.
- Ensures safe rendering of user-provided content via Helpers::escape().
- Placeholder stalls provide a default look if no real data is available.
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
            'class' => 'btn-browse',
            'icon' => 'fas fa-arrow-up-right-from-square'
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
            'href' => 'stall-details.php?id=' . ($stall['id'] ?? ''),
            'variant' => Button::VARIANT_SUCCESS,
            'class' => 'stall-card-action'
        ]);
        
        return '
        <article class="stall-card">
            <div class="stall-card-header">
                <div class="stall-image-placeholder"></div>
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
    
    /**
     * Render placeholder stalls
     * 
     * @return string Rendered HTML
     */
    protected function renderPlaceholderStalls(): string {
        $placeholders = [
            [
                'id' => 1,
                'name' => 'Kape Kuripot',
                'hours' => 'Monday to Saturday 09:00 to 18:00',
                'description' => 'Lorem ipsum dolor sit amet. Est magnam possimus in odio quis sed assumenda odio quis impedit.'
            ],
            [
                'id' => 2,
                'name' => 'Kape Kuripot',
                'hours' => 'Monday to Saturday 09:00 to 18:00',
                'description' => 'Lorem ipsum dolor sit amet. Est magnam possimus in odio quis sed assumenda odio quis impedit.'
            ]
        ];
        
        $html = '';
        foreach ($placeholders as $stall) {
            $html .= $this->renderStallCard($stall);
        }
        
        return $html;
    }
}