<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Roles & Permissions Setup (2-role model: Admin, User)
     * - Admin: Full access ke semua modul dan fitur
     * - User: Default hanya punya work-plan dan work-realization
     * - Permissions granular untuk kontrol akses detail
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Starting Roles & Permissions Setup...');

        // ================================================================
        // STEP 1: CREATE PERMISSIONS (Granular & Scalable)
        // ================================================================
        
        $modulesConfig = config('modules.list', []);
        $defaultActions = config('modules.default_actions', ['view', 'create', 'update', 'delete']);

        $this->command->info('📝 Creating Permissions...');
        $permissionCount = 0;

        foreach ($modulesConfig as $module => $moduleData) {
            $actions = $moduleData['actions'] ?? $defaultActions;
            foreach ($actions as $action) {
                // Create permission dengan format: action-module
                // Example: view-work-plan, create-work-plan, approve-leave, etc.
                Permission::firstOrCreate(
                    [
                        'name' => "{$action}-{$module}",
                        'guard_name' => 'web',
                    ]
                );
                $permissionCount++;
            }
        }

        // Additional Special Permissions
        $specialPermissions = [
            'access-admin-panel',
            'access-user-panel',
            'manage-all-users',
            'view-all-reports',
            'export-all-data',
            'manage-system-settings',
        ];

        foreach ($specialPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            $permissionCount++;
        }

        $this->command->info("   ✅ Created {$permissionCount} permissions");

        // ================================================================
        // STEP 2: CREATE ROLES (Hanya 2 role: Admin dan User)
        // ================================================================
        
        $this->command->info('👥 Creating Roles...');

        // --------------------------------------------------
        // ADMIN ROLE (Full Access - Semua Permissions)
        // --------------------------------------------------
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all()); // Give ALL permissions
        $this->command->info('   ✅ Admin role created (ALL PERMISSIONS)');

        // --------------------------------------------------
        // USER ROLE (No default permissions - permissions diberikan via module assignment)
        // --------------------------------------------------
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            // Panel Access
            'access-user-panel',
            // Note: Module permissions (work-plan, work-realization, etc) diberikan saat module di-assign ke user
        ]);
        $this->command->info('   ✅ User role created (permissions via module assignment)');

        // ================================================================
        // STEP 3: ASSIGN ROLES TO EXISTING USERS
        // ================================================================
        
        $this->command->info('🔄 Assigning roles to existing users...');

        $users = User::all();
        $migrated = 0;

        foreach ($users as $user) {
            // Jika user belum punya role, assign default 'user'
            if ($user->roles->isEmpty()) {
                $user->assignRole('user');
                $migrated++;
            }
        }

        $this->command->info("   ✅ Processed {$migrated} users");

        // ================================================================
        // SUMMARY
        // ================================================================
        
        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Roles & Permissions Setup Complete!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 Summary:');
        $this->command->info("   - Permissions: {$permissionCount}");
        $this->command->info('   - Roles: 2 (Admin, User)');
        $this->command->info("   - Users Processed: {$migrated}");
        $this->command->info('');
        $this->command->info('📌 Note:');
        $this->command->info('   - Admin: Full access ke semua modul');
        $this->command->info('   - User: Default hanya work-plan & work-realization');
        $this->command->info('   - Admin dapat assign modul tambahan ke user via User Management');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
    }
}
