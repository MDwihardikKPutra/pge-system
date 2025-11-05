<?php

namespace App\Models;

use App\Enums\WorkLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkRealization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'realization_number',
        'user_id',
        'department',
        'work_plan_id',
        'project_id',
        'realization_date',
        'title',
        'description',
        'achievements',
        'output_description',
        'output_files',
        'work_location',
        'actual_duration_hours',
        'progress_percentage',
    ];

    protected function casts(): array
    {
        return [
            'realization_date' => 'date',
            'achievements' => 'array',
            'output_files' => 'array',
            'actual_duration_hours' => 'decimal:1',
            'work_location' => WorkLocation::class,
        ];
    }

    /**
     * Get the user (employee) who created this work realization
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
     * Get the related work plan
     */
    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('realization_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('realization_date', '<=', $endDate);
        }
        return $query;
    }
}
