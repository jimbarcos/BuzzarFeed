<?php
/*
PROGRAM NAME: Reviews Section Component (ReviewsSection.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It is a reusable UI component used primarily on the Home page to display user reviews of food stalls.
The component follows a component-based architecture and extends the BaseComponent class to ensure consistency, reusability, and modular rendering across the system.
It integrates with common UI components such as buttons and relies on helper utilities for safe output handling.

DATE CREATED: November 23, 2025
LAST MODIFIED: December 14, 2025

PURPOSE:
The purpose of this program is to render a reviews section that showcases user feedback and ratings for food stalls.
It supports dynamic review data passed through component props and provides a fallback placeholder display when no reviews are available.
This component enhances credibility and user engagement by highlighting real or sample customer experiences.

DATA STRUCTURES:
- $reviews (array): List of review data containing:
  - reviewer (string)
  - text (string)
  - rating (string or float)
  - stall_id (optional integer)
  - stall_name (optional string)
- $title (string): Section title displayed at the top of the reviews section.
- Button (component): Used to render a reusable “Read more” call-to-action button.
- Helpers (class): Provides escape() for secure HTML output.

ALGORITHM / LOGIC:
1. Initialize the component and load props:
   a. Retrieve reviews data.
   b. Set the section title with a default fallback.
2. Render the reviews section container and title.
3. Check if reviews data is empty:
   a. If empty, render placeholder review cards.
   b. If not empty, loop through and render each review card.
4. For each review card:
   a. Determine the correct “Read more” link.
   b. Render a default user avatar.
   c. Display reviewer name and stall name (if available).
   d. Display review text and rating.
   e. Render a reusable button component.
5. Return the fully rendered HTML output.

NOTES:
- This component does not perform data fetching; all data is injected via props.
- Helpers::escape() is used to prevent XSS vulnerabilities.
- Placeholder reviews ensure visual consistency during development or when no data is available.
- The component adheres to ISO 9241 principles for modularity, consistency, and usability.
- Future improvements may include user avatars, pagination, animations, or live review feeds.
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
        $readMoreLink = '#';
        if (!empty($review['stall_id'])) {
            $readMoreLink = 'stall-detail.php?id=' . $review['stall_id'];
        }
        
        $readMoreButton = new Button([
            'text' => 'Read more',
            'href' => $readMoreLink,
            'variant' => Button::VARIANT_OUTLINE,
            'class' => 'review-readmore'
        ]);

        // Display default user avatar (gray circular profile picture)
        $avatarHtml = '<div class="reviewer-avatar" style="background-color: #9ca3af; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 50%; overflow: hidden;">
            <svg viewBox="0 0 24 24" fill="white" style="width: 60%; height: 60%; margin-top: 20%;">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>';

        return '
        <article class="review-card">
            <div class="review-header-row">
                ' . $avatarHtml . '
                <div class="review-stall-info">
                    <h4 class="reviewer-name">' . Helpers::escape($review['reviewer'] ?? '') . '</h4>
                    ' . (!empty($review['stall_name']) ? '<p class="review-stall-name" title="' . Helpers::escape($review['stall_name']) . '"><i class="fas fa-store"></i> ' . Helpers::escape($review['stall_name']) . '</p>' : '') . '
                </div>
            </div>
            <div class="review-content">
                <p class="review-text">"' . Helpers::escape($review['text'] ?? '') . '"</p>
                <div class="review-footer">
                    <div class="review-rating">
                        <span class="rating-value">' . Helpers::escape($review['rating'] ?? '0.0') . '</span>
                        <img src="' . IMAGES_URL . 'star-rating.svg" alt="rating star" class="rating-star">
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
