<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Enums\WorkLocation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkDataSeeder extends Seeder
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

        // Create Work Plans
        $workPlanTitles = [
            'Survey Lokasi Proyek',
            'Pengukuran Tanah',
            'Penyiapan Material',
            'Pengecoran Beton',
            'Instalasi Pipa',
            'Pemasangan Struktur',
            'Finishing Bangunan',
            'Quality Control',
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < 4; $i++) {
                $planDate = Carbon::now()->subDays(rand(0, 30));
                WorkPlan::create([
                    'work_plan_number' => 'WP-' . strtoupper(Str::random(8)),
                    'user_id' => $user->id,
                    'department' => $user->department,
                    'project_id' => $projects->random()->id,
                    'plan_date' => $planDate,
                    'title' => $workPlanTitles[array_rand($workPlanTitles)],
                    'description' => 'Deskripsi lengkap untuk ' . $workPlanTitles[array_rand($workPlanTitles)] . ' yang akan dilakukan pada tanggal ' . $planDate->format('d M Y'),
                    'objectives' => ['Menyelesaikan tugas dengan baik', 'Memenuhi standar kualitas', 'Menyelesaikan tepat waktu'],
                    'work_location' => WorkLocation::cases()[array_rand(WorkLocation::cases())]->value,
                    'planned_duration_hours' => rand(4, 8),
                ]);
            }
        }

        // Create Work Realizations
        $realizationTitles = [
            'Realisasi Survey Lokasi',
            'Realisasi Pengukuran',
            'Realisasi Penyiapan Material',
            'Realisasi Pengecoran',
            'Realisasi Instalasi',
            'Realisasi Pemasangan',
            'Realisasi Finishing',
            'Realisasi QC',
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $realizationDate = Carbon::now()->subDays(rand(0, 25));
                WorkRealization::create([
                    'realization_number' => 'WR-' . strtoupper(Str::random(8)),
                    'user_id' => $user->id,
                    'department' => $user->department,
                    'project_id' => $projects->random()->id,
                    'realization_date' => $realizationDate,
                    'title' => $realizationTitles[array_rand($realizationTitles)],
                    'description' => 'Deskripsi realisasi kerja yang telah dilakukan pada tanggal ' . $realizationDate->format('d M Y'),
                    'work_location' => WorkLocation::cases()[array_rand(WorkLocation::cases())]->value,
                    'actual_duration_hours' => rand(4, 8),
                    'progress_percentage' => rand(50, 100),
                ]);
            }
        }

        $this->command->info('✅ Work Plans seeded: ' . WorkPlan::count());
        $this->command->info('✅ Work Realizations seeded: ' . WorkRealization::count());
    }
}

