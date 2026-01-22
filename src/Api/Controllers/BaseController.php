<?php
/*
PROGRAM NAME: Base API Controller (BaseController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It serves as the abstract base class for all API controllers in the system.
The BaseController provides common functionality including authentication checking,
authorization enforcement, request body parsing, and field validation that is shared
across all specific resource controllers (auth, users, stalls, reviews, etc.).

All API controllers extend this base class to inherit its shared functionality and
must implement the handleRequest method to define their specific routing logic.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide a reusable foundation for all API controllers.
It centralizes common operations such as session management, authentication verification,
admin privilege checking, and request validation. By abstracting these concerns into a base
class, the system promotes code reuse, maintains consistency across controllers, and
simplifies the implementation of new API endpoints.

DATA STRUCTURES:
- $requestBody (array): Parsed JSON request body from client.
- Session data:
  - user_id (int): Authenticated user's identifier.
  - role (string): User's role (e.g., 'admin', 'vendor').
  - Various user profile fields stored in session.
- Required fields array: List of field names that must be present in requests.
- Missing fields array: List of field names that failed validation.

ALGORITHM / LOGIC:
1. Constructor initialization:
   a. Start or resume PHP session.
   b. Parse JSON request body and store in $requestBody property.
2. Define abstract handleRequest method:
   a. Must be implemented by all child controllers.
   b. Parameters: HTTP method, resource ID, and action.
3. Authentication checking:
   a. Query session to determine if user is logged in.
   b. Return boolean indicating authentication status.
4. Require authentication:
   a. Check if user is authenticated.
   b. If not, return 401 Unauthorized error and terminate.
5. Require admin privileges:
   a. First verify user is authenticated.
   b. Check if user has admin role.
   c. If not admin, return 403 Forbidden error and terminate.
6. Get current user ID:
   a. Retrieve user_id from session.
   b. Return user ID or null if not authenticated.
7. Validate required fields:
   a. Check request body for presence of required fields.
   b. If any fields are missing, return 400 Bad Request error.
   c. Include list of missing fields in error message.

NOTES:
- This is an abstract class and cannot be instantiated directly.
- All API controllers must extend this class.
- Authentication is session-based, not token-based.
- The requireAuth and requireAdmin methods terminate execution on failure.
- Field validation is basic; complex validation should occur in service layers.
- All child classes have access to parsed request body via $this->requestBody.
- Future enhancements may include:
  - Rate limiting per user or IP address
  - Request logging and audit trails
  - Support for JWT or OAuth2 authentication
  - Role-based access control (RBAC) beyond simple admin checking
  - Request/response transformation hooks
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Utils\Session;

abstract class BaseController
{
    protected $requestBody;
    
    public function __construct()
    {
        Session::start();
        $this->requestBody = ApiResponse::getRequestBody();
    }
    
    /**
     * Handle incoming request
     * 
     * @param string $method HTTP method
     * @param string|null $id Resource ID
     * @param string|null $action Additional action
     */
    abstract public function handleRequest($method, $id = null, $action = null);
    
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated()
    {
        return Session::isLoggedIn();
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            ApiResponse::error('Authentication required', 401);
        }
    }
    
    /**
     * Require admin privileges
     */
    protected function requireAdmin()
    {
        $this->requireAuth();
        if (!Session::isAdmin()) {
            ApiResponse::error('Admin access required', 403);
        }
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    protected function getCurrentUserId()
    {
        return Session::get('user_id');
    }
    
    /**
     * Validate required fields in request body
     * 
     * @param array $required Required field names
     */
    protected function validateRequired($required)
    {
        $missing = ApiResponse::validateRequired($this->requestBody, $required);
        if (!empty($missing)) {
            ApiResponse::error('Missing required fields: ' . implode(', ', $missing), 400);
        }
    }
}
