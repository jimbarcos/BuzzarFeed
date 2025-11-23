<?php
/**
 * BuzzarFeed - Application Service
 * 
 * Handles business logic for stall application management
 * Following ISO 9241 principles: Modularity, Reusability, Separation of Concerns
 * 
 * @package BuzzarFeed\Services
 * @version 1.0
 */

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Session;
use BuzzarFeed\Utils\EmailService;

class ApplicationService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get application by ID with user details
     * 
     * @param int $applicationId
     * @return array|null
     */
    public function getApplicationById(int $applicationId): ?array
    {
        return $this->db->querySingle(
            "SELECT a.*, u.name, u.email 
             FROM applications a 
             JOIN users u ON a.user_id = u.user_id 
             WHERE a.application_id = ?",
            [$applicationId]
        );
    }
    
    /**
     * Get all pending applications
     * 
     * @return array
     */
    public function getPendingApplications(): array
    {
        return $this->db->query(
            "SELECT a.*, u.name as applicant_name, u.email as applicant_email 
             FROM applications a 
             JOIN users u ON a.user_id = u.user_id 
             WHERE a.current_status_id = 1 
             ORDER BY a.created_at DESC"
        );
    }
}