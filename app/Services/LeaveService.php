<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Enums\ApprovalStatus;
use Carbon\Carbon;

class LeaveService
{
    /**
     * Generate Leave Request number
     * Format: LV-YYYYMMDD-XXXX
     */
    public function generateLeaveRequestNumber(): string
    {
        $date = date('Ymd');
        $lastRequest = LeaveRequest::whereDate('created_at', today())
            ->latest('leave_number')
            ->first();

        if ($lastRequest && $lastRequest->leave_number) {
            $lastNumber = (int) substr($lastRequest->leave_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'LV-' . $date . '-' . $newNumber;
    }

    /**
     * Calculate total days between dates (excluding weekends)
     */
    public function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $totalDays = 0;
        while ($start->lte($end)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (!in_array($start->dayOfWeek, [0, 6])) {
                $totalDays++;
            }
            $start->addDay();
        }

        return $totalDays;
    }

    /**
     * Create Leave Request
     */
    public function createLeaveRequest(array $data, int $userId): LeaveRequest
    {
        $data['user_id'] = $userId;
        $data['leave_number'] = $this->generateLeaveRequestNumber();
        $data['status'] = ApprovalStatus::PENDING;

        // Calculate total days if not provided
        if (!isset($data['total_days'])) {
            $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        }

        // Handle file upload
        if (isset($data['attachment'])) {
            $data['attachment_path'] = $data['attachment']->store('leave-requests', 'public');
            unset($data['attachment']);
        }

        return LeaveRequest::create($data);
    }

    /**
     * Update Leave Request status
     */
    public function updateLeaveRequestStatus(LeaveRequest $leaveRequest, ApprovalStatus $status, ?string $notes = null, ?string $rejectionReason = null): LeaveRequest
    {
        $updateData = [
            'status' => $status,
            'approved_by' => auth()->id(),
            'approved_at' => $status === ApprovalStatus::APPROVED ? now() : null,
        ];

        if ($notes) {
            $updateData['admin_notes'] = $notes;
        }

        if ($rejectionReason) {
            $updateData['rejection_reason'] = $rejectionReason;
        }

        $leaveRequest->update($updateData);
        
        // Send notification to user who submitted
        $leaveRequest->refresh()->load('user');
        $approver = auth()->user();
        
        if ($leaveRequest->user) {
            $leaveRequest->user->notify(new \App\Notifications\SubmissionStatusNotification(
                $leaveRequest,
                'leave',
                $status->value,
                $approver
            ));
        }

        return $leaveRequest;
    }

    /**
     * Get leave statistics
     */
    public function getLeaveStats(): array
    {
        return [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', ApprovalStatus::PENDING)->count(),
            'approved' => LeaveRequest::where('status', ApprovalStatus::APPROVED)->count(),
            'rejected' => LeaveRequest::where('status', ApprovalStatus::REJECTED)->count(),
        ];
    }

    /**
     * Get user leave statistics
     */
    public function getUserLeaveStats(int $userId): array
    {
        return [
            'pending_requests' => LeaveRequest::where('user_id', $userId)
                ->where('status', ApprovalStatus::PENDING)
                ->count(),
            'approved_requests' => LeaveRequest::where('user_id', $userId)
                ->where('status', ApprovalStatus::APPROVED)
                ->count(),
            'rejected_requests' => LeaveRequest::where('user_id', $userId)
                ->where('status', ApprovalStatus::REJECTED)
                ->count(),
        ];
    }
}

