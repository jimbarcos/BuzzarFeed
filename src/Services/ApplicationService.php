<?php
/*
PROGRAM NAME: Application Service (ApplicationService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It handles the business logic for stall applications submitted by users.
It interacts with the Database, Session, EmailService, and AdminLogService utilities to approve, decline, archive, and manage applications.
It ensures that moderation workflows, file handling, and email notifications are consistently enforced.
This service is primarily used by admin controllers or application management tools.

DATE CREATED: Novemeber 23, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to provide a structured and reusable service for managing stall applications.
It allows admins to approve or decline applications, create food stalls, archive old applications, manage application files, and notify applicants of moderation decisions.
It maintains data integrity through transactions and logs all admin actions for auditing purposes.

DATA STRUCTURES:
- $db (Database): Database instance for executing queries.
- $logService (AdminLogService): Service for logging admin actions.
- $application (array): Application record fetched from the database.
- $filesToDelete (array): Array of application files to be deleted.
- $uploadDir (string): Base directory path for uploaded files.
- $stallId (int): ID of newly created food stall.
- $adminId (int|null): Admin user performing the action.
- $reviewNotes (string): Optional notes provided by the admin during approval or decline.

ALGORITHM / LOGIC:
1. getApplicationById():
   - Retrieve a single application record joined with user details.
2. getPendingApplications():
   - Retrieve all applications with status 'pending' (current_status_id = 1).
3. approveApplication():
   - Fetch application data.
   - Start a transaction.
   - Create the food stall using application data.
   - Create stall location if provided.
   - Update application status to approved.
   - Update the user's timestamp.
   - Commit transaction.
   - Log admin action.
   - Send approval email to the applicant.
4. declineApplication():
   - Fetch application data.
   - Delete uploaded application files.
   - Remove application record from database.
   - Log admin action.
   - Send decline email to the applicant.
5. archiveApplication():
   - Update application status to archived (3).
   - Log admin action.
6. createFoodStall():
   - Insert food stall into database using application details.
   - Return the new stall ID.
7. createStallLocation():
   - Insert stall location data into database.
8. updateApplicationStatus():
   - Update the application's current_status_id field.
9. updateUserTimestamp():
   - Update the updated_at timestamp for the user.
10. deleteApplicationFiles():
    - Remove application files from the server filesystem.
    - Delete the directory if empty.
11. sendApprovalEmail():
    - Send HTML email notification of approval to applicant.
    - Catch exceptions to prevent email failures from affecting the approval.
12. sendDeclineEmail():
    - Send HTML email notification of decline to applicant.
    - Catch exceptions to prevent email failures from affecting the decline.

NOTES:
- All public methods ensure proper validation and existence checks for applications.
- Transactions are used in approveApplication() to maintain consistency when creating food stalls and updating statuses.
- File handling ensures that uploaded documents are safely deleted when applications are declined.
- Admin actions are logged for auditing purposes.
- Email failures are logged but do not interrupt the approval/decline workflows.
- Future enhancements may include batch approval, automated notifications, and advanced reporting dashboards.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;
use BuzzarFeed\Utils\Session;
use BuzzarFeed\Utils\EmailService;

class ApplicationService
{
    private Database $db;
    private AdminLogService $logService;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logService = new AdminLogService();
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

    /**
     * Approve an application and create the food stall
     * 
     * Steps:
     *  1. Validate and fetch the application.
     *  2. Start DB transaction.
     *  3. Create the food stall and location.
     *  4. Update application + user timestamp.
     *  5. Commit transaction.
     *  6. Send approval email (outside transaction).
     * 
     * @param int $applicationId
     * @param string $reviewNotes
     * @return bool
     * @throws \Exception
     */
    public function approveApplication(int $applicationId, string $reviewNotes = ''): bool
    {
        $application = $this->getApplicationById($applicationId);
        
        if (!$application) {
            throw new \Exception('Application not found');
        }
        
        try {
            // Start transaction
            $this->db->execute("START TRANSACTION");
            
            // Create food stall
            $stallId = $this->createFoodStall($application);
            
            // Create stall location
            if (!empty($application['location'])) {
                $this->createStallLocation($stallId, $application);
            }
            
            // Update application status to approved (2)
            $this->updateApplicationStatus($applicationId, 2);
            
            // Update user timestamp
            $this->updateUserTimestamp($application['user_id']);
            
            // Commit transaction
            $this->db->execute("COMMIT");
            
            // Log admin action
            $adminId = Session::get('user_id');
            if ($adminId) {
                $this->logService->logApplicationApproval(
                    $adminId,
                    $applicationId,
                    $application['stall_name'],
                    $reviewNotes
                );
            }
            
            // Send approval email
            $this->sendApprovalEmail($application, $reviewNotes);
            
            return true;
        } catch (\Exception $e) {
            $this->db->execute("ROLLBACK");
            throw $e;
        }
    }

    /**
     * Decline an application and clean up files
     * 
     * @param int $applicationId
     * @param string $reviewNotes
     * @return bool
     * @throws \Exception
     */
    public function declineApplication(int $applicationId, string $reviewNotes = ''): bool
    {
        $application = $this->getApplicationById($applicationId);
        
        if (!$application) {
            throw new \Exception('Application not found');
        }
        
        try {
            // Delete uploaded files
            $this->deleteApplicationFiles($application);
            
            // Delete application from database
            $this->db->execute(
                "DELETE FROM applications WHERE application_id = ?",
                [$applicationId]
            );
            
            // Log admin action
            $adminId = Session::get('user_id');
            if ($adminId) {
                $this->logService->logApplicationDecline(
                    $adminId,
                    $applicationId,
                    $application['stall_name'],
                    $reviewNotes
                );
            }
            
            // Send decline email
            $this->sendDeclineEmail($application, $reviewNotes);
            
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Archive/hide an application
     * 
     * @param int $applicationId
     * @return bool
     */
    public function archiveApplication(int $applicationId): bool
    {
        $application = $this->getApplicationById($applicationId);
        
        $this->updateApplicationStatus($applicationId, 3);
        
        // Log admin action
        $adminId = Session::get('user_id');
        if ($adminId && $application) {
            $this->logService->logApplicationArchive(
                $adminId,
                $applicationId,
                $application['stall_name']
            );
        }
        
        return true;
    }

    /**
     * Create food stall from application data
     * 
     * @param array $application
     * @return int Stall ID
     */
    private function createFoodStall(array $application): int
    {
        $query = "INSERT INTO food_stalls 
            (owner_id, name, description, logo_path, food_categories, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, 1, NOW())";
        
        $this->db->execute($query, [
            $application['user_id'],
            $application['stall_name'],
            $application['stall_description'],
            $application['stall_logo_path'],
            $application['food_categories']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Create stall location from application data
     * 
     * @param int $stallId
     * @param array $application
     * @return void
     */
    private function createStallLocation(int $stallId, array $application): void
    {
        $query = "INSERT INTO stall_locations 
            (stall_id, address, latitude, longitude, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
        
        $this->db->execute($query, [
            $stallId,
            $application['location'],
            $application['map_x'] ?? null,
            $application['map_y'] ?? null
        ]);
    }

    /**
     * Update application status
     * 
     * @param int $applicationId
     * @param int $statusId
     * @return void
     */
    private function updateApplicationStatus(int $applicationId, int $statusId): void
    {
        $this->db->execute(
            "UPDATE applications SET current_status_id = ? WHERE application_id = ?",
            [$statusId, $applicationId]
        );
    }
    
    /**
     * Update user timestamp
     * 
     * @param int $userId
     * @return void
     */
    private function updateUserTimestamp(int $userId): void
    {
        $this->db->execute(
            "UPDATE users SET updated_at = NOW() WHERE user_id = ?",
            [$userId]
        );
    }
    
    /**
     * Delete application files
     * 
     * @param array $application
     * @return void
     */
    private function deleteApplicationFiles(array $application): void
    {
        $uploadDir = __DIR__ . '/../../';
        $filesToDelete = [
            $application['bir_registration_path'],
            $application['business_permit_path'],
            $application['dti_sec_path'],
            $application['stall_logo_path']
        ];
        
        foreach ($filesToDelete as $filePath) {
            if ($filePath && file_exists($uploadDir . $filePath)) {
                unlink($uploadDir . $filePath);
            }
        }
        
        // Delete the directory if empty
        if (!empty($application['bir_registration_path'])) {
            $appDir = dirname($uploadDir . $application['bir_registration_path']);
            if (is_dir($appDir) && count(scandir($appDir)) == 2) {
                rmdir($appDir);
            }
        }
    }

    /**
     * Send approval email to applicant
     * 
     * @param array $application
     * @param string $reviewNotes
     * @return void
     */
    private function sendApprovalEmail(array $application, string $reviewNotes): void
    {
        try {
            $emailService = EmailService::getInstance();
            $emailService->sendApplicationApprovalEmail(
                $application['email'],
                $application['name'],
                $application['stall_name'],
                $reviewNotes
            );
            error_log("Application approval email sent to: {$application['email']}");
        } catch (\Exception $e) {
            error_log("Failed to send approval email: " . $e->getMessage());
            // Don't throw exception - email failure shouldn't stop the approval process
        }
    }
    
    /**
     * Send decline email to applicant
     * 
     * @param array $application
     * @param string $reviewNotes
     * @return void
     */
    private function sendDeclineEmail(array $application, string $reviewNotes): void
    {
        try {
            $emailService = EmailService::getInstance();
            $emailService->sendApplicationDeclineEmail(
                $application['email'],
                $application['name'],
                $application['stall_name'],
                $reviewNotes
            );
            error_log("Application decline email sent to: {$application['email']}");
        } catch (\Exception $e) {
            error_log("Failed to send decline email: " . $e->getMessage());
            // Don't throw exception - email failure shouldn't stop the decline process
        }
    }
}