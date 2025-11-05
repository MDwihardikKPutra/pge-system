<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'leave_number',
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'admin_notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'attachment_path',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
            'status' => ApprovalStatus::class,
        ];
    }

    /**
     * Get the user that owns the leave request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type for the leave request.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the admin who approved the leave request.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if the leave request is pending.
     */
    public function isPending()
    {
        return $this->status === ApprovalStatus::PENDING;
    }

    /**
     * Check if the leave request is approved.
     */
    public function isApproved()
    {
        return $this->status === ApprovalStatus::APPROVED;
    }

    /**
     * Check if the leave request is rejected.
     */
    public function isRejected()
    {
        return $this->status === ApprovalStatus::REJECTED;
    }
}
