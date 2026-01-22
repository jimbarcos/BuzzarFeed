<?php
/*
PROGRAM NAME: Review Management API Controller (ReviewController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all review-related API endpoints including review creation, retrieval, updating,
deletion, reactions (likes/dislikes), and reporting of inappropriate reviews.
The ReviewController works with the ReviewService to manage user-generated reviews and feedback
for food stalls, ensuring proper authorization and maintaining review integrity.

This controller serves users submitting and managing their reviews, as well as those browsing
reviews to make informed decisions about food stalls.

DATE CREATED: January 23, 2026
LAST MODIFIED: January 23, 2026

PURPOSE:
The purpose of this program is to provide API endpoints for review management in the BuzzarFeed system.
It enables users to share their experiences through reviews and ratings, interact with other reviews
through reactions, and report inappropriate content. By centralizing review operations, this controller
ensures consistent moderation, proper ownership verification, and authentic user feedback.

DATA STRUCTURES:
- $reviewService (ReviewService): Service instance for review operations.
- Review data:
  - review_id, stall_id, user_id, rating (1-5), comment, created_at, updated_at (mixed).
  - helpful_count, unhelpful_count (int): Reaction tallies.
- Reaction data:
  - review_id, user_id, reaction_type ('helpful' or 'unhelpful').
- Report data:
  - review_id, reported_by (user_id), reason, details, status.
- Query parameters:
  - stall_id (int): Filter reviews by stall.
  - user_id (int): Filter reviews by user.
  - page, limit (int): Pagination parameters.

ALGORITHM / LOGIC:
1. Initialize ReviewService in constructor.
2. Route requests based on HTTP method and action:
   a. GET:
      - /reviews → get reviews (filtered by stall or user).
      - /reviews/{id} → get specific review.
   b. POST:
      - /reviews → create new review.
      - /reviews/react → add reaction to review.
      - /reviews/report → report review.
   c. PUT:
      - /reviews/{id} → update review.
   d. DELETE:
      - /reviews/{id} → delete review.
3. Get reviews:
   a. Extract filter parameters (stall_id, user_id).
   b. Extract pagination parameters.
   c. Call appropriate ReviewService method based on filters.
   d. Return paginated review list.
4. Get single review:
   a. Call ReviewService to retrieve review by ID.
   b. Return review data or 404 if not found.
5. Create review:
   a. Require authentication.
   b. Validate required fields (stall_id, rating, comment).
   c. Add current user ID to review data.
   d. Call ReviewService to create review.
   e. Return created review or error.
6. Update review:
   a. Require authentication.
   b. Verify user owns the review.
   c. Call ReviewService to update review.
   d. Return updated review or error.
7. Delete review:
   a. Require authentication.
   b. Verify user owns the review or is admin.
   c. Call ReviewService to delete review.
   d. Return success or error.
8. React to review:
   a. Require authentication.
   b. Validate review_id and reaction_type.
   c. Call ReviewService to add/update reaction.
   d. Toggle reaction if user already reacted.
   e. Return updated reaction counts.
9. Report review:
   a. Require authentication.
   b. Validate review_id and reason.
   c. Call ReviewService to create report.
   d. Return success confirmation.

NOTES:
- Reviews can only be modified by their authors.
- Review deletion is allowed for authors and administrators.
- Users can only submit one review per stall.
- Reactions are toggleable (clicking helpful again removes the reaction).
- Review reports are queued for admin review.
- Rating validation ensures values are between 1 and 5.
- Pagination helps manage large review sets.
- Future enhancements may include:
  - Review image attachments
  - Review helpful sorting
  - Verified purchase badges
  - Review moderation workflow
  - Review editing history
  - Sentiment analysis
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\ReviewService;

class ReviewController extends BaseController
{
    private $reviewService;
    
    public function __construct()
    {
        parent::__construct();
        $this->reviewService = new ReviewService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getReview($id);
                } else {
                    $this->getReviews();
                }
                break;
            case 'POST':
                if ($action === 'react') {
                    $this->reactToReview();
                } elseif ($action === 'report') {
                    $this->reportReview();
                } else {
                    $this->createReview();
                }
                break;
            case 'PUT':
                if ($id) {
                    $this->updateReview($id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteReview($id);
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getReviews()
    {
        $stallId = $_GET['stall_id'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        
        if ($stallId) {
            $reviews = $this->reviewService->getReviewsByStall($stallId, $page, $limit);
            $total = $this->reviewService->countReviewsByStall($stallId);
        } elseif ($userId) {
            $reviews = $this->reviewService->getReviewsByUser($userId, $page, $limit);
            $total = $this->reviewService->countReviewsByUser($userId);
        } else {
            $reviews = $this->reviewService->getAllReviews($page, $limit);
            $total = $this->reviewService->countAllReviews();
        }
        
        ApiResponse::paginated($reviews, $total, $page, $limit);
    }
    
    private function getReview($id)
    {
        $review = $this->reviewService->getReviewById($id);
        
        if (!$review) {
            ApiResponse::error('Review not found', 404);
        }
        
        ApiResponse::success($review);
    }
    
    private function createReview()
    {
        $this->requireAuth();
        $this->validateRequired(['stall_id', 'rating', 'comment']);
        
        $data = $this->requestBody;
        $data['user_id'] = $this->getCurrentUserId();
        
        $result = $this->reviewService->createReview($data);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function updateReview($id)
    {
        $this->requireAuth();
        
        $result = $this->reviewService->updateReview($id, $this->getCurrentUserId(), $this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function deleteReview($id)
    {
        $this->requireAuth();
        
        $result = $this->reviewService->deleteReview($id, $this->getCurrentUserId());
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function reactToReview()
    {
        $this->requireAuth();
        $this->validateRequired(['review_id', 'reaction_type']);
        
        $result = $this->reviewService->addReaction(
            $this->requestBody['review_id'],
            $this->getCurrentUserId(),
            $this->requestBody['reaction_type']
        );
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function reportReview()
    {
        $this->requireAuth();
        $this->validateRequired(['review_id', 'reason']);
        
        $result = $this->reviewService->reportReview(
            $this->requestBody['review_id'],
            $this->getCurrentUserId(),
            $this->requestBody['reason'],
            $this->requestBody['details'] ?? ''
        );
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
