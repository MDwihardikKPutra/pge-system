<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'PLTP Sarulla Expansion',
                'code' => 'PRJ-2024-001',
                'client' => 'PT Sarulla Operations Ltd',
                'description' => 'Proyek ekspansi pembangkit listrik tenaga panas bumi Sarulla untuk meningkatkan kapasitas produksi hingga 330 MW',
                'is_active' => true,
            ],
            [
                'name' => 'Geothermal Survey Sumatra',
                'code' => 'PRJ-2024-002',
                'client' => 'Kementerian ESDM',
                'description' => 'Survey dan eksplorasi potensi panas bumi di wilayah Sumatera Utara untuk pengembangan PLTP baru',
                'is_active' => true,
            ],
            [
                'name' => 'PLTP Lahendong Unit 6',
                'code' => 'PRJ-2024-003',
                'client' => 'PT Pertamina Geothermal Energy',
                'description' => 'Pembangunan unit pembangkit baru PLTP Lahendong dengan kapasitas 20 MW',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance PLTP Kamojang',
                'code' => 'PRJ-2024-004',
                'client' => 'PT Indonesia Power',
                'description' => 'Pemeliharaan rutin dan perbaikan sistem pembangkit PLTP Kamojang',
                'is_active' => true,
            ],
            [
                'name' => 'PLTP Dieng Development',
                'code' => 'PRJ-2024-005',
                'client' => 'PT Geo Dipa Energi',
                'description' => 'Pengembangan infrastruktur dan peningkatan efisiensi PLTP Dieng',
                'is_active' => true,
            ],
            [
                'name' => 'Training & Certification',
                'code' => 'PRJ-2024-006',
                'client' => 'Internal',
                'description' => 'Program pelatihan dan sertifikasi untuk operator dan teknisi PLTP',
                'is_active' => true,
            ],
            [
                'name' => 'Environmental Impact Assessment',
                'code' => 'PRJ-2024-007',
                'client' => 'PT PLN',
                'description' => 'Analisis dampak lingkungan untuk proyek PLTP baru di Flores',
                'is_active' => true,
            ],
            [
                'name' => 'PLTP Ulubelu Optimization',
                'code' => 'PRJ-2024-008',
                'client' => 'PT Pertamina Geothermal Energy',
                'description' => 'Optimasi sistem operasional dan peningkatan output PLTP Ulubelu',
                'is_active' => true,
            ],
            [
                'name' => 'Internal Kantor',
                'code' => 'PRJ-INTERNAL-001',
                'client' => 'Internal',
                'description' => 'Project untuk mencatat pembelian dan pengeluaran untuk keperluan kantor',
                'is_active' => true,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['code' => $project['code']],
                $project
            );
        }

        $this->command->info('✅ Projects seeded successfully!');
        $this->command->info('   Total projects: ' . count($projects));
    }
}
