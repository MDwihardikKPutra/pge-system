<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SPD;
use App\Constants\ProjectAccessType;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'client',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function spd()
    {
        return $this->hasMany(SPD::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function workPlans()
    {
        return $this->hasMany(WorkPlan::class);
    }

    public function workRealizations()
    {
        return $this->hasMany(WorkRealization::class);
    }

    /**
     * Get users who are Project Managers of this project
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'project_managers')
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Get users with specific access type
     */
    public function managersByAccessType($accessType)
    {
        return $this->belongsToMany(User::class, 'project_managers')
            ->wherePivot('access_type', $accessType)
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Check if a user is Project Manager of this project
     */
    public function hasManager($userId): bool
    {
        return $this->managers()->where('users.id', $userId)->exists();
    }

    /**
     * Get user's access type for this project
     * Returns: 'pm', 'finance', 'full', or null if not a manager
     */
    public function getManagerAccessType($userId): ?string
    {
        $manager = $this->managers()->where('users.id', $userId)->first();
        if (!$manager) {
            return null;
        }
        
        // Fallback to PM if access_type is null (for old data before migration)
        return $manager->pivot->access_type ?? ProjectAccessType::PM;
    }

    /**
     * Check if user has access to work plans/realizations (PM or Full)
     */
    public function canAccessWork($userId): bool
    {
        $accessType = $this->getManagerAccessType($userId);
        return $accessType && ProjectAccessType::canAccessWork($accessType);
    }

    /**
     * Check if user has access to payments (Finance or Full)
     */
    public function canAccessPayments($userId): bool
    {
        $accessType = $this->getManagerAccessType($userId);
        return $accessType && ProjectAccessType::canAccessPayments($accessType);
    }

    /**
     * Scope untuk mengambil project yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengambil project yang diurutkan berdasarkan nama
     */
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('name');
    }
}
