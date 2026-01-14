<?php
/*
PROGRAM NAME: Application Helper (ApplicationHelper.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It serves as a centralized helper for handling application-related business logic that would otherwise be implemented using database triggers.
Due to hosting limitations (such as InfinityFree not supporting triggers), this class provides an application-layer alternative to ensure data consistency and transactional integrity.
It is used by controllers and services responsible for application submission, review, approval, and retrieval.

DATE CREATED: November 29, 2025
LAST MODIFIED: November 29, 2025

PURPOSE:
The purpose of this program is to manage the lifecycle of vendor applications within the BuzzarFeed platform.
It handles submission, review, approval, and status tracking of applications while ensuring that related database operations are executed safely using transactions.
This helper encapsulates complex multi-step database logic to keep controllers clean and maintainable.

DATA STRUCTURES:
- self::$db (Database|null): Singleton database instance used for executing queries.
- applications (table): Stores vendor application records.
- application_reviews (table): Stores application review history.
- approval_statuses (table): Defines possible application states (e.g., pending, approved).
- food_stalls (table): Stores approved vendor stall records.
- stall_locations (table): Stores stall address information.
- JSON-encoded arrays:
  - food_categories

ALGORITHM / LOGIC:
1. Maintain a single database instance using lazy initialization.
2. For application submission:
   a. Retrieve the default "pending" approval status.
   b. Insert a new application record with provided details and documents.
3. For application review:
   a. Start a database transaction.
   b. Insert a review record.
   c. Update the application's current status.
   d. Commit changes or rollback on failure.
4. For application approval:
   a. Retrieve application and approved status details.
   b. Review the application as approved.
   c. Create corresponding food stall and location records.
   d. Commit all changes atomically.
5. For retrieval:
   a. Fetch all applications for a user.
   b. Join status metadata for descriptive output.
6. Handle all database errors using try–catch blocks with rollback protection.

NOTES:
- This class replaces database triggers to maintain compatibility with shared hosting environments.
- All write operations involving multiple tables are wrapped in transactions to prevent partial updates.
- JSON encoding is used for flexible storage of food categories.
- Errors are logged using error_log() for debugging and monitoring.
- The helper is designed to be stateless and accessed via static methods.
- Future enhancements may include role-based validation or audit logging.
*/

namespace BuzzarFeed\Utils;

use PDO;
use PDOException;

class ApplicationHelper {
    
    /**
     * @var Database Database instance
     */
    private static ?Database $db = null;
    
    /**
     * Get database instance
     * 
     * @return Database
     */
    private static function getDb(): Database {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    /**
     * Review an application and update its status
     * Replaces trigger: update_application_status
     * 
     * @param int $applicationId Application ID
     * @param int $reviewerId Reviewer user ID
     * @param int $statusId Status ID
     * @param string $notes Review notes
     * @return bool Success status
     */
    public static function reviewApplication(
        int $applicationId,
        int $reviewerId,
        int $statusId,
        string $notes = ''
    ): bool {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert application review
            $insertQuery = "
                INSERT INTO application_reviews 
                (application_id, reviewer_id, status_id, notes)
                VALUES (:application_id, :reviewer_id, :status_id, :notes)
            ";
            
            $stmt = $conn->prepare($insertQuery);
            $stmt->execute([
                'application_id' => $applicationId,
                'reviewer_id' => $reviewerId,
                'status_id' => $statusId,
                'notes' => $notes
            ]);
            
            // Update application status
            $updateQuery = "
                UPDATE applications 
                SET 
                    current_status_id = :status_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE application_id = :application_id
            ";
            
            $stmt = $conn->prepare($updateQuery);
            $success = $stmt->execute([
                'status_id' => $statusId,
                'application_id' => $applicationId
            ]);
            
            if (!$success) {
                $conn->rollBack();
                return false;
            }
            
            // Commit transaction
            $conn->commit();
            
            return true;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error reviewing application: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Submit a new application
     * 
     * @param int $userId User ID
     * @param string $stallName Stall name
     * @param string $stallDescription Description
     * @param string $location Location
     * @param array $foodCategories Food categories
     * @param array $documents Document paths
     * @return int|false Application ID on success, false on failure
     */
    public static function submitApplication(
        int $userId,
        string $stallName,
        string $stallDescription,
        string $location,
        array $foodCategories,
        array $documents = []
    ) {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Get pending status ID
            $statusQuery = "SELECT status_id FROM approval_statuses WHERE status_name = 'pending'";
            $stmt = $conn->prepare($statusQuery);
            $stmt->execute();
            $status = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$status) {
                return false;
            }
            
            // Insert application
            $query = "
                INSERT INTO applications 
                (user_id, stall_name, stall_description, location, food_categories, 
                 bir_registration_path, business_permit_path, dti_sec_path, stall_logo_path, current_status_id)
                VALUES 
                (:user_id, :stall_name, :stall_description, :location, :food_categories,
                 :bir_registration_path, :business_permit_path, :dti_sec_path, :stall_logo_path, :current_status_id)
            ";
            
            $stmt = $conn->prepare($query);
            $success = $stmt->execute([
                'user_id' => $userId,
                'stall_name' => $stallName,
                'stall_description' => $stallDescription,
                'location' => $location,
                'food_categories' => json_encode($foodCategories),
                'bir_registration_path' => $documents['bir'] ?? null,
                'business_permit_path' => $documents['permit'] ?? null,
                'dti_sec_path' => $documents['dti_sec'] ?? null,
                'stall_logo_path' => $documents['logo'] ?? null,
                'current_status_id' => $status['status_id']
            ]);
            
            if (!$success) {
                return false;
            }
            
            return (int)$conn->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error submitting application: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Approve application and create food stall
     * 
     * @param int $applicationId Application ID
     * @param int $reviewerId Reviewer user ID
     * @param string $notes Approval notes
     * @return int|false Stall ID on success, false on failure
     */
    public static function approveApplication(
        int $applicationId,
        int $reviewerId,
        string $notes = ''
    ) {
        try {
            $db = self::getDb();
            $conn = $db->getConnection();
            
            // Get application details
            $appQuery = "SELECT * FROM applications WHERE application_id = :application_id";
            $stmt = $conn->prepare($appQuery);
            $stmt->execute(['application_id' => $applicationId]);
            $application = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$application) {
                return false;
            }
            
            // Get approved status ID
            $statusQuery = "SELECT status_id FROM approval_statuses WHERE status_name = 'approved'";
            $stmt = $conn->prepare($statusQuery);
            $stmt->execute();
            $approvedStatus = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$approvedStatus) {
                return false;
            }
            
            // Start transaction
            $conn->beginTransaction();
            
            // Review application as approved
            $reviewSuccess = self::reviewApplication(
                $applicationId,
                $reviewerId,
                $approvedStatus['status_id'],
                $notes
            );
            
            if (!$reviewSuccess) {
                $conn->rollBack();
                return false;
            }
            
            // Create food stall
            $stallQuery = "
                INSERT INTO food_stalls 
                (owner_id, name, description, logo_path)
                VALUES 
                (:owner_id, :name, :description, :logo_path)
            ";
            
            $stmt = $conn->prepare($stallQuery);
            $stmt->execute([
                'owner_id' => $application['user_id'],
                'name' => $application['stall_name'],
                'description' => $application['stall_description'],
                'logo_path' => $application['stall_logo_path']
            ]);
            
            $stallId = (int)$conn->lastInsertId();
            
            // Create stall location
            $locationQuery = "
                INSERT INTO stall_locations 
                (stall_id, address)
                VALUES 
                (:stall_id, :address)
            ";
            
            $stmt = $conn->prepare($locationQuery);
            $stmt->execute([
                'stall_id' => $stallId,
                'address' => $application['location']
            ]);
            
            // Commit transaction
            $conn->commit();
            
            return $stallId;
            
        } catch (PDOException $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error approving application: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's applications
     * 
     * @param int $userId User ID
     * @return array Applications
     */
    public static function getUserApplications(int $userId): array {
        try {
            $db = self::getDb();
            
            $query = "
                SELECT 
                    a.*,
                    ast.status_name,
                    ast.description as status_description
                FROM applications a
                LEFT JOIN approval_statuses ast ON a.current_status_id = ast.status_id
                WHERE a.user_id = :user_id
                ORDER BY a.created_at DESC
            ";
            
            return $db->query($query, ['user_id' => $userId]);
            
        } catch (PDOException $e) {
            error_log("Error getting user applications: " . $e->getMessage());
            return [];
        }
    }
}
