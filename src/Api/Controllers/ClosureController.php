<?php
/*
PROGRAM NAME: Account Closure Request API Controller (ClosureController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all account closure request-related API endpoints, managing the workflow for users
to request the closure of their accounts and for administrators to review and process these
closure requests.
The ClosureController works with the AccountClosureService to track closure request status,
validate closure eligibility, and facilitate the approval/rejection process.

This controller serves users requesting account deletion and administrators managing the
account closure review process.

DATE CREATED: December 23, 2025
LAST MODIFIED: December 23, 2025

PURPOSE:
The purpose of this program is to provide API endpoints for the account closure workflow.
It enables users to submit requests to close their accounts and allows administrators to
review and process these requests. By requiring admin approval for account closures, this
controller ensures proper validation of closure eligibility (checking for outstanding issues,
active stalls, pending transactions) and maintains data integrity throughout the process.

DATA STRUCTURES:
- $closureService (AccountClosureService): Service instance for closure operations.
- Closure request data:
  - closure_id, user_id (IDs).
  - reason (string): User's reason for requesting closure.
  - status ('pending', 'approved', 'rejected').
  - has_active_stalls (boolean): Whether user owns active stalls.
  - has_pending_reviews (boolean): Whether user has review reports.
  - submitted_at, reviewed_at, reviewed_by (timestamps/IDs).
  - admin_notes (string): Administrator's notes on the closure.
- Query parameters:
  - page, limit (int): Pagination parameters.

ALGORITHM / LOGIC:
1. Initialize AccountClosureService in constructor.
2. Route requests based on HTTP method and action:
   a. GET:
      - /closures → get paginated list of closure requests.
      - /closures/{id} → get specific closure request.
   b. POST:
      - /closures → create new closure request.
      - /closures/{id}/approve → approve closure (admin only).
      - /closures/{id}/reject → reject closure (admin only).
3. Get closures:
   a. Require authentication.
   b. Extract pagination parameters.
   c. If admin, retrieve all closure requests.
   d. If regular user, retrieve only their own requests.
   e. Return paginated closure request list.
4. Get single closure:
   a. Require authentication.
   b. Call AccountClosureService to retrieve closure request.
   c. Verify user has permission to view (owner or admin).
   d. Return closure data or 404 if not found.
5. Create closure:
   a. Require authentication.
   b. Validate user doesn't have pending closure requests.
   c. Check for active stalls owned by user.
   d. Check for pending reviews or reports.
   e. Call AccountClosureService to create closure request.
   f. Set initial status to 'pending'.
   g. Return created closure request or error.
6. Approve closure:
   a. Require admin privileges.
   b. Verify all prerequisites are met (no active stalls, no pending issues).
   c. Call AccountClosureService to approve closure.
   d. Deactivate user account.
   e. Archive or anonymize user data as per policy.
   f. Update closure status to 'approved'.
   g. Send confirmation to user.
   h. Return success or error.
7. Reject closure:
   a. Require admin privileges.
   b. Call AccountClosureService to reject closure.
   c. Update closure status to 'rejected'.
   d. Store rejection reason in admin notes.
   e. Send notification to user with reason.
   f. Return success or error.

NOTES:
- Users can only close their own accounts.
- Accounts with active stalls cannot be closed until stalls are transferred or closed.
- Multiple closure requests from the same user are prevented.
- Approved closures deactivate accounts but preserve data for legal/audit purposes.
- Account closure is permanent and cannot be undone.
- Users are notified of closure request status changes.
- Admin review ensures proper handling of user data and outstanding obligations.
- Future enhancements may include:
  - Self-service account closure for accounts without dependencies
  - Data export before closure
  - Temporary account suspension as alternative
  - Automated eligibility checking
  - Grace period for closure cancellation
  - Closure analytics and reasons tracking
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\AccountClosureService;

class ClosureController extends BaseController
{
    private $closureService;
    
    public function __construct()
    {
        parent::__construct();
        $this->closureService = new AccountClosureService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getClosure($id);
                } else {
                    $this->getClosures();
                }
                break;
            case 'POST':
                if ($action === 'approve') {
                    $this->approveClosure($id);
                } elseif ($action === 'reject') {
                    $this->rejectClosure($id);
                } else {
                    $this->createClosure();
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getClosures()
    {
        $this->requireAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        
        $closures = $this->closureService->getClosures($page, $limit);
        $total = $this->closureService->countClosures();
        
        ApiResponse::paginated($closures, $total, $page, $limit);
    }
    
    private function getClosure($id)
    {
        $this->requireAuth();
        
        $closure = $this->closureService->getClosureById($id);
        
        if (!$closure) {
            ApiResponse::error('Closure request not found', 404);
        }
        
        ApiResponse::success($closure);
    }
    
    private function createClosure()
    {
        $this->requireAuth();
        
        $result = $this->closureService->createClosure($this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function approveClosure($id)
    {
        $this->requireAdmin();
        
        $result = $this->closureService->approveClosure($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function rejectClosure($id)
    {
        $this->requireAdmin();
        
        $result = $this->closureService->rejectClosure($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
