<?php
/*
PROGRAM NAME: Admin Log Service (AdminLogService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It handles logging of all admin actions for auditing and monitoring purposes.
It interacts with the Database and Session utilities to record actions such as application approvals/declines, review deletions, user conversions, and other administrative operations.
Logs include timestamps, admin identity, IP address, entity affected, action type, and optional details.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to provide a centralized and consistent way to track all administrative actions within the platform.
This ensures accountability, traceability, and allows for monitoring admin activities for compliance and auditing.
All logs are stored in the 'admin_logs' table with reference to the admin performing the action.

DATA STRUCTURES:
- $db (Database): Database instance for executing queries.
- $adminId (int): ID of the admin performing the action.
- $entity (string): Type of entity acted upon (application, review, user, etc.).
- $entityId (int|null): ID of the entity being acted upon.
- $action (string): Action performed (approve, decline, delete, convert_to_admin, archive, etc.).
- $details (string|null): Optional additional details about the action.
- $ipAddress (string|null): IP address of the admin performing the action.

ALGORITHM / LOGIC:
1. logAction():
   - Inserts a record into 'admin_logs' with admin ID, entity, entity ID, action, details, IP address, and timestamp.
2. logApplicationApproval():
   - Logs approval of a stall application, including optional review notes.
3. logApplicationDecline():
   - Logs decline of a stall application, including optional review notes.
4. logApplicationArchive():
   - Logs archiving of a stall application.
5. logReviewDeletion():
   - Logs deletion of a review, including optional reason.
6. logUserConversion():
   - Logs conversion of a user account to admin privileges.
7. getAllLogs():
   - Retrieves all admin logs with pagination, including admin user details.
8. getLogsByAdmin():
   - Retrieves logs performed by a specific admin with pagination.
9. getLogsByEntity():
   - Retrieves logs filtered by entity type with pagination.
10. getTotalLogsCount():
    - Returns the total number of logs in the system.
11. getLogsCountByEntity():
    - Returns the total number of logs for a specific entity type.

NOTES:
- All logging methods are designed to be reusable and consistent across different admin actions.
- Optional details provide context for each action and improve traceability.
- Pagination support allows for efficient retrieval and display of logs in admin dashboards.
- IP address is captured automatically from the server environment to track the source of actions.
- Future enhancements may include filtering by date ranges, exporting logs, or integration with monitoring tools.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Session;

class AdminLogService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Log an admin action
     * 
     * @param int $adminId Admin user ID performing the action
     * @param string $entity Entity type (application, review, user, etc.)
     * @param int|null $entityId ID of the entity being acted upon
     * @param string $action Action performed (approve, decline, delete, convert, etc.)
     * @param string|null $details Additional details about the action
     * @return bool Success status
     */
    public function logAction(
        int $adminId,
        string $entity,
        ?int $entityId,
        string $action,
        ?string $details = null
    ): bool {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        return $this->db->execute(
            "INSERT INTO admin_logs (admin_id, entity, entity_id, action, details, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [$adminId, $entity, $entityId, $action, $details, $ipAddress]
        );
    }
    
    /**
     * Log application approval
     */
    public function logApplicationApproval(int $adminId, int $applicationId, string $stallName, ?string $notes = null): bool
    {
        $details = "Approved stall application: {$stallName}";
        if ($notes) {
            $details .= " | Review Notes: {$notes}";
        }
        
        return $this->logAction($adminId, 'application', $applicationId, 'approve', $details);
    }
    
    /**
     * Log application decline
     */
    public function logApplicationDecline(int $adminId, int $applicationId, string $stallName, ?string $notes = null): bool
    {
        $details = "Declined stall application: {$stallName}";
        if ($notes) {
            $details .= " | Review Notes: {$notes}";
        }
        
        return $this->logAction($adminId, 'application', $applicationId, 'decline', $details);
    }
    
    /**
     * Log application archive
     */
    public function logApplicationArchive(int $adminId, int $applicationId, string $stallName): bool
    {
        $details = "Archived stall application: {$stallName}";
        return $this->logAction($adminId, 'application', $applicationId, 'archive', $details);
    }
    
    /**
     * Log review deletion
     */
    public function logReviewDeletion(int $adminId, int $reviewId, string $stallName, ?string $reason = null): bool
    {
        $details = "Deleted review for stall: {$stallName}";
        if ($reason) {
            $details .= " | Reason: {$reason}";
        }
        
        return $this->logAction($adminId, 'review', $reviewId, 'delete', $details);
    }
    
    /**
     * Log user to admin conversion
     */
    public function logUserConversion(int $adminId, int $convertedUserId, string $userName, string $userEmail): bool
    {
        $details = "Converted user to admin: {$userName} ({$userEmail})";
        return $this->logAction($adminId, 'user', $convertedUserId, 'convert_to_admin', $details);
    }
    
    /**
     * Get all admin logs with pagination
     * 
     * @param int $limit Number of logs per page
     * @param int $offset Starting position
     * @return array Array of log entries
     */
    public function getAllLogs(int $limit = 50, int $offset = 0): array
    {
        return $this->db->query(
            "SELECT 
                al.log_id,
                al.entity,
                al.entity_id,
                al.action,
                al.details,
                al.ip_address,
                al.created_at,
                u.user_id,
                u.name as admin_name,
                u.email as admin_email
             FROM admin_logs al
             JOIN users u ON al.admin_id = u.user_id
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }
    
    /**
     * Get logs by admin user
     */
    public function getLogsByAdmin(int $adminId, int $limit = 50, int $offset = 0): array
    {
        return $this->db->query(
            "SELECT 
                al.log_id,
                al.entity,
                al.entity_id,
                al.action,
                al.details,
                al.ip_address,
                al.created_at,
                u.user_id,
                u.name as admin_name,
                u.email as admin_email
             FROM admin_logs al
             JOIN users u ON al.admin_id = u.user_id
             WHERE al.admin_id = ?
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            [$adminId, $limit, $offset]
        );
    }
    
    /**
     * Get logs by entity type
     */
    public function getLogsByEntity(string $entity, int $limit = 50, int $offset = 0): array
    {
        return $this->db->query(
            "SELECT 
                al.log_id,
                al.entity,
                al.entity_id,
                al.action,
                al.details,
                al.ip_address,
                al.created_at,
                u.user_id,
                u.name as admin_name,
                u.email as admin_email
             FROM admin_logs al
             JOIN users u ON al.admin_id = u.user_id
             WHERE al.entity = ?
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            [$entity, $limit, $offset]
        );
    }
    
    /**
     * Get total count of logs
     */
    public function getTotalLogsCount(): int
    {
        $result = $this->db->querySingle("SELECT COUNT(*) as count FROM admin_logs");
        return $result['count'] ?? 0;
    }
    
    /**
     * Get logs count by entity
     */
    public function getLogsCountByEntity(string $entity): int
    {
        $result = $this->db->querySingle(
            "SELECT COUNT(*) as count FROM admin_logs WHERE entity = ?",
            [$entity]
        );
        return $result['count'] ?? 0;
    }
}
