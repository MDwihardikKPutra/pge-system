<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'phone',
        'department',
        'position',
        'join_date',
        'annual_leave_quota',
        'remaining_leave',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'join_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Modules (many-to-many relationship)
    public function modules()
    {
        return $this->belongsToMany(Module::class)->withTimestamps();
    }

    /**
     * Get active modules for user (default + assigned)
     */
    public function getActiveModules()
    {
        if ($this->hasRole('admin')) {
            // Admin punya semua modul aktif
            return Module::active()->orderBy('sort_order')->get();
        }

        // User: default modules + assigned modules
        $defaultModules = Module::default()->get();
        $assignedModules = $this->modules()->active()->get();
        
        return $defaultModules->merge($assignedModules)->unique('id')->sortBy('sort_order');
    }

    /**
     * Check if user has access to module
     */
    public function hasModuleAccess($moduleKey): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        $module = Module::where('key', $moduleKey)->first();
        if (!$module) {
            return false;
        }

        // Check if default module
        if ($module->is_default) {
            return true;
        }

        // Check if assigned to user
        return $this->modules()->where('modules.id', $module->id)->exists();
    }

    /**
     * Check if user has Admin role
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get projects where user is Project Manager
     */
    public function managedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_managers')
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Get projects where user has work access (PM or Full)
     */
    public function managedProjectsWithWorkAccess()
    {
        return $this->belongsToMany(Project::class, 'project_managers')
            ->wherePivotIn('access_type', ['pm', 'full'])
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Get projects where user has payment access (Finance or Full)
     */
    public function managedProjectsWithPaymentAccess()
    {
        return $this->belongsToMany(Project::class, 'project_managers')
            ->wherePivotIn('access_type', ['finance', 'full'])
            ->withPivot('access_type')
            ->withTimestamps();
    }

    /**
     * Check if user is Project Manager of a specific project
     */
    public function isProjectManager($projectId): bool
    {
        return $this->managedProjects()->where('projects.id', $projectId)->exists();
    }
}
