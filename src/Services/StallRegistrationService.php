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

}