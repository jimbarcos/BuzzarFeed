<?php
/*
PROGRAM NAME: User Account Management Service (UserService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides business logic for managing user accounts, including profile management, password updates, account deletion, and profile image uploads.
The UserService class interacts with the Database utility and Helper functions to ensure secure and consistent operations across the system.
It is typically used by controllers, API endpoints, and other service layers that require user account management functionality.

DATE CREATED: Novemeber 29, 2025
LAST MODIFIED: Novemeber 29, 2025

PURPOSE:
The purpose of this program is to centralize user-related business logic in a reusable and maintainable service.
It ensures secure handling of sensitive user data, enforces validation rules, and provides structured methods for account operations.
By abstracting database interactions and helper utilities, it promotes code modularity and separation of concerns.

DATA STRUCTURES:
- $db (Database): Database instance used for executing queries and commands.
- User data arrays:
  - user_id (int): Unique identifier of the user.
  - name (string): User's display name.
  - user_type_id (int): Identifier for the user's role/type.
  - profile_image (string): Path to user's profile image.
  - hashed_password (string): Securely hashed password.
- $_FILES array elements: Uploaded file information for profile images.
- Validation result arrays:
  - 'valid' (bool): Indicates if input is valid.
  - 'message' (string): Validation feedback message.

ALGORITHM / LOGIC:
1. Initialize Database instance on service construction.
2. Retrieve user information:
   a. Join with user type to include type name and description.
3. Update user profile:
   a. Validate required fields.
   b. Execute update query with timestamp.
4. Change user password:
   a. Verify current password against stored hash.
   b. Validate new password strength and rules.
   c. Hash new password and update the database.
5. Delete user account:
   a. Verify password.
   b. Check for dependent resources (e.g., food stalls).
   c. Delete user if no dependencies exist.
6. Upload and update profile image:
   a. Validate file type and size.
   b. Create upload directory if missing.
   c. Generate unique filename and move uploaded file.
   d. Update database with relative path.
7. Ensure all operations throw exceptions on invalid input or failure.
8. Maintain modularity and reusability by leveraging Database and Helper utilities.

NOTES:
- This service abstracts database operations from controllers and endpoints.
- Password management uses secure hashing and validation utilities.
- Profile image uploads enforce strict type and size constraints for security.
- Account deletion is prevented if dependent resources exist.
- Methods are designed for exception handling to propagate meaningful errors to calling layers.
- Future enhancements may include multi-factor authentication, account activity logging, and profile image optimization.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Helpers;

class UserService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get user by ID with type information
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserById(int $userId): ?array
    {
        return $this->db->querySingle(
            "SELECT u.*, ut.type_name, ut.description as type_description
             FROM users u
             INNER JOIN user_types ut ON u.user_type_id = ut.user_type_id
             WHERE u.user_id = ?",
            [$userId]
        );
    }
    
    /**
     * Update user profile
     * 
     * @param int $userId
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function updateProfile(int $userId, string $name): bool
    {
        if (empty($name)) {
            throw new \Exception("Name is required");
        }
        
        $this->db->execute(
            "UPDATE users SET name = ?, updated_at = NOW() WHERE user_id = ?",
            [$name, $userId]
        );
        
        return true;
    }
    
    /**
     * Change user password
     * 
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @param string $hashedPassword Current hashed password from DB
     * @return bool
     * @throws \Exception
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword, string $hashedPassword): bool
    {
        // Verify current password
        if (!Helpers::verifyPassword($currentPassword, $hashedPassword)) {
            throw new \Exception("Current password is incorrect");
        }
        
        // Validate new password
        $passwordValidation = Helpers::validatePassword($newPassword);
        if (!$passwordValidation['valid']) {
            throw new \Exception($passwordValidation['message']);
        }
        
        // Update password
        $newHashedPassword = Helpers::hashPassword($newPassword);
        $this->db->execute(
            "UPDATE users SET hashed_password = ?, updated_at = NOW() WHERE user_id = ?",
            [$newHashedPassword, $userId]
        );
        
        return true;
    }
    
    /**
     * Delete user account
     * 
     * @param int $userId
     * @param string $password
     * @param string $hashedPassword Current hashed password from DB
     * @return bool
     * @throws \Exception
     */
    public function deleteAccount(int $userId, string $password, string $hashedPassword): bool
    {
        // Verify password
        if (!Helpers::verifyPassword($password, $hashedPassword)) {
            throw new \Exception("Password is incorrect");
        }
        
        // Check for associated stalls
        $stalls = $this->db->query(
            "SELECT stall_id FROM food_stalls WHERE owner_id = ?",
            [$userId]
        );
        
        if (!empty($stalls)) {
            throw new \Exception("Cannot delete account with active stalls. Please remove your stalls first.");
        }
        
        // Delete user
        $this->db->execute("DELETE FROM users WHERE user_id = ?", [$userId]);
        
        return true;
    }
    
    /**
     * Upload and update profile image
     * 
     * @param int $userId
     * @param array $file $_FILES array element
     * @return string Path to uploaded image
     * @throws \Exception
     */
    public function updateProfileImage(int $userId, array $file): string
    {
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception("No file uploaded");
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception("Only JPG and PNG images are allowed");
        }
        
        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            throw new \Exception("Image must be less than 2MB");
        }
        
        // Create upload directory
        $uploadDir = __DIR__ . '/../../uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception("Failed to upload image");
        }
        
        // Update database
        $relativePath = 'uploads/profiles/' . $filename;
        $this->db->execute(
            "UPDATE users SET profile_image = ?, updated_at = NOW() WHERE user_id = ?",
            [$relativePath, $userId]
        );
        
        return $relativePath;
    }
}
