<?php

namespace App\Models;

use App\Enums\WorkLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'work_plan_number',
        'user_id',
        'department',
        'project_id',
        'plan_date',
        'title',
        'description',
        'objectives',
        'expected_output',
        'work_location',
        'planned_duration_hours',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'objectives' => 'array',
            'planned_duration_hours' => 'decimal:1',
            'work_location' => WorkLocation::class,
        ];
    }

    /**
     * Get the user (employee) who created this work plan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get related work realizations
     */
    public function realizations()
    {
        return $this->hasMany(WorkRealization::class);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('plan_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('plan_date', '<=', $endDate);
        }
        return $query;
    }
}
