<?php
/**
 * BuzzarFeed - Stall Registration Service
 * 
 * Handles stall registration business logic
 * Following ISO 9241 principles: Modularity, Reusability, Separation of Concerns
 * 
 * @package BuzzarFeed\Services
 * @version 1.0
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

}