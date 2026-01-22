<?php
/*
PROGRAM NAME: Hero Section Component (HeroSection.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It is a reusable hero section component primarily used on the Home page to introduce the platform and highlight the BGC Night Market experience.
The component follows a component-based architecture and extends the BaseComponent class, ensuring modularity, consistency, and reusability across the system.
It integrates reusable UI components such as buttons and utilizes helper utilities for safe rendering of dynamic content.

DATE CREATED: November 23, 2025
LAST MODIFIED: November 28, 2025

PURPOSE:
The purpose of this program is to render a visually engaging hero section that serves as the main entry point of the homepage.
It presents a prominent title, descriptive tagline, and a call-to-action button that encourages users to explore featured food stalls.
The hero section enhances first impressions, guides user navigation, and establishes the overall theme of the BuzzarFeed platform.

DATA STRUCTURES:
- $title (string): The main hero heading, supporting HTML for styled highlights.
- $description (string): Short descriptive text explaining the platform’s purpose.
- $ctaText (string|null): Text displayed on the call-to-action button.
- $ctaLink (string|null): URL or anchor link triggered by the CTA button.
- Button (component): Reusable button component used for the CTA.
- Helpers (class): Provides escape() for safe HTML output.

ALGORITHM / LOGIC:
1. Initialize the component and load properties using default fallback values.
2. Set the hero title, description, and CTA button configuration.
3. Create a reusable Button instance using provided properties.
4. Render the hero section layout:
   a. Display the hero title and description.
   b. Render the CTA button.
   c. Render decorative hero imagery elements.
5. Return the fully composed HTML output.

NOTES:
- This component does not fetch or manage data; all values are injected via props.
- Helpers::escape() is applied to user-facing text where necessary to prevent XSS.
- The title supports inline HTML for highlighted text styling.
- Visual elements (polaroid images) are styled via CSS and contain no business logic.
- Future enhancements may include background animations, dynamic images, or personalized CTA behavior.
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
            'class' => 'btn-discover'
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
