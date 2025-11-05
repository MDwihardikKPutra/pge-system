<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders dalam urutan yang benar
        $this->call([
            ModuleSeeder::class,              // 1. Sync modules dari config
            RolesAndPermissionsSeeder::class, // 2. Buat roles & permissions
            LeaveTypeSeeder::class,           // 3. Seed leave types
            ProjectSeeder::class,             // 4. Seed projects
            ChangelogSeeder::class,           // 5. Seed changelog entries
        ]);
        
        // Seed full sample data (users, vendors, work plans, realizations, payments, leaves)
        $this->call([FullDataSeeder::class]);

        // Buat user admin default jika belum ada
        if (!User::where('email', 'admin@pge.local')->exists()) {
            $admin = User::create([
                'name' => 'Admin PGE',
                'email' => 'admin@pge.local',
                'password' => Hash::make('password'),
                'employee_id' => 'ADM001',
                'department' => 'IT',
                'position' => 'Administrator',
                'is_active' => true,
            ]);
            $admin->assignRole('admin');
            $this->command->info('✅ Default admin user created');
            $this->command->info('   Email: admin@pge.local');
            $this->command->info('   Password: password');
        } else {
            // Pastikan user admin punya role admin
            $admin = User::where('email', 'admin@pge.local')->first();
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
        }

        // Buat user test default jika belum ada
        if (!User::where('email', 'user@pge.local')->exists()) {
            $user = User::create([
                'name' => 'User Test',
                'email' => 'user@pge.local',
                'password' => Hash::make('password'),
                'employee_id' => 'USR001',
                'department' => 'Operations',
                'position' => 'Staff',
                'is_active' => true,
            ]);
            $user->assignRole('user');
            $this->command->info('✅ Default user created');
            $this->command->info('   Email: user@pge.local');
            $this->command->info('   Password: password');
        } else {
            // Pastikan user punya role user
            $user = User::where('email', 'user@pge.local')->first();
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }
        }
    }
}
