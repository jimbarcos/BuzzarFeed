<?php
/*
PROGRAM NAME: API Response Utility (ApiResponse.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's core utility layer.
It provides standardized methods for generating JSON API responses across all API endpoints.
The ApiResponse class ensures consistency in response structure, HTTP status codes, and error handling
throughout the application. It is used by all API controllers to send formatted responses back to clients.

This utility standardizes the JSON response format for both successful operations and error conditions,
making it easier for frontend clients to parse and handle API responses predictably.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide a centralized, reusable mechanism for generating API responses.
It eliminates code duplication across controllers, ensures all responses follow the same structure,
and simplifies the process of sending data back to API clients. By standardizing response formats,
this utility makes the API more predictable and easier to consume from frontend applications.

DATA STRUCTURES:
- Success response structure:
  - success (boolean): Always true for successful responses.
  - message (string): Human-readable success message.
  - data (mixed): Response payload (can be any JSON-serializable type).
- Error response structure:
  - success (boolean): Always false for error responses.
  - message (string): Human-readable error message.
  - errors (array): Additional error details or validation failures.
- Paginated response structure:
  - success (boolean): Always true.
  - data (array): Array of items for current page.
  - pagination (object):
    - total (int): Total number of items across all pages.
    - page (int): Current page number.
    - perPage (int): Number of items per page.
    - totalPages (int): Total number of pages.
- Request body: JSON-decoded associative array from php://input.
- Required fields validation: Array of missing field names.

ALGORITHM / LOGIC:
1. Success response method:
   a. Set HTTP response code (default: 200).
   b. Create JSON object with success=true, message, and data.
   c. Output JSON and terminate execution.
2. Error response method:
   a. Set HTTP response code (default: 400).
   b. Create JSON object with success=false, message, and errors array.
   c. Output JSON and terminate execution.
3. Paginated response method:
   a. Set HTTP response code to 200.
   b. Calculate total pages from total items and items per page.
   c. Create JSON object with success=true, data array, and pagination metadata.
   d. Output JSON and terminate execution.
4. Get request body method:
   a. Read raw input from php://input stream.
   b. Decode JSON string into associative array.
   c. Return decoded array or null if invalid.
5. Validate required fields method:
   a. Iterate through array of required field names.
   b. Check if each field exists and is not empty in data array.
   c. Collect missing field names.
   d. Return array of missing fields.

NOTES:
- All response methods call exit() to prevent further execution and output.
- HTTP status codes follow REST conventions (200 for success, 400 for client errors, etc.).
- The success/error response structure is consistent across all API endpoints.
- Pagination metadata helps clients implement pagination controls in the UI.
- Request body parsing handles raw JSON input from POST/PUT requests.
- Field validation is basic; complex validation should be done in services or controllers.
- All methods are static for convenient access without instantiation.
- Future enhancements may include:
  - Support for additional response formats (XML, CSV)
  - Response compression for large datasets
  - ETag generation for caching support
  - HATEOAS links for RESTful navigation
  - Standardized error codes for programmatic error handling
*/

namespace BuzzarFeed\Utils;

class ApiResponse
{
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param int $code HTTP status code
     * @param string $message Success message
     */
    public static function success($data = null, $code = 200, $message = 'Success')
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array $errors Additional error details
     */
    public static function error($message = 'An error occurred', $code = 400, $errors = [])
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }
    
    /**
     * Send paginated response
     * 
     * @param array $data Response data
     * @param int $total Total items
     * @param int $page Current page
     * @param int $perPage Items per page
     */
    public static function paginated($data, $total, $page = 1, $perPage = 10)
    {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]
        ]);
        exit;
    }
    
    /**
     * Get request body as JSON
     * 
     * @return array|null
     */
    public static function getRequestBody()
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true);
    }
    
    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $required Required field names
     * @return array Missing fields
     */
    public static function validateRequired($data, $required)
    {
        $missing = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        return $missing;
    }
}
