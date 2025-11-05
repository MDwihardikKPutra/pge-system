<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SPD extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spd';

    protected $fillable = [
        'spd_number',
        'user_id',
        'project_id',
        'destination',
        'departure_date',
        'return_date',
        'purpose',
        'transport_cost',
        'accommodation_cost',
        'meal_cost',
        'other_cost',
        'other_cost_description',
        'total_cost',
        'status',
        'notes',
        'costs',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'pdf_path',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'transport_cost' => 'decimal:2',
        'accommodation_cost' => 'decimal:2',
        'meal_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'status' => ApprovalStatus::class,
        'costs' => 'array', // JSON cast for costs array
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
