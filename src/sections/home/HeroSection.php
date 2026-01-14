<?php
/*
PROGRAM NAME: Hero Section Component (HeroSection.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed Home Sections.
It is responsible for rendering the reusable hero section on the homepage.
It depends on BaseComponent, Button, and Helpers utilities to construct and safely render HTML content.

DATE CREATED: Decemeber 2, 2025
LAST MODIFIED: Decemeber 2, 2025

PURPOSE:
The purpose of this program is to display a visually appealing hero section with a title, description, and call-to-action button.
It showcases the BGC Night Market theme and can be reused with custom content.
The hero also includes a polaroid-style image arrangement for visual interest.

DATA STRUCTURES:
- $title (string): Hero section title, supports HTML for highlights.
- $description (string): Short descriptive text for the hero section.
- $ctaText (string|null): Text for the call-to-action button.
- $ctaLink (string|null): Link target for the call-to-action button.
- Button instances are used for CTA functionality.
- Helpers: Provides utility functions such as HTML escaping.

ALGORITHM / LOGIC:
1. initialize():
   - Loads component properties ($title, $description, $ctaText, $ctaLink).
   - Sets default values if none are provided.
2. render():
   - Creates a Button instance for the CTA using the passed or default properties.
   - Constructs the hero section HTML with:
     - Title and description.
     - CTA button.
     - Polaroid image container with three placeholder images.
   - Escapes the description to prevent XSS while allowing HTML in the title.
   - Returns the complete HTML string for rendering.

NOTES:
- Designed with ISO 9241 principles: modularity and reusability.
- Can be reused on different pages by passing custom props for title, description, and CTA.
- The polaroid images can be replaced dynamically in future enhancements.
- Ensures safe rendering of user-provided content via Helpers::escape().
*/

namespace BuzzarFeed\Sections\Home;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Components\Common\Button;
use BuzzarFeed\Utils\Helpers;

class HeroSection extends BaseComponent {
    
    /**
     * @var string Hero title
     */
    protected string $title = '';
    
    /**
     * @var string Hero description
     */
    protected string $description = '';
    
    /**
     * @var string|null CTA button text
     */
    protected ?string $ctaText = null;
    
    /**
     * @var string|null CTA button link
     */
    protected ?string $ctaLink = null;
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->title = $this->prop('title', 'Discover the Flavors<br>of <span class="highlight-orange">BGC Night Market</span>');
        $this->description = $this->prop('description', 'Taste. Try. Savor. Explore top stalls, menus, and honest reviews from fellow food lovers — at BGC Night Market.');
        $this->ctaText = $this->prop('ctaText', 'Discover Stalls');
        $this->ctaLink = $this->prop('ctaLink', '#featured-stalls');
    }
    
    /**
     * Render the section
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $button = new Button([
            'text' => $this->ctaText,
            'href' => $this->ctaLink,
            'variant' => Button::VARIANT_PRIMARY,
            'class' => 'btn-discover',
            'icon' => 'fas fa-utensils'
        ]);
        
        return '
        <section class="hero-section">
            <div class="container hero-container">
                <div class="hero-content">
                    <h1 class="hero-title">' . $this->title . '</h1>
                    <p class="hero-description">' . Helpers::escape($this->description) . '</p>
                    ' . $button->render() . '
                </div>
                <div class="hero-image">
                    <div class="polaroid-container">
                        <div class="polaroid polaroid-1">
                            <div class="polaroid-img"></div>
                        </div>
                        <div class="polaroid polaroid-2">
                            <div class="polaroid-img"></div>
                        </div>
                        <div class="polaroid polaroid-3">
                            <div class="polaroid-img"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    }
}