<?php
/*
PROGRAM NAME: Reviews Section Component (ReviewsSection.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed Home Sections.
It is responsible for rendering a reusable reviews section on the homepage or other pages.
It depends on BaseComponent, Button, and Helpers utilities to construct and safely render HTML content.

DATE CREATED: Decemeber 2, 2025
LAST MODIFIED: Decemeber 2, 2025

PURPOSE:
The purpose of this program is to display user reviews dynamically in a visually appealing card layout.
It provides default placeholder reviews if no actual reviews are passed.
The component ensures safe HTML rendering by escaping user-provided data and supports buttons for further actions (e.g., "Read more").

DATA STRUCTURES:
- $reviews (array): Array of review data objects. Each object contains:
    - reviewer: Name of the reviewer
    - text: Review content
    - rating: Numeric rating (string or float)
- $title (string): Section title displayed above reviews.
- Button instances are used for "Read more" functionality.
- Helpers: Provides utility functions such as HTML escaping.

ALGORITHM / LOGIC:
1. initialize():
   - Loads component properties ($reviews and $title).
   - Sets a default title if none is provided.
2. render():
   - Outputs the main section HTML wrapper.
   - Iterates over $reviews and renders each card using renderReviewCard().
   - If $reviews is empty, calls renderPlaceholderReviews() to render default reviews.
3. renderReviewCard(array $review):
   - Constructs individual review card HTML.
   - Escapes all user-provided data to prevent XSS.
   - Appends a "Read more" button.
4. renderPlaceholderReviews():
   - Generates a set of predefined placeholder reviews.
   - Calls renderReviewCard() for each placeholder review.

NOTES:
- Designed with ISO 9241 principles: modularity and reusability.
- Can be reused in multiple pages by passing different review datasets.
- Placeholder content ensures consistent layout even when no reviews are available.
- Future enhancements may include dynamic links for "Read more", star rating visualization, and integration with live review data sources.
*/

namespace BuzzarFeed\Sections\Home;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Components\Common\Button;
use BuzzarFeed\Utils\Helpers;

class ReviewsSection extends BaseComponent {
    
    /**
     * @var array Reviews data
     */
    protected array $reviews = [];
    
    /**
     * @var string Section title
     */
    protected string $title = '';
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->reviews = $this->prop('reviews', []);
        $this->title = $this->prop('title', 'See what foodies are <span class="highlight-green">raving</span> about...');
    }
    
    /**
     * Render the section
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $html = '
        <section class="reviews-section">
            <div class="container">
                <h2 class="section-title centered">' . $this->title . '</h2>
                <div class="reviews-grid">';
        
        // Render review cards
        if (empty($this->reviews)) {
            // Default placeholder reviews
            $html .= $this->renderPlaceholderReviews();
        } else {
            foreach ($this->reviews as $review) {
                $html .= $this->renderReviewCard($review);
            }
        }
        
        $html .= '
                </div>
            </div>
        </section>';
        
        return $html;
    }
    
    /**
     * Render a review card
     * 
     * @param array $review Review data
     * @return string Rendered HTML
     */
    protected function renderReviewCard(array $review): string {
        $readMoreButton = new Button([
            'text' => 'Read more',
            'href' => '#',
            'variant' => Button::VARIANT_SECONDARY,
            'size' => Button::SIZE_SMALL,
            'icon' => 'fas fa-arrow-right'
        ]);
        
        return '
        <article class="review-card">
            <div class="reviewer-avatar"></div>
            <div class="review-content">
                <h4 class="reviewer-name">' . Helpers::escape($review['reviewer'] ?? '') . '</h4>
                <p class="review-text">"' . Helpers::escape($review['text'] ?? '') . '"</p>
                <div class="review-footer">
                    <div class="review-rating">
                        <span class="rating-value">' . Helpers::escape($review['rating'] ?? '4.9') . '</span>
                        <i class="fas fa-star"></i>
                    </div>
                    ' . $readMoreButton->render() . '
                </div>
            </div>
        </article>';
    }
    
    /**
     * Render placeholder reviews
     * 
     * @return string Rendered HTML
     */
    protected function renderPlaceholderReviews(): string {
        $placeholders = [
            [
                'reviewer' => 'Sirap pochi•',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum.',
                'rating' => '4.9'
            ],
            [
                'reviewer' => 'H think homemade yung patty sa Yunie•',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum.',
                'rating' => '4.9'
            ],
            [
                'reviewer' => 'Sirap pochi•',
                'text' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua et dolore et labore sit ipsum.',
                'rating' => '4.9'
            ]
        ];
        
        $html = '';
        foreach ($placeholders as $review) {
            $html .= $this->renderReviewCard($review);
        }
        
        return $html;
    }
}