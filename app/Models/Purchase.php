<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_number',
        'user_id',
        'project_id',
        'type',
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'category',
        'status',
        'notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'pdf_path',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'approved_at' => 'datetime',
        'status' => ApprovalStatus::class,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
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
