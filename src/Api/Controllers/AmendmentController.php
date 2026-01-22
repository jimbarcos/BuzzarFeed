<?php
/*
PROGRAM NAME: Amendment Request API Controller (AmendmentController.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform's API layer.
It handles all amendment request-related API endpoints, managing the workflow for stall owners
to request modifications to their existing stall information and for administrators to review
and process these amendment requests.
The AmendmentController works with the AmendmentRequestService to track amendment status,
store proposed changes, and facilitate the approval/rejection process.

This controller serves stall owners requesting changes and administrators managing the
amendment review process.

DATE CREATED: January 23, 2026
LAST MODIFIED: January 23, 2026

PURPOSE:
The purpose of this program is to provide API endpoints for the stall amendment workflow.
It enables stall owners to submit requests for changes to their stall information (such as
updating operating hours, contact information, or descriptions) and allows administrators to
review, approve, or reject these amendments. By requiring admin approval for changes, this
controller ensures data integrity and prevents unauthorized modifications.

DATA STRUCTURES:
- $amendmentService (AmendmentRequestService): Service instance for amendment operations.
- Amendment request data:
  - amendment_id, stall_id, user_id (IDs).
  - field_name (string): The field being amended (e.g., 'description', 'operating_hours').
  - old_value, new_value (strings): Previous and proposed values.
  - status ('pending', 'approved', 'rejected').
  - reason (string): Explanation for the amendment.
  - submitted_at, reviewed_at, reviewed_by (timestamps/IDs).
- Query parameters:
  - page, limit (int): Pagination parameters.

ALGORITHM / LOGIC:
1. Initialize AmendmentRequestService in constructor.
2. Route requests based on HTTP method and action:
   a. GET:
      - /amendments → get paginated list of amendments.
      - /amendments/{id} → get specific amendment request.
   b. POST:
      - /amendments → create new amendment request.
      - /amendments/{id}/approve → approve amendment (admin only).
      - /amendments/{id}/reject → reject amendment (admin only).
3. Get amendments:
   a. Require authentication.
   b. Extract pagination parameters.
   c. Call AmendmentRequestService to retrieve amendments.
   d. Filter by user if not admin (only show own amendments).
   e. Return paginated amendment list.
4. Get single amendment:
   a. Require authentication.
   b. Call AmendmentRequestService to retrieve amendment.
   c. Verify user has permission to view (owner or admin).
   d. Return amendment data or 404 if not found.
5. Create amendment:
   a. Require authentication.
   b. Verify user owns the stall being amended.
   c. Validate amendment data (field name, new value).
   d. Call AmendmentRequestService to create amendment request.
   e. Set initial status to 'pending'.
   f. Return created amendment or error.
6. Approve amendment:
   a. Require admin privileges.
   b. Call AmendmentRequestService to approve amendment.
   c. Apply the amendment to the actual stall record.
   d. Update amendment status to 'approved'.
   e. Send notification to stall owner.
   f. Return success or error.
7. Reject amendment:
   a. Require admin privileges.
   b. Call AmendmentRequestService to reject amendment.
   c. Update amendment status to 'rejected'.
   d. Send notification to stall owner.
   e. Return success or error.

NOTES:
- Only stall owners can submit amendment requests for their stalls.
- Amendments require admin approval before being applied.
- Multiple pending amendments can exist for the same stall.
- Amendment history is preserved for audit purposes.
- Approved amendments automatically update the stall record.
- Rejected amendments do not affect the stall data.
- Stall owners are notified of amendment status changes.
- Future enhancements may include:
  - Batch amendment approval
  - Amendment comparison view
  - Automatic approval for minor changes
  - Amendment templates for common changes
  - Revision history tracking
  - Amendment analytics
*/

namespace BuzzarFeed\Api\Controllers;

use BuzzarFeed\Utils\ApiResponse;
use BuzzarFeed\Services\AmendmentRequestService;

class AmendmentController extends BaseController
{
    private $amendmentService;
    
    public function __construct()
    {
        parent::__construct();
        $this->amendmentService = new AmendmentRequestService();
    }
    
    public function handleRequest($method, $id = null, $action = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getAmendment($id);
                } else {
                    $this->getAmendments();
                }
                break;
            case 'POST':
                if ($action === 'approve') {
                    $this->approveAmendment($id);
                } elseif ($action === 'reject') {
                    $this->rejectAmendment($id);
                } else {
                    $this->createAmendment();
                }
                break;
            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }
    
    private function getAmendments()
    {
        $this->requireAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        
        $amendments = $this->amendmentService->getAmendments($page, $limit);
        $total = $this->amendmentService->countAmendments();
        
        ApiResponse::paginated($amendments, $total, $page, $limit);
    }
    
    private function getAmendment($id)
    {
        $this->requireAuth();
        
        $amendment = $this->amendmentService->getAmendmentById($id);
        
        if (!$amendment) {
            ApiResponse::error('Amendment not found', 404);
        }
        
        ApiResponse::success($amendment);
    }
    
    private function createAmendment()
    {
        $this->requireAuth();
        
        $result = $this->amendmentService->createAmendment($this->requestBody);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success($result['data'], 201, $result['message']);
    }
    
    private function approveAmendment($id)
    {
        $this->requireAdmin();
        
        $result = $this->amendmentService->approveAmendment($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
    
    private function rejectAmendment($id)
    {
        $this->requireAdmin();
        
        $result = $this->amendmentService->rejectAmendment($id);
        
        if (!$result['success']) {
            ApiResponse::error($result['message'], 400);
        }
        
        ApiResponse::success(null, 200, $result['message']);
    }
}
