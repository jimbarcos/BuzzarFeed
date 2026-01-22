<?php
/*
PROGRAM NAME: Session Management Utility (Session.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides centralized and secure session management utilities used across the application.
The Session class is responsible for starting sessions, managing session variables, handling authentication state, generating and verifying CSRF tokens, and enforcing security best practices such as session regeneration.
It is designed to be used by controllers, middleware, and other utility classes that require session access.

DATE CREATED: November 29, 2025
LAST MODIFIED: November 29, 2025

PURPOSE:
The purpose of this program is to abstract PHP’s native session handling into a secure, reusable, and maintainable utility class.
It ensures consistent session behavior across the system, improves security against session fixation and hijacking attacks, and simplifies common session-related tasks such as flash messaging, authentication checks, and CSRF protection.

DATA STRUCTURES:
- $_SESSION (superglobal array): Stores all session-related data.
- SESSION_NAME (constant): Defines the custom session name for the application.
- CSRF_TOKEN_LENGTH (constant): Specifies the length of generated CSRF tokens.
- 'created' (int): Timestamp indicating when the session was created, used for regeneration logic.
- 'user_id' (int): Stores the authenticated user’s ID.
- 'user_type' (string): Stores the role or type of the logged-in user.
- 'flash_message' (array): Temporary message structure containing:
  - 'message' (string): Flash message text.
  - 'type' (string): Message category (success, error, info).
- 'csrf_token' (string): CSRF token stored for request validation.

ALGORITHM / LOGIC:
1. Check if a session is already active before performing any session operation.
2. When starting a session:
   a. Apply secure session settings (HTTP-only cookies, cookie-only usage, HTTPS when available).
   b. Assign a custom session name.
   c. Start the PHP session.
3. Store the session creation timestamp on first initialization.
4. Regenerate the session ID if the session lifetime exceeds 30 minutes to prevent fixation attacks.
5. Provide helper methods to:
   a. Set, get, check, and remove session variables.
   b. Destroy the session and clear cookies securely.
6. Handle authentication-related logic:
   a. Determine if a user is logged in.
   b. Retrieve the logged-in user’s ID and user type.
7. Manage flash messages by storing them temporarily and removing them after retrieval.
8. Generate cryptographically secure CSRF tokens using a helper utility.
9. Verify CSRF tokens using timing-attack-safe comparison (hash_equals).
10. Allow manual session ID regeneration when needed.

NOTES:
- All public methods ensure the session is started before accessing $_SESSION data.
- Session regeneration is time-based and automatic, reducing the risk of long-lived session abuse.
- Flash messages are designed for one-time use, commonly for form submissions and redirects.
- CSRF protection relies on server-side token storage and strict comparison.
- This utility should be used instead of direct access to $_SESSION to ensure consistent behavior across the system.
- Future enhancements may include configurable session lifetimes, multi-device session tracking, or persistent login support.
*/

namespace BuzzarFeed\Utils;

class Session {
    
    /**
     * Start session with security settings
     * 
     * @return void
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            
            session_name(SESSION_NAME);
            session_start();
            
            // Regenerate session ID periodically for security
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
    
    /**
     * Set session variable
     * 
     * @param string $key Session key
     * @param mixed $value Session value
     * @return void
     */
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session variable
     * 
     * @param string $key Session key
     * @param mixed $default Default value if not set
     * @return mixed Session value or default
     */
    public static function get(string $key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Check if session variable exists
     * 
     * @param string $key Session key
     * @return bool True if exists
     */
    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session variable
     * 
     * @param string $key Session key
     * @return void
     */
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Destroy session
     * 
     * @return void
     */
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if logged in
     */
    public static function isLoggedIn(): bool {
        return self::has('user_id');
    }
    
    /**
     * Get logged in user ID
     * 
     * @return int|null User ID or null
     */
    public static function getUserId(): ?int {
        return self::get('user_id');
    }
    
    /**
     * Get logged in user type
     * 
     * @return string|null User type or null
     */
    public static function getUserType(): ?string {
        return self::get('user_type');
    }
    
    /**
     * Set flash message
     * 
     * @param string $message Flash message
     * @param string $type Message type (success, error, info)
     * @return void
     */
    public static function setFlash(string $message, string $type = 'info'): void {
        self::set('flash_message', ['message' => $message, 'type' => $type]);
    }
    
    /**
     * Get and remove flash message
     * 
     * @return array|null Flash message array or null
     */
    public static function getFlash(): ?array {
        $flash = self::get('flash_message');
        self::remove('flash_message');
        return $flash;
    }
    
    /**
     * Regenerate session ID for security
     * 
     * @param bool $deleteOldSession Whether to delete old session
     * @return bool Success status
     */
    public static function regenerate(bool $deleteOldSession = true): bool {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return session_regenerate_id($deleteOldSession);
        }
        return false;
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCsrfToken(): string {
        $token = Helpers::generateToken(CSRF_TOKEN_LENGTH);
        self::set('csrf_token', $token);
        return $token;
    }
    
    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if valid
     */
    public static function verifyCsrfToken(string $token): bool {
        $sessionToken = self::get('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }
}
