<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;

class ProjectManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        if ($users->isEmpty() || $projects->isEmpty()) {
            $this->command->warn('⚠️  Users or Projects not found. Please run UserSeeder and ProjectSeeder first.');
            return;
        }

        // Assign Project Managers with different access types
        if (isset($users[1]) && $projects->count() > 0) {
            // Budi (PM) - Full Access to first project
            $projects[0]->managers()->syncWithoutDetaching([
                $users[1]->id => ['access_type' => 'full']
            ]);
        }
        if (isset($users[2]) && $projects->count() > 1) {
            // Siti (Senior Engineer) - PM Access to second project
            $projects[1]->managers()->syncWithoutDetaching([
                $users[2]->id => ['access_type' => 'pm']
            ]);
        }
        if (isset($users[4]) && $projects->count() > 2) {
            // Dewi (Finance) - Finance Access to third project
            $projects[2]->managers()->syncWithoutDetaching([
                $users[4]->id => ['access_type' => 'finance']
            ]);
        }
        if (isset($users[1]) && $projects->count() > 3) {
            // Budi juga PM di project keempat
            $projects[3]->managers()->syncWithoutDetaching([
                $users[1]->id => ['access_type' => 'pm']
            ]);
        }

        $this->command->info('✅ Project Managers assigned');
    }
}





