<?php
/*
PROGRAM NAME: API Request Router (index.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is the central entry point for all API requests in the BuzzarFeed platform.
It serves as the main routing mechanism for the RESTful API, intercepting incoming HTTP requests
and directing them to the appropriate controller based on the requested resource and action.
The router handles CORS configuration, preflight requests, and provides a unified error handling
mechanism for all API endpoints.

This router is invoked when clients make requests to /api/* endpoints and uses a resource-based
routing strategy to map URLs to controller methods.

DATE CREATED: December 23, 2026
LAST MODIFIED: December 23, 2026

PURPOSE:
The purpose of this program is to provide a centralized API routing layer that standardizes request
handling across the BuzzarFeed application. It ensures consistent CORS headers, proper HTTP method
handling, and clean URL-to-controller mapping. By centralizing the routing logic, this module
simplifies API endpoint management and provides a single point of entry for all API operations.

DATA STRUCTURES:
- $_SERVER (superglobal): Contains HTTP request information including method and URI.
- $path (string): The requested URL path after removing the base API prefix.
- $segments (array): URL path components split by forward slashes.
- $resource (string): The primary resource being requested (e.g., 'stalls', 'reviews').
- $id (string|null): Optional resource identifier from the URL.
- $action (string|null): Optional action parameter for sub-resource operations.
- Controller objects: Instances of various API controller classes.

ALGORITHM / LOGIC:
1. Set CORS headers to allow cross-origin API requests:
   a. Enable JSON content type responses.
   b. Allow all origins (Access-Control-Allow-Origin: *).
   c. Permit GET, POST, PUT, DELETE, OPTIONS methods.
   d. Allow Content-Type and Authorization headers.
2. Handle OPTIONS preflight requests:
   a. Return 200 status code immediately for preflight checks.
3. Load application bootstrap to initialize autoloading and configuration.
4. Extract HTTP method from server variables.
5. Parse request URI to extract path components:
   a. Remove the base '/api' prefix if present.
   b. Split path into segments.
   c. Extract resource, id, and action from segments.
6. Route request to appropriate controller based on resource:
   a. 'auth' → AuthController
   b. 'stalls' → StallController
   c. 'reviews' → ReviewController
   d. 'users' → UserController
   e. 'applications' → ApplicationController
   f. 'amendments' → AmendmentController
   g. 'closures' → ClosureController
   h. 'admin' → AdminController
7. If resource is not recognized, return 404 error.
8. Call controller's handleRequest method with method, id, and action parameters.
9. Catch any exceptions and return 500 error with exception message.

NOTES:
- All API responses are in JSON format with standardized structure.
- CORS is configured to allow requests from any origin for development flexibility.
- The router uses a simple path-based routing strategy without a complex routing library.
- Each controller is responsible for handling method-specific logic (GET, POST, PUT, DELETE).
- Error handling ensures that all exceptions result in proper JSON error responses.
- The router does not perform authentication; this is delegated to individual controllers.
- Future enhancements may include:
  - Route-specific middleware support
  - Rate limiting
  - Request logging
  - API versioning (e.g., /api/v1, /api/v2)
  - More restrictive CORS policies for production environments
*/

// Set headers for API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../bootstrap.php';

use BuzzarFeed\Api\Controllers\StallController;
use BuzzarFeed\Api\Controllers\ReviewController;
use BuzzarFeed\Api\Controllers\UserController;
use BuzzarFeed\Api\Controllers\AuthController;
use BuzzarFeed\Api\Controllers\ApplicationController;
use BuzzarFeed\Api\Controllers\AmendmentController;
use BuzzarFeed\Api\Controllers\ClosureController;
use BuzzarFeed\Api\Controllers\AdminController;
use BuzzarFeed\Utils\ApiResponse;

try {
    // Get request method and path
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove base path if exists
    $basePath = '/api';
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }
    
    // Remove leading slash
    $path = trim($path, '/');
    
    // Split path into segments
    $segments = explode('/', $path);
    $resource = $segments[0] ?? '';
    $id = $segments[1] ?? null;
    $action = $segments[2] ?? null;
    
    // Route to appropriate controller
    switch ($resource) {
        case 'auth':
            $controller = new AuthController();
            break;
        case 'stalls':
            $controller = new StallController();
            break;
        case 'reviews':
            $controller = new ReviewController();
            break;
        case 'users':
            $controller = new UserController();
            break;
        case 'applications':
            $controller = new ApplicationController();
            break;
        case 'amendments':
            $controller = new AmendmentController();
            break;
        case 'closures':
            $controller = new ClosureController();
            break;
        case 'admin':
            $controller = new AdminController();
            break;
        default:
            ApiResponse::error('Resource not found', 404);
            exit;
    }
    
    // Call controller method based on HTTP method and action
    $controller->handleRequest($method, $id, $action);
    
} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
