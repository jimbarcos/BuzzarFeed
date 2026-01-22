<?php
/*
PROGRAM NAME: Admin Operations API Controller (AdminController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all administrator-specific API endpoints including dashboard statistics retrieval,
admin activity logs, and review report management.
The AdminController works with various services (AdminLogService, Database) to provide
comprehensive administrative oversight capabilities and reporting functionality.

This controller serves exclusively administrators who need access to system-wide statistics,
activity logs, and moderation tools.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide API endpoints for administrative operations and oversight.
It enables administrators to monitor system activity, view platform statistics, access audit logs,
and manage reported content. By centralizing admin functionality, this controller ensures that
sensitive administrative data is properly protected and that administrators have the tools needed
to effectively manage the BuzzarFeed platform.

DATA STRUCTURES:
- $adminLogService (AdminLogService): Service instance for admin log operations.
- Dashboard statistics:
  - totalUsers (int): Total number of registered users.
  - totalStalls (int): Total number of active food stalls.
  - totalReviews (int): Total number of reviews in system.
  - pendingApplications (int): Count of pending stall applications.
  - pendingAmendments (int): Count of pending amendment requests.
  - pendingClosures (int): Count of pending closure requests.
- Admin log data:
  - log_id, admin_id, action, entity_type, entity_id (mixed).
  - timestamp, ip_address, user_agent (strings).
  - details (JSON): Additional context about the action.
- Review report data:
  - report_id, review_id, reported_by, reason, details (mixed).
  - review content, reporter info, stall info (joined data).
  - status ('pending', 'resolved', 'dismissed').
  - created_at (timestamp).
- Query parameters:
  - page, limit (int): Pagination parameters.

ALGORITHM / LOGIC:
1. Initialize AdminLogService in constructor.
2. Require admin privileges for ALL requests to this controller.
3. Route requests based on action parameter:
   a. GET /admin/dashboard → retrieve dashboard statistics.
   b. GET /admin/logs → retrieve admin activity logs.
   c. GET /admin/reports → retrieve review reports.
4. Get dashboard statistics:
   a. Query database for total users count.
   b. Query for total active stalls count.
   c. Query for total reviews count.
   d. Query for pending applications count.
   e. Query for pending amendments count.
   f. Query for pending closures count.
   g. Compile statistics into single object.
   h. Return statistics as JSON response.
5. Get admin logs:
   a. Extract pagination parameters.
   b. Call AdminLogService to retrieve logs.
   c. Count total log entries.
   d. Return paginated log list.
6. Get review reports:
   a. Extract pagination parameters.
   b. Query database for review reports with joins:
      - Include review content.
      - Include reporter user information.
      - Include associated stall information.
   c. Order by most recent reports first.
   d. Count total reports.
   e. Return paginated report list.

NOTES:
- All endpoints in this controller require admin authentication.
- Dashboard provides quick overview of platform health and pending tasks.
- Admin logs create an audit trail of administrative actions.
- Review reports enable content moderation workflow.
- Statistics are calculated in real-time from database queries.
- Sensitive user data is included since this is admin-only.
- Pagination prevents overwhelming response sizes.
- Future enhancements may include:
  - Real-time dashboard updates via WebSockets
  - Advanced analytics and trend reporting
  - Customizable dashboard widgets
  - Export functionality for logs and reports
  - Automated anomaly detection and alerts
  - Role-based admin permissions (super admin, moderator, etc.)
  - Admin action reversal capabilities
  - Bulk operations for reports and moderation
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\AdminLogService;
use BuzzarFeed\Utils\Database;

class AdminController extends BaseController
{
    private $adminLogService;
    
    public function __construct()
    {
        parent::__construct();
        $this->adminLogService = new AdminLogService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        $this->requireAdmin();
        
        switch ($action) {
            case 'dashboard':
                if ($method === 'GET') {
                    $this->getDashboardStats();
                }
                break;
            case 'logs':
                if ($method === 'GET') {
                    $this->getLogs();
                }
                break;
            case 'reports':
                if ($method === 'GET') {
                    $this->getReports();
                }
                break;
            default:
                ApiResponse::error('Invalid action', 404);
        }
    }
    
    private function getDashboardStats()
    {
        $db = Database::getInstance();
        
        // Get various statistics
        $stats = [
            'totalUsers' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'totalStalls' => $db->query("SELECT COUNT(*) FROM food_stalls WHERE status = 'active'")->fetchColumn(),
            'totalReviews' => $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
            'pendingApplications' => $db->query("SELECT COUNT(*) FROM stall_applications WHERE status = 'pending'")->fetchColumn(),
            'pendingAmendments' => $db->query("SELECT COUNT(*) FROM amendment_requests WHERE status = 'pending'")->fetchColumn(),
            'pendingClosures' => $db->query("SELECT COUNT(*) FROM account_closure_requests WHERE status = 'pending'")->fetchColumn(),
        ];
        
        ApiResponse::success($stats);
    }
    
    private function getLogs()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 50);
        
        $logs = $this->adminLogService->getLogs($page, $limit);
        $total = $this->adminLogService->countLogs();
        
        ApiResponse::paginated($logs, $total, $page, $limit);
    }
    
    private function getReports()
    {
        $db = Database::getInstance();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("
            SELECT rr.*, r.comment, u.first_name, u.last_name, fs.stall_name
            FROM review_reports rr
            JOIN reviews r ON rr.review_id = r.review_id
            JOIN users u ON rr.reported_by = u.user_id
            JOIN food_stalls fs ON r.stall_id = fs.stall_id
            ORDER BY rr.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        $reports = $stmt->fetchAll();
        
        $total = $db->query("SELECT COUNT(*) FROM review_reports")->fetchColumn();
        
        ApiResponse::paginated($reports, $total, $page, $limit);
    }
}
