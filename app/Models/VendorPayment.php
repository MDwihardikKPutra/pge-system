<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'user_id',
        'vendor_id',
        'project_id',
        'invoice_number',
        'po_number',
        'amount',
        'description',
        'payment_type',
        'payment_date',
        'status',
        'notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'status' => ApprovalStatus::class,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === ApprovalStatus::PENDING;
    }

    public function isApproved()
    {
        return $this->status === ApprovalStatus::APPROVED;
    }

    public function isRejected()
    {
        return $this->status === ApprovalStatus::REJECTED;
    }
}
