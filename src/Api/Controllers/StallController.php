<?php
/*
PROGRAM NAME: Stall Management API Controller (StallController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all food stall-related API endpoints including stall retrieval, search, filtering,
menu access, review access, and stall management operations (create, update, delete).
The StallController works with the StallService to perform operations on food stall data
and provides public access to stall information while restricting modification operations
to authenticated users and administrators.

This controller serves both public users browsing stalls and vendors managing their stall listings.

DATE CREATED: January 23, 2026
LAST MODIFIED: January 23, 2026

PURPOSE:
The purpose of this program is to provide API endpoints for food stall discovery and management.
It enables users to search and filter stalls, view detailed stall information including menus
and reviews, and allows stall owners to create and update their listings. By centralizing stall
operations, this controller ensures consistent data access patterns and proper authorization.

DATA STRUCTURES:
- $stallService (StallService): Service instance for stall operations.
- Stall data:
  - stall_id, stall_name, description, location, category, status, owner_id (mixed).
  - operating_hours, contact_info, images (arrays/strings).
- Menu items:
  - item_id, item_name, price, description, category (mixed).
- Search/filter parameters:
  - search (string): Search query for stall names/descriptions.
  - category (string): Filter by food category.
  - page, limit (int): Pagination parameters.
- Review data: Array of review objects for a specific stall.

ALGORITHM / LOGIC:
1. Initialize StallService in constructor.
2. Route requests based on HTTP method and sub-resource:
   a. GET:
      - /stalls → get paginated, filtered list of stalls.
      - /stalls/{id} → get specific stall details.
      - /stalls/{id}/menu → get menu items for stall.
      - /stalls/{id}/reviews → get reviews for stall.
   b. POST:
      - /stalls → create new stall (requires authentication).
   c. PUT:
      - /stalls/{id} → update stall (requires authentication).
   d. DELETE:
      - /stalls/{id} → delete stall (requires admin).
3. Get stalls:
   a. Extract search, category, and pagination parameters.
   b. Call StallService to search and filter stalls.
   c. Count total matching stalls.
   d. Return paginated response.
4. Get single stall:
   a. Call StallService to retrieve stall by ID.
   b. Return stall data or 404 if not found.
5. Get stall menu:
   a. Call StallService to retrieve menu items.
   b. Return menu array.
6. Get stall reviews:
   a. Extract pagination parameters.
   b. Call StallService to retrieve reviews.
   c. Return paginated review list.
7. Create stall:
   a. Require authentication.
   b. Validate required fields (name, description, location).
   c. Call StallService to create stall.
   d. Return created stall data or error.
8. Update stall:
   a. Require authentication.
   b. Verify user owns the stall or is admin.
   c. Call StallService to update stall.
   d. Return updated data or error.
9. Delete stall:
   a. Require admin privileges.
   b. Call StallService to delete stall.
   c. Return success or error.

NOTES:
- Stall browsing is public and does not require authentication.
- Creating and updating stalls requires authentication.
- Deleting stalls is restricted to administrators.
- Search supports full-text search across stall names and descriptions.
- Category filtering enables food type-based discovery.
- Menu items are read-only through this controller.
- Review access is provided but review management is in ReviewController.
- Future enhancements may include:
  - Geolocation-based search
  - Advanced filtering (price range, ratings, operating hours)
  - Stall image upload
  - Menu item management endpoints
  - Stall analytics and statistics
  - Favorite/bookmark functionality
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\StallService;

class StallController extends BaseController
{
    private $stallService;
    
    public function __construct()
    {
        parent::__construct();
        $this->stallService = new StallService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id && $action === 'menu') {
                    $this->getMenu($id);
                } elseif ($id && $action === 'reviews') {
                    $this->getReviews($id);
                } elseif ($id) {
                    $this->getStall($id);
                } else {
                    $this->getStalls();
                }
                break;
            case 'POST':
                $this->createStall();
                break;
            case 'PUT':
                if ($id) {
                    $this->updateStall($id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteStall($id);
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getStalls()
    {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 12);
        
        $stalls = $this->stallService->searchStalls($search, $category, $page, $limit);
        $total = $this->stallService->countStalls($search, $category);
        
        ApiResponse::paginated($stalls, $total, $page, $limit);
    }
    
    private function getStall($id)
    {
        $stall = $this->stallService->getStallById($id);
        
        if (!$stall) {
            ApiResponse::error('Stall not found', 404);
        }
        
        ApiResponse::success($stall);
    }
    
    private function getMenu($stallId)
    {
        $menu = $this->stallService->getMenuItems($stallId);
        ApiResponse::success($menu);
    }
    
    private function getReviews($stallId)
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        
        $reviews = $this->stallService->getReviews($stallId, $page, $limit);
        $total = $this->stallService->countReviews($stallId);
        
        ApiResponse::paginated($reviews, $total, $page, $limit);
    }
    
    private function createStall()
    {
        $this->requireAuth();
        $this->validateRequired(['name', 'description', 'location']);
        
        $result = $this->stallService->createStall($this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function updateStall($id)
    {
        $this->requireAuth();
        
        $result = $this->stallService->updateStall($id, $this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function deleteStall($id)
    {
        $this->requireAdmin();
        
        $result = $this->stallService->deleteStall($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
