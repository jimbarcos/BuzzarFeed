<?php
/*
PROGRAM NAME: User Management API Controller (UserController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all user-related API endpoints including profile retrieval, profile updates,
password changes, and user management operations.
The UserController works with the UserService to perform CRUD operations on user accounts
and enforces authorization rules to ensure users can only modify their own data unless
they have admin privileges.

This controller serves both regular users managing their own profiles and administrators
managing all user accounts.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide API endpoints for user account management.
It allows users to view and update their profiles, change passwords, and enables administrators
to manage all user accounts. By centralizing user management logic, this controller ensures
consistent data handling and proper authorization enforcement across user operations.

DATA STRUCTURES:
- $userService (UserService): Service instance for user operations.
- User profile data:
  - user_id, email, first_name, last_name, phone, profile_image, user_type_id (mixed).
- Password change request:
  - current_password (string): Current password for verification.
  - new_password (string): New password to set.
- Query parameters:
  - page (int): Page number for pagination.
  - limit (int): Items per page.
  - search (string): Search query for filtering users.
- User list response: Array of user objects with pagination metadata.

ALGORITHM / LOGIC:
1. Initialize UserService in constructor.
2. Route requests based on HTTP method and action:
   a. GET:
      - /users/profile → get current user's profile.
      - /users/{id} → get specific user by ID.
      - /users → get paginated list of all users (admin only).
   b. PUT:
      - /users/profile → update current user's profile.
      - /users/password → change current user's password.
      - /users/{id} → update specific user (admin only).
   c. DELETE:
      - /users/{id} → delete user account (admin only).
3. Get users (admin only):
   a. Require admin authentication.
   b. Extract pagination and search parameters.
   c. Call UserService to retrieve filtered users.
   d. Return paginated response.
4. Get user by ID:
   a. Require authentication.
   b. Call UserService to retrieve user.
   c. Remove sensitive data (password).
   d. Return user data or 404 if not found.
5. Get current user profile:
   a. Require authentication.
   b. Retrieve current user's data.
   c. Remove sensitive data.
   d. Return profile data.
6. Update profile:
   a. Require authentication.
   b. Call UserService to update current user's data.
   c. Return updated profile or error.
7. Change password:
   a. Require authentication.
   b. Validate required fields (current and new password).
   c. Call UserService to verify current password and update.
   d. Return success or error.
8. Update user (admin):
   a. Require admin privileges.
   b. Call UserService to update specified user.
   c. Return updated data or error.
9. Delete user (admin):
   a. Require admin privileges.
   b. Call UserService to delete user account.
   c. Return success or error.

NOTES:
- Authentication is required for all user endpoints.
- Password data is always removed from response objects.
- Regular users can only access and modify their own profiles.
- Administrators can manage all user accounts.
- Password changes require verification of current password.
- User deletion may be prevented if user has dependent resources.
- Pagination is supported for user listing.
- Future enhancements may include:
  - Bulk user operations
  - User export functionality
  - Advanced filtering and sorting
  - User activity history
  - Profile picture upload via API
  - Email change with verification
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\UserService;

class UserController extends BaseController
{
    private $userService;
    
    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($action === 'profile') {
                    $this->getProfile();
                } elseif ($id) {
                    $this->getUser($id);
                } else {
                    $this->getUsers();
                }
                break;
            case 'PUT':
                if ($action === 'profile') {
                    $this->updateProfile();
                } elseif ($action === 'password') {
                    $this->changePassword();
                } elseif ($id) {
                    $this->updateUser($id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteUser($id);
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getUsers()
    {
        $this->requireAdmin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $search = $_GET['search'] ?? '';
        
        $users = $this->userService->getUsers($search, $page, $limit);
        $total = $this->userService->countUsers($search);
        
        ApiResponse::paginated($users, $total, $page, $limit);
    }
    
    private function getUser($id)
    {
        $this->requireAuth();
        
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            ApiResponse::error('User not found', 404);
        }
        
        // Remove sensitive data
        unset($user['password']);
        
        ApiResponse::success($user);
    }
    
    private function getProfile()
    {
        $this->requireAuth();
        
        $user = $this->userService->getUserById($this->getCurrentUserId());
        
        if (!$user) {
            ApiResponse::error('User not found', 404);
        }
        
        unset($user['password']);
        
        ApiResponse::success($user);
    }
    
    private function updateProfile()
    {
        $this->requireAuth();
        
        $result = $this->userService->updateUser($this->getCurrentUserId(), $this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function changePassword()
    {
        $this->requireAuth();
        $this->validateRequired(['current_password', 'new_password']);
        
        $result = $this->userService->changePassword(
            $this->getCurrentUserId(),
            $this->requestBody['current_password'],
            $this->requestBody['new_password']
        );
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function updateUser($id)
    {
        $this->requireAdmin();
        
        $result = $this->userService->updateUser($id, $this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function deleteUser($id)
    {
        $this->requireAdmin();
        
        $result = $this->userService->deleteUser($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
