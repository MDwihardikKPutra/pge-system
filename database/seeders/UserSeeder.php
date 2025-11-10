<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing Admin User (created by DatabaseSeeder)
        $admin = User::where('email', 'admin@pge.local')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin PGE',
                'email' => 'admin@pge.local',
                'password' => Hash::make('password'),
                'is_active' => true,
                'employee_id' => 'ADM001',
                'position' => 'Administrator',
                'department' => 'IT',
            ]);
            $admin->assignRole('admin');
        }

        // Create Regular Users (skip if already exists from previous seed)
        $userData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@pge.local', 'employee_id' => 'EMP002', 'position' => 'Project Manager', 'department' => 'Engineering'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@pge.local', 'employee_id' => 'EMP003', 'position' => 'Senior Engineer', 'department' => 'Engineering'],
            ['name' => 'Ahmad Yani', 'email' => 'ahmad@pge.local', 'employee_id' => 'EMP004', 'position' => 'Engineer', 'department' => 'Engineering'],
            ['name' => 'Dewi Sartika', 'email' => 'dewi@pge.local', 'employee_id' => 'EMP005', 'position' => 'Finance Officer', 'department' => 'Finance'],
            ['name' => 'Rizki Pratama', 'email' => 'rizki@pge.local', 'employee_id' => 'EMP006', 'position' => 'Procurement Officer', 'department' => 'Procurement'],
        ];

        $users = [$admin];
        
        foreach ($userData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'employee_id' => $data['employee_id'],
                    'position' => $data['position'],
                    'department' => $data['department'],
                ]
            );
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }
            $users[] = $user;
        }
        
        // Get existing user test if exists
        $userTest = User::where('email', 'user@pge.local')->first();
        if ($userTest) {
            $users[] = $userTest;
        }

        $this->command->info('✅ Users seeded: ' . count($users));
        
        // Store users in seeder cache for other seeders
        $this->command->getOutput()->getFormatter()->setDecorated(true);
    }

    /**
     * Get all users (helper method for other seeders)
     */
    public static function getUsers(): array
    {
        return User::all()->toArray();
    }
}

