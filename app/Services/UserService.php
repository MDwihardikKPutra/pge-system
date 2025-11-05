<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return User::with('modules')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
        ];
    }

    /**
     * Generate unique employee ID
     */
    public function generateEmployeeId(): string
    {
        $year = date('Y');
        $lastEmployee = User::whereYear('created_at', $year)
            ->whereNotNull('employee_id')
            ->latest('employee_id')
            ->first();

        if ($lastEmployee && $lastEmployee->employee_id) {
            $lastNumber = (int) substr($lastEmployee->employee_id, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'EMP-' . $year . '-' . $newNumber;
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        // Generate employee_id if not provided
        if (empty($data['employee_id'])) {
            $data['employee_id'] = $this->generateEmployeeId();
        }

        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set defaults
        $data['is_active'] = $data['is_active'] ?? true;
        $data['remaining_leave'] = $data['annual_leave_quota'] ?? 12;

        $user = User::create($data);

        // Assign Spatie role (admin or user)
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        // Sync modules for user role
        if (($data['role'] ?? 'user') === 'user') {
            // Default modules untuk user baru (work-plan & work-realization)
            $defaultModules = ['work-plan', 'work-realization'];
            $modules = !empty($data['modules']) && is_array($data['modules']) 
                ? $data['modules'] 
                : $defaultModules; // Default modules jika tidak ada yang dipilih
            $this->syncUserModules($user, $modules);
        }

        // Clear cache yang terkait dengan user
        CacheHelper::clearProjectManagementUsers();
        CacheHelper::clearDashboardUsersList();

        return $user;
    }

    /**
     * Update existing user
     */
    public function updateUser(User $user, array $data): User
    {
        // Hash password only if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Update remaining leave if quota changes
        if (isset($data['annual_leave_quota']) && $data['annual_leave_quota'] != $user->annual_leave_quota) {
            $difference = $data['annual_leave_quota'] - $user->annual_leave_quota;
            $data['remaining_leave'] = $user->remaining_leave + $difference;
        }

        $user->update($data);

        // Sync Spatie role if changed
        if (!empty($data['role'])) {
            $user->syncRoles($data['role']);
        }

        // Sync modules for user role
        $currentRole = $data['role'] ?? $user->getRoleNames()->first();
        if ($currentRole === 'user') {
            // Default modules untuk user baru (work-plan & work-realization)
            $defaultModules = ['work-plan', 'work-realization'];
            
            if (array_key_exists('modules', $data)) {
                $modules = is_array($data['modules']) ? $data['modules'] : [];
                $this->syncUserModules($user, $modules);
            }
            // Jika role berubah dari admin ke user, set default modules
            elseif ($user->getRoleNames()->first() === 'admin' && $currentRole === 'user') {
                $this->syncUserModules($user, $defaultModules);
            }
        }

        // Clear cache yang terkait dengan user
        CacheHelper::clearProjectManagementUsers();
        CacheHelper::clearDashboardUsersList();

        return $user->fresh();
    }

    /**
     * Sync user modules to database (module_user pivot table)
     * Also syncs permissions based on module assignments
     * 
     * Work-plan and work-realization are now assignable modules with default checked
     */
    protected function syncUserModules(User $user, array $moduleKeys): void
    {
        // Define CRUD actions to grant for each module
        $defaultActions = ['view', 'create', 'update', 'delete'];
        
        // Default modules that user role always has (work-plan & work-realization)
        $defaultModules = ['work-plan', 'work-realization'];

        // Build target permission names based on selected modules
        $targetPermissions = [];
        
        // Always include default modules for user role
        foreach ($defaultModules as $moduleKey) {
            $module = Module::where('key', $moduleKey)->first();
            if ($module) {
                $actions = $module->actions ?? $defaultActions;
                foreach ($actions as $action) {
                    $targetPermissions[] = "{$action}-{$moduleKey}";
                }
            }
        }
        
        // Add selected additional modules (excluding defaults to avoid duplicates)
        foreach ($moduleKeys as $moduleKey) {
            if (!in_array($moduleKey, $defaultModules)) {
                $module = Module::where('key', $moduleKey)->first();
                if ($module) {
                    $actions = $module->actions ?? $defaultActions;
                    foreach ($actions as $action) {
                        $targetPermissions[] = "{$action}-{$moduleKey}";
                    }
                }
            }
        }

        // Sync permissions (Spatie Permission)
        // Ensure all permissions exist before syncing
        foreach ($targetPermissions as $permissionName) {
            \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }
        $user->syncPermissions($targetPermissions);

        // Get module IDs to sync
        // Include default modules + selected modules
        $allModuleKeys = array_unique(array_merge($defaultModules, $moduleKeys));
        $moduleIds = [];
        foreach ($allModuleKeys as $moduleKey) {
            $module = Module::where('key', $moduleKey)->first();
            if ($module) {
                $moduleIds[] = $module->id;
            }
        }
        
        // Sync modules (module_user pivot table) - including defaults + selected modules
        $user->modules()->sync($moduleIds);
    }

    /**
     * Delete user (with safety check)
     */
    public function deleteUser(User $user, int $currentUserId): bool
    {
        // Prevent self-deletion
        if ($user->id === $currentUserId) {
            throw new \Exception('Anda tidak dapat menghapus akun sendiri.');
        }

        $result = $user->delete();

        // Clear cache yang terkait dengan user
        CacheHelper::clearProjectManagementUsers();
        CacheHelper::clearDashboardUsersList();

        return $result;
    }

    /**
     * Validate user data
     */
    public function validateUserData(array $data, ?int $userId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($userId ? ",$userId" : ''),
            'role' => 'required|in:admin,user',
            'employee_id' => 'nullable|string|unique:users,employee_id' . ($userId ? ",$userId" : ''),
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
            'annual_leave_quota' => 'nullable|integer|min:0|max:30',
            'remaining_leave' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'modules' => 'sometimes|array',
            'modules.*' => 'string',
        ];

        // Password required for new users
        if (!$userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        return $rules;
    }
}

