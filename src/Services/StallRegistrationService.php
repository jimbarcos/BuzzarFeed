<?php
/*
PROGRAM NAME: Stall Registration Service (StallRegistrationService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides business logic for handling stall registration and application management.
The StallRegistrationService class interacts with the Database utility and Helper functions to validate user input, manage uploaded files, and create stall applications.
It is typically used by controllers, API endpoints, or other service layers that handle stall registration workflows.

DATE CREATED: Novemeber 23, 2025
LAST MODIFIED: Novemeber 23, 2025

PURPOSE:
The purpose of this program is to centralize stall registration operations into a reusable and maintainable service.
It ensures consistent validation, secure file handling, and proper creation of stall applications.
The service supports rollback mechanisms to delete uploaded files if an error occurs, maintaining data integrity.

DATA STRUCTURES:
- $db (Database): Database instance used for queries and inserts.
- $data (array): Application data including stall name, description, location, categories, and optional map coordinates.
- $filePaths (array): Paths to uploaded application files, including BIR registration, business permit, DTI/SEC certificate, and stall logo.
- $_FILES array elements: Used for validating and uploading files.
- Validation errors array: Maps field names to error messages.
- Stall slug (string): Unique identifier for the upload directory, generated using slugified stall name and uniqid().
- Uploaded file paths (string): Relative paths returned after successful upload.

ALGORITHM / LOGIC:
1. Initialize Database instance on service construction.
2. Check for existing pending applications for a user to prevent duplicates.
3. Validate stall registration data:
   a. Ensure stall name, description, location, and categories are present.
   b. Enforce minimum character lengths for name and description.
4. Validate uploaded files:
   a. Confirm file presence if required.
   b. Restrict to allowed MIME types (PDF, JPG, PNG).
   c. Enforce file size limits (5MB).
5. Upload files securely:
   a. Generate a unique directory for each application.
   b. Move uploaded files to designated folder.
   c. Return relative file paths for database storage.
6. Create stall application:
   a. Insert validated data and uploaded file paths into the applications table.
   b. Set initial status as pending (status_id = 1).
7. Delete application files on rollback:
   a. Remove all uploaded files.
   b. Remove the directory if empty.
8. Ensure consistency and rollback support to maintain data integrity in case of errors.

NOTES:
- All public methods are designed for reuse and modularity following ISO 9241 principles.
- File uploads are isolated per application to avoid conflicts and improve traceability.
- Validation is strict to ensure high-quality and complete stall registrations.
- Rollback logic prevents orphaned files in case of application creation failure.
- Future enhancements may include automatic thumbnail generation, multi-step application workflows, and integration with geolocation APIs for map coordinates.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Helpers;

class StallRegistrationService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check if user has a pending application
     * 
     * @param int $userId
     * @return bool
     */
    public function hasPendingApplication(int $userId): bool
    {
        $pendingApp = $this->db->querySingle(
            "SELECT application_id FROM applications 
             WHERE user_id = ? AND current_status_id = 1",
            [$userId]
        );
        
        return !empty($pendingApp);
    }

    /**
     * Validate stall registration data
     * 
     * @param array $data
     * @return array Validation errors (empty if valid)
     */
    public function validateStallData(array $data): array
    {
        $errors = [];
        
        // Validate stall name
        if (empty($data['stall_name'])) {
            $errors['stall_name'] = 'Stall name is required';
        } elseif (strlen($data['stall_name']) < 3) {
            $errors['stall_name'] = 'Stall name must be at least 3 characters';
        }
        
        // Validate description
        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        } elseif (strlen($data['description']) < 20) {
            $errors['description'] = 'Description must be at least 20 characters';
        }
        
        // Validate location
        if (empty($data['location'])) {
            $errors['location'] = 'Location is required';
        }
        
        // Validate categories
        if (empty($data['categories']) || !is_array($data['categories'])) {
            $errors['categories'] = 'Please select at least one food category';
        }
        
        return $errors;
    }

    /**
     * Validate uploaded file
     * 
     * @param array $file $_FILES array element
     * @param string $fieldName Field name for error messages
     * @param bool $required Whether file is required
     * @return array|null Error array or null if valid
     */
    public function validateFile(array $file, string $fieldName, bool $required = true): ?array
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return $required ? [$fieldName => ucfirst(str_replace('_', ' ', $fieldName)) . ' is required'] : null;
        }
        
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            return [$fieldName => 'Only PDF and image files are allowed'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            return [$fieldName => 'File must be less than 5MB'];
        }
        
        return null;
    }
    
    /**
     * Upload file for stall application
     * 
     * @param array $file $_FILES array element
     * @param string $stallName
     * @param string $fieldName
     * @return string Uploaded file path
     * @throws \Exception
     */
    public function uploadFile(array $file, string $stallName, string $fieldName): string
    {
        // Generate unique directory for this application
        $stallSlug = Helpers::slugify($stallName) . '_' . uniqid();
        $uploadDir = __DIR__ . '/../../uploads/applications/' . $stallSlug . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $fieldName . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception("Failed to upload " . $fieldName);
        }
        
        return 'uploads/applications/' . $stallSlug . '/' . $filename;
    }

     /**
     * Create stall application
     * 
     * @param int $userId
     * @param array $data Application data
     * @param array $filePaths Uploaded file paths
     * @return int Application ID
     * @throws \Exception
     */
    public function createApplication(int $userId, array $data, array $filePaths): int
    {
        $query = "INSERT INTO applications 
            (user_id, stall_name, stall_description, location, food_categories, 
             bir_registration_path, business_permit_path, dti_sec_path, stall_logo_path,
             map_x, map_y, current_status_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
        
        $this->db->execute($query, [
            $userId,
            $data['stall_name'],
            $data['description'],
            $data['location'],
            json_encode($data['categories']),
            $filePaths['bir_registration'] ?? null,
            $filePaths['business_permit'] ?? null,
            $filePaths['dti_sec'] ?? null,
            $filePaths['stall_logo'] ?? null,
            $data['map_x'] ?? null,
            $data['map_y'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Delete application files (rollback on error)
     * 
     * @param array $filePaths
     * @return void
     */
    public function deleteApplicationFiles(array $filePaths): void
    {
        foreach ($filePaths as $path) {
            if ($path && file_exists(__DIR__ . '/../../' . $path)) {
                unlink(__DIR__ . '/../../' . $path);
            }
        }
        
        // Try to remove directory if empty
        if (!empty($filePaths)) {
            $firstPath = reset($filePaths);
            $dir = dirname(__DIR__ . '/../../' . $firstPath);
            if (is_dir($dir) && count(scandir($dir)) == 2) {
                rmdir($dir);
            }
        }
    }
}