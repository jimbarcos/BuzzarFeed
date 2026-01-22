<?php
/*
PROGRAM NAME: Review Service (ReviewService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides business logic for managing user reviews of food stalls.
The ReviewService class interacts with the Database utility to retrieve, format, and display reviews.
It is typically used by controllers, API endpoints, and frontend components to fetch recent reviews, stall-specific reviews, and formatted review data for presentation.

DATE CREATED: Novemeber 29, 2025
LAST MODIFIED: Decemeber 14, 2025

PURPOSE:
The purpose of this program is to centralize review retrieval and formatting logic into a reusable service.
It ensures consistent presentation of review information across the system, handles anonymous reviews, and formats numeric ratings for display.
This service abstracts database queries and provides structured data for frontend consumption.

DATA STRUCTURES:
- $db (Database): Database instance used for executing queries.
- $review (array): Single review record fetched from the database.
- $reviews (array): Collection of review records returned by queries.
- Formatted review array:
  - 'id' (int): Review ID.
  - 'reviewer' (string): Reviewer name or 'Anonymous'.
  - 'text' (string): Review comment text.
  - 'rating' (float): Numeric rating formatted to 1 decimal place.
  - 'stall_name' (string): Name of the reviewed stall.
  - 'stall_id' (int|null): Stall ID.
  - 'stall_logo' (string|null): Path to stall logo.
  - 'created_at' (string): Timestamp of review creation.

ALGORITHM / LOGIC:
1. Initialize Database instance on service construction.
2. Retrieve recent reviews for homepage:
   a. Join reviews with users and food_stalls tables.
   b. Filter for active stalls.
   c. Limit results to the specified number.
   d. Format each review for display.
3. Retrieve reviews for a specific stall:
   a. Join reviews with users table.
   b. Filter reviews by the given stall ID.
   c. Format each review for display.
4. Format review data:
   a. Mask reviewer name if the review is marked as anonymous.
   b. Ensure comment text is not null.
   c. Format rating as a float with one decimal place.
   d. Include stall information if available.

NOTES:
- All public methods ensure consistent and safe data formatting for frontend consumption.
- Anonymous reviews are supported to protect user privacy.
- Rating values are always formatted for uniform display.
- Future enhancements may include pagination, review sorting, filtering by rating, or moderation features.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;

class ReviewService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get recent reviews for homepage
     * 
     * @param int $limit
     * @return array
     */
    public function getRecentReviews(int $limit = 6): array
    {
        $query = "SELECT 
                    r.review_id,
                    r.rating,
                    r.comment,
                    r.created_at,
                    r.is_anonymous,
                    u.name as reviewer_name,
                    fs.name as stall_name,
                    fs.stall_id,
                    fs.logo_path as stall_logo
                  FROM reviews r
                  INNER JOIN users u ON r.user_id = u.user_id
                  INNER JOIN food_stalls fs ON r.stall_id = fs.stall_id
                  WHERE fs.is_active = 1
                  ORDER BY r.created_at DESC
                  LIMIT ?";
        
        $reviews = $this->db->query($query, [$limit]);
        
        return array_map(function($review) {
            return $this->formatReviewData($review);
        }, $reviews);
    }
    
    /**
     * Get reviews for a specific stall
     * 
     * @param int $stallId
     * @return array
     */
    public function getStallReviews(int $stallId): array
    {
        $query = "SELECT 
                    r.review_id,
                    r.rating,
                    r.comment,
                    r.created_at,
                    r.is_anonymous,
                    u.name as reviewer_name
                  FROM reviews r
                  INNER JOIN users u ON r.user_id = u.user_id
                  WHERE r.stall_id = ?
                  ORDER BY r.created_at DESC";
        
        $reviews = $this->db->query($query, [$stallId]);
        
        return array_map(function($review) {
            return $this->formatReviewData($review);
        }, $reviews);
    }
    
    /**
     * Format review data for display
     * 
     * @param array $review
     * @return array
     */
    private function formatReviewData(array $review): array
    {
        return [
            'id' => $review['review_id'],
            'reviewer' => $review['is_anonymous'] ? 'Anonymous' : $review['reviewer_name'],
            'text' => $review['comment'] ?? '',
            'rating' => number_format((float)$review['rating'], 1),
            'stall_name' => $review['stall_name'] ?? '',
            'stall_id' => $review['stall_id'] ?? null,
            'stall_logo' => $review['stall_logo'] ?? null,
            'created_at' => $review['created_at'] ?? '',
        ];
    }
}
