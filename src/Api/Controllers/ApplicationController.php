<?php
/*
PROGRAM NAME: Stall Application API Controller (ApplicationController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all stall application-related API endpoints, managing the workflow for vendors
to apply for stall registration and for administrators to review and process these applications.
The ApplicationController works with the ApplicationService to track application status,
store application data, and facilitate the approval/rejection process.

This controller serves vendors submitting applications and administrators managing the
application review process.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide API endpoints for the stall application workflow.
It enables vendors to submit applications for new food stalls and allows administrators to
review, approve, or reject these applications. By centralizing application management, this
controller ensures a consistent review process, proper tracking of application status, and
clear communication between vendors and administrators.

DATA STRUCTURES:
- $applicationService (ApplicationService): Service instance for application operations.
- Application data:
  - application_id, user_id, stall_name, description, location, category (mixed).
  - status ('pending', 'approved', 'rejected').
  - business_license, contact_info, operating_hours (strings/arrays).
  - submitted_at, reviewed_at, reviewed_by (timestamps/IDs).
- Rejection data:
  - reason (string): Explanation for application rejection.
- Query parameters:
  - status (string): Filter applications by status.
  - page, limit (int): Pagination parameters.

ALGORITHM / LOGIC:
1. Initialize ApplicationService in constructor.
2. Route requests based on HTTP method and action:
   a. GET:
      - /applications → get applications (all for admin, user's own otherwise).
      - /applications/{id} → get specific application.
   b. POST:
      - /applications → create new application.
      - /applications/{id}/approve → approve application (admin only).
      - /applications/{id}/reject → reject application (admin only).
   c. PUT:
      - /applications/{id} → update application.
3. Get applications:
   a. Require authentication.
   b. Extract status filter and pagination parameters.
   c. If user is admin, retrieve all applications with optional status filter.
   d. If regular user, retrieve only their own applications.
   e. Return paginated application list.
4. Get single application:
   a. Require authentication.
   b. Call ApplicationService to retrieve application.
   c. Verify user has permission to view (owner or admin).
   d. Return application data or 404 if not found.
5. Create application:
   a. Require authentication.
   b. Add current user ID to application data.
   c. Call ApplicationService to create application.
   d. Set initial status to 'pending'.
   e. Return created application or error.
6. Update application:
   a. Require authentication.
   b. Verify application is still pending.
   c. Verify user owns the application.
   d. Call ApplicationService to update application.
   e. Return updated application or error.
7. Approve application:
   a. Require admin privileges.
   b. Call ApplicationService to approve application.
   c. Create corresponding stall record.
   d. Send notification to applicant.
   e. Return success or error.
8. Reject application:
   a. Require admin privileges.
   b. Validate rejection reason.
   c. Call ApplicationService to reject application.
   d. Store rejection reason.
   e. Send notification to applicant.
   f. Return success or error.

NOTES:
- Only authenticated users can submit applications.
- Users can only view and edit their own applications.
- Administrators can view and manage all applications.
- Applications can only be edited while in 'pending' status.
- Approval creates a new active stall in the system.
- Rejection requires a reason that is communicated to the applicant.
- Application workflow prevents duplicate submissions.
- Future enhancements may include:
  - Document upload support
  - Multi-step application process
  - Application revision requests
  - Automated application validation
  - Application analytics and reporting
  - Batch application processing
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\ApplicationService;

class ApplicationController extends BaseController
{
    private $applicationService;
    
    public function __construct()
    {
        parent::__construct();
        $this->applicationService = new ApplicationService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getApplication($id);
                } else {
                    $this->getApplications();
                }
                break;
            case 'POST':
                if ($action === 'approve') {
                    $this->approveApplication($id);
                } elseif ($action === 'reject') {
                    $this->rejectApplication($id);
                } else {
                    $this->createApplication();
                }
                break;
            case 'PUT':
                if ($id) {
                    $this->updateApplication($id);
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getApplications()
    {
        $this->requireAuth();
        
        $status = $_GET['status'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        
        if ($this->isAdmin()) {
            $applications = $this->applicationService->getAllApplications($status, $page, $limit);
            $total = $this->applicationService->countAllApplications($status);
        } else {
            $applications = $this->applicationService->getUserApplications($this->getCurrentUserId(), $page, $limit);
            $total = $this->applicationService->countUserApplications($this->getCurrentUserId());
        }
        
        ApiResponse::paginated($applications, $total, $page, $limit);
    }
    
    private function getApplication($id)
    {
        $this->requireAuth();
        
        $application = $this->applicationService->getApplicationById($id);
        
        if (!$application) {
            ApiResponse::error('Application not found', 404);
        }
        
        ApiResponse::success($application);
    }
    
    private function createApplication()
    {
        $this->requireAuth();
        
        $data = $this->requestBody;
        $data['user_id'] = $this->getCurrentUserId();
        
        $result = $this->applicationService->createApplication($data);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function updateApplication($id)
    {
        $this->requireAuth();
        
        $result = $this->applicationService->updateApplication($id, $this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 200, $result['message']);
    }
    
    private function approveApplication($id)
    {
        $this->requireAdmin();
        
        $result = $this->applicationService->approveApplication($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function rejectApplication($id)
    {
        $this->requireAdmin();
        $this->validateRequired(['reason']);
        
        $result = $this->applicationService->rejectApplication($id, $this->requestBody['reason']);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
