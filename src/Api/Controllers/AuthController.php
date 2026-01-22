<?php
/*
PROGRAM NAME: Authentication API Controller (AuthController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all authentication-related API endpoints including user login, logout, registration,
email verification, password recovery, and password reset operations.
The AuthController works in conjunction with the UserService to validate credentials, manage
sessions, and ensure secure authentication flows throughout the application.

This controller processes authentication requests from the frontend and maintains user session
state for subsequent authenticated requests.

DATE CREATED: January 23, 2026
LAST MODIFIED: January 23, 2026

PURPOSE:
The purpose of this program is to provide secure authentication endpoints for the BuzzarFeed API.
It manages user identity verification, session creation and destruction, user registration,
and password recovery workflows. By centralizing authentication logic, this controller ensures
consistent security practices and simplifies the implementation of authentication features
across the application.

DATA STRUCTURES:
- $userService (UserService): Service instance for user-related operations.
- Login request:
  - email (string): User's email address.
  - password (string): User's password (plaintext, verified against hash).
- Registration request:
  - email, password, firstName, lastName, phone (strings): New user data.
- User session data:
  - user_id, role, first_name, last_name (mixed): Stored in PHP session.
- User record from database:
  - user_id, email, password (hash), first_name, last_name, role, status (strings/ints).
- Token strings: Random cryptographic tokens for email verification and password reset.

ALGORITHM / LOGIC:
1. Initialize UserService in constructor.
2. Route requests based on action parameter:
   a. 'login' → validate credentials and create session.
   b. 'logout' → destroy session.
   c. 'register' → create new user account.
   d. 'verify-email' → confirm email address with token.
   e. 'forgot-password' → send password reset email.
   f. 'reset-password' → update password with valid token.
   g. 'check' → return current authentication status.
3. Login process:
   a. Validate required fields (email, password).
   b. Query database for user with matching email and active status.
   c. Verify password against stored hash.
   d. If valid, store user data in session.
   e. Return user profile without sensitive data.
   f. If invalid, return 401 Unauthorized error.
4. Logout process:
   a. Destroy PHP session.
   b. Return success response.
5. Registration process:
   a. Validate required fields.
   b. Call UserService to create new user.
   c. Return success or error based on service result.
6. Email verification:
   a. Validate token parameter.
   b. Call UserService to verify email.
   c. Return success or error.
7. Password recovery:
   a. Validate email parameter.
   b. Call UserService to send reset email.
   c. Return success response (even if email doesn't exist for security).
8. Password reset:
   a. Validate token and new password.
   b. Call UserService to update password.
   c. Return success or error.
9. Authentication check:
   a. Check if user is logged in via session.
   b. Return authentication status and user data if authenticated.

NOTES:
- Password verification uses password_verify() for security.
- Sensitive data (password hashes) is never returned in responses.
- Session-based authentication is used throughout the application.
- Email verification and password reset use cryptographic tokens.
- Failed login attempts do not reveal whether email exists for security.
- All authentication operations are logged via session management.
- Future enhancements may include:
  - Multi-factor authentication (MFA)
  - OAuth2/social login integration
  - Login attempt rate limiting
  - Password strength enforcement
  - Session timeout and refresh mechanisms
  - Account lockout after failed attempts
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Utils\Session;
use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Helpers;
use BuzzarFeed\Services\UserService;

class AuthController extends BaseController
{
    private $userService;
    
    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($action) {
            case 'login':
                if ($method === 'POST') {
                    $this->login();
                }
                break;
            case 'logout':
                if ($method === 'POST') {
                    $this->logout();
                }
                break;
            case 'register':
                if ($method === 'POST') {
                    $this->register();
                }
                break;
            case 'verify-email':
                if ($method === 'POST') {
                    $this->verifyEmail();
                }
                break;
            case 'forgot-password':
                if ($method === 'POST') {
                    $this->forgotPassword();
                }
                break;
            case 'reset-password':
                if ($method === 'POST') {
                    $this->resetPassword();
                }
                break;
            case 'check':
                if ($method === 'GET') {
                    $this->checkAuth();
                }
                break;
            default:
                ApiResponse::error('Invalid action', 404);
        }
    }
    
    private function login()
    {
        $this->validateRequired(['email', 'password']);
        
        $email = $this->requestBody['email'];
        $password = $this->requestBody['password'];
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            ApiResponse::error('Invalid email or password', 401);
        }
        
        // Set session
        Session::set('user_id', $user['user_id']);
        Session::set('role', $user['role']);
        Session::set('first_name', $user['first_name']);
        Session::set('last_name', $user['last_name']);
        
        ApiResponse::success([
            'user' => [
                'id' => $user['user_id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'role' => $user['role']
            ]
        ], 200, 'Login successful');
    }
    
    private function logout()
    {
        Session::destroy();
        ApiResponse::success(null, 200, 'Logout successful');
    }
    
    private function register()
    {
        $this->validateRequired(['email', 'password', 'firstName', 'lastName', 'phone']);
        
        $result = $this->userService->createUser([
            'email' => $this->requestBody['email'],
            'password' => $this->requestBody['password'],
            'first_name' => $this->requestBody['firstName'],
            'last_name' => $this->requestBody['lastName'],
            'phone' => $this->requestBody['phone']
        ]);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function verifyEmail()
    {
        $this->validateRequired(['token']);
        
        $result = $this->userService->verifyEmail($this->requestBody['token']);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function forgotPassword()
    {
        $this->validateRequired(['email']);
        
        $result = $this->userService->sendPasswordReset($this->requestBody['email']);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function resetPassword()
    {
        $this->validateRequired(['token', 'password']);
        
        $result = $this->userService->resetPassword(
            $this->requestBody['token'],
            $this->requestBody['password']
        );
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function checkAuth()
    {
        if (!$this->isAuthenticated()) {
            ApiResponse::success(['authenticated' => false]);
        }
        
        ApiResponse::success([
            'authenticated' => true,
            'user' => [
                'id' => Session::get('user_id'),
                'firstName' => Session::get('first_name'),
                'lastName' => Session::get('last_name'),
                'role' => Session::get('role')
            ]
        ]);
    }
}
