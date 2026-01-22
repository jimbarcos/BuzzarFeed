<?php
/*
PROGRAM NAME: Review Helper Utility (ReviewHelper.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides centralized helper functions for managing user reviews and stall ratings within the system.
The ReviewHelper class replaces database-level triggers to ensure compatibility with shared hosting environments such as InfinityFree.
It is used by controllers and services responsible for creating, updating, deleting, moderating, and retrieving reviews, as well as maintaining accurate stall rating statistics.

DATE CREATED: December 2, 2025
LAST MODIFIED: December 2, 2025

PURPOSE:
The purpose of this program is to handle all review-related business logic in the application layer.
It ensures that stall ratings and review counts remain consistent whenever reviews are added, updated, deleted, or moderated.
By moving this logic out of database triggers and into PHP, the system gains better portability, debuggability, and maintainability.

DATA STRUCTURES:
- Database (class): Singleton database handler used for PDO connections.
- PDO / PDOStatement: Used for executing parameterized SQL queries.
- reviews (table):
  - review_id (int): Primary key.
  - stall_id (int): Associated food stall.
  - user_id (int): Reviewer’s user ID.
  - rating (int): Numeric rating (1–5).
  - comment (string): Review text.
  - is_anonymous (bool): Indicates anonymous review.
  - is_hidden (bool): Moderation flag.
  - created_at / updated_at (timestamp): Audit fields.
- food_stalls (table):
  - average_rating (decimal): Computed average rating.
  - total_reviews (int): Count of visible reviews.
- review_moderations (table):
  - review_id (int): Moderated review.
  - moderator_id (int): Moderator user ID.
  - reason (string): Moderation reason.
  - is_hidden (bool): Visibility status.

ALGORITHM / LOGIC:
1. Retrieve a singleton database instance when needed.
2. For rating updates:
   a. Calculate the average rating and total visible reviews for a stall.
   b. Update the corresponding record in the food_stalls table.
3. When adding a review:
   a. Begin a database transaction.
   b. Insert the review record.
   c. Recalculate and update the stall rating.
   d. Commit the transaction.
4. When updating a review:
   a. Retrieve the associated stall ID.
   b. Begin a transaction.
   c. Update the review fields.
   d. Recalculate the stall rating.
   e. Commit the transaction.
5. When deleting a review:
   a. Retrieve the associated stall ID.
   b. Begin a transaction.
   c. Delete the review record.
   d. Recalculate the stall rating.
   e. Commit the transaction.
6. When moderating a review:
   a. Record moderation details in the review_moderations table.
   b. Update the review’s hidden status.
   c. Recalculate the stall rating, excluding hidden reviews.
7. Provide helper queries to:
   a. Check if a user has already reviewed a stall.
   b. Retrieve paginated lists of reviews, optionally including hidden entries.
8. Use transactions to ensure data consistency and rollback on failure.
9. Log errors using error_log() for debugging and monitoring.

NOTES:
- This class replaces MySQL triggers to maintain compatibility with hosting environments that restrict trigger usage.
- All write operations use transactions to prevent partial updates and maintain referential consistency.
- Hidden reviews are excluded from rating calculations by default.
- Anonymous reviews mask the reviewer’s name while still linking to a valid user ID.
- Parameterized queries are used throughout to prevent SQL injection.
- This utility centralizes review logic, reducing duplication across controllers.
- Future enhancements may include rating weight adjustments, soft deletes, or caching of frequently accessed review data.
*/

namespace BuzzarFeed\Utils;

use PDO;
use PDOException;

class ReviewHelper {
    
    /**
     * @var Database Database instance
     */
    private static ?Database $db = null;
    
    /**
     * Get database instance
     * 
     * @return Database
     */
    private static function getDb(): Database {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    /**
     * Update stall rating and review count
     * Replaces trigger: update_stall_rating_after_insert/update/delete
     * 
     * @param int $stallId Stall ID
     * @return bool Success status
     */
    public static function updateStallRating(int $stallId): bool {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Calculate average rating and total reviews
            $query = "
                SELECT 
                    COALESCE(AVG(rating), 0) as avg_rating,
                    COUNT(*) as total_reviews
                FROM reviews 
                WHERE stall_id = :stall_id 
                AND is_hidden = FALSE
            ";
            
            $stmt = $conn->prepare($query);
            $stmt->execute(['stall_id' => $stallId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update food_stalls table
            $updateQuery = "
                UPDATE food_stalls 
                SET 
                    average_rating = :avg_rating,
                    total_reviews = :total_reviews,
                    updated_at = CURRENT_TIMESTAMP
                WHERE stall_id = :stall_id
            ";
            
            $updateStmt = $conn->prepare($updateQuery);
            return $updateStmt->execute([
                'avg_rating' => round($result['avg_rating'], 2),
                'total_reviews' => $result['total_reviews'],
                'stall_id' => $stallId
            ]);
            
        } catch (PDOException $e) {
            error_log("Error updating stall rating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add a review and update stall rating
     * 
     * @param int $stallId Stall ID
     * @param int $userId User ID
     * @param int $rating Rating (1-5)
     * @param string $comment Review comment
     * @param bool $isAnonymous Anonymous review flag
     * @return int|false Review ID on success, false on failure
     */
    public static function addReview(
        int $stallId, 
        int $userId, 
        int $rating, 
        string $comment = '', 
        bool $isAnonymous = false
    ) {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert review
            $query = "
                INSERT INTO reviews (stall_id, user_id, rating, comment, is_anonymous)
                VALUES (:stall_id, :user_id, :rating, :comment, :is_anonymous)
            ";
            
            $stmt = $conn->prepare($query);
            $success = $stmt->execute([
                'stall_id' => $stallId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment,
                'is_anonymous' => $isAnonymous ? 1 : 0
            ]);
            
            if (!$success) {
                $conn->rollBack();
                return false;
            }
            
            $reviewId = (int)$conn->lastInsertId();
            
            // Update stall rating
            self::updateStallRating($stallId);
            
            // Commit transaction
            $conn->commit();
            
            return $reviewId;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error adding review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a review and recalculate stall rating
     * 
     * @param int $reviewId Review ID
     * @param int $rating New rating
     * @param string $comment New comment
     * @return bool Success status
     */
    public static function updateReview(int $reviewId, int $rating, string $comment): bool {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Get stall_id before update
            $getStallQuery = "SELECT stall_id FROM reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($getStallQuery);
            $stmt->execute(['review_id' => $reviewId]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$review) {
                return false;
            }
            
            // Start transaction
            $conn->beginTransaction();
            
            // Update review
            $query = "
                UPDATE reviews 
                SET 
                    rating = :rating,
                    comment = :comment,
                    updated_at = CURRENT_TIMESTAMP
                WHERE review_id = :review_id
            ";
            
            $stmt = $conn->prepare($query);
            $success = $stmt->execute([
                'rating' => $rating,
                'comment' => $comment,
                'review_id' => $reviewId
            ]);
            
            if (!$success) {
                $conn->rollBack();
                return false;
            }
            
            // Update stall rating
            self::updateStallRating($review['stall_id']);
            
            // Commit transaction
            $conn->commit();
            
            return true;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error updating review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a review and update stall rating
     * 
     * @param int $reviewId Review ID
     * @return bool Success status
     */
    public static function deleteReview(int $reviewId): bool {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Get stall_id before delete
            $getStallQuery = "SELECT stall_id FROM reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($getStallQuery);
            $stmt->execute(['review_id' => $reviewId]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$review) {
                return false;
            }
            
            // Start transaction
            $conn->beginTransaction();
            
            // Delete review
            $query = "DELETE FROM reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($query);
            $success = $stmt->execute(['review_id' => $reviewId]);
            
            if (!$success) {
                $conn->rollBack();
                return false;
            }
            
            // Update stall rating
            self::updateStallRating($review['stall_id']);
            
            // Commit transaction
            $conn->commit();
            
            return true;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error deleting review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Moderate a review (hide/show)
     * Replaces trigger: update_review_hidden_status
     * 
     * @param int $reviewId Review ID
     * @param int $moderatorId Moderator user ID
     * @param string $reason Moderation reason
     * @param bool $isHidden Hide the review
     * @return bool Success status
     */
    public static function moderateReview(
        int $reviewId, 
        int $moderatorId, 
        string $reason, 
        bool $isHidden = true
    ): bool {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Get stall_id for rating update
            $getStallQuery = "SELECT stall_id FROM reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($getStallQuery);
            $stmt->execute(['review_id' => $reviewId]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$review) {
                return false;
            }
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert moderation record
            $insertQuery = "
                INSERT INTO review_moderations 
                (review_id, moderator_id, reason, is_hidden)
                VALUES (:review_id, :moderator_id, :reason, :is_hidden)
            ";
            
            $stmt = $conn->prepare($insertQuery);
            $stmt->execute([
                'review_id' => $reviewId,
                'moderator_id' => $moderatorId,
                'reason' => $reason,
                'is_hidden' => $isHidden ? 1 : 0
            ]);
            
            // Update review hidden status
            $updateQuery = "
                UPDATE reviews 
                SET is_hidden = :is_hidden 
                WHERE review_id = :review_id
            ";
            
            $stmt = $conn->prepare($updateQuery);
            $success = $stmt->execute([
                'is_hidden' => $isHidden ? 1 : 0,
                'review_id' => $reviewId
            ]);
            
            if (!$success) {
                $conn->rollBack();
                return false;
            }
            
            // Update stall rating (hidden reviews don't count)
            self::updateStallRating($review['stall_id']);
            
            // Commit transaction
            $conn->commit();
            
            return true;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error moderating review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has already reviewed a stall
     * 
     * @param int $userId User ID
     * @param int $stallId Stall ID
     * @return bool True if user has reviewed
     */
    public static function hasUserReviewed(int $userId, int $stallId): bool {
        try {
            $db = self::getDb();
            $query = "
                SELECT COUNT(*) as count 
                FROM reviews 
                WHERE user_id = :user_id AND stall_id = :stall_id
            ";
            
            $result = $db->querySingle($query, [
                'user_id' => $userId,
                'stall_id' => $stallId
            ]);
            
            return $result && $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error checking user review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get reviews for a stall
     * 
     * @param int $stallId Stall ID
     * @param bool $includeHidden Include hidden reviews
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array Reviews
     */
    public static function getStallReviews(
        int $stallId, 
        bool $includeHidden = false,
        int $limit = 10,
        int $offset = 0
    ): array {
        try {
            $db = self::getDb();
            
            $hiddenClause = $includeHidden ? '' : 'AND r.is_hidden = FALSE';
            
            $query = "
                SELECT 
                    r.*,
                    CASE 
                        WHEN r.is_anonymous = TRUE THEN 'Anonymous'
                        ELSE u.name
                    END as reviewer_name,
                    u.profile_image
                FROM reviews r
                INNER JOIN users u ON r.user_id = u.user_id
                WHERE r.stall_id = :stall_id
                {$hiddenClause}
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $conn = $db->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':stall_id', $stallId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting stall reviews: " . $e->getMessage());
            return [];
        }
    }
}
