<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Cuti Tahunan',
                'description' => 'Cuti tahunan regular untuk karyawan',
                'max_days' => 12,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cuti Sakit',
                'description' => 'Cuti untuk sakit atau keperluan medis',
                'max_days' => 10,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Pribadi',
                'description' => 'Izin untuk keperluan pribadi mendesak',
                'max_days' => 5,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cuti Melahirkan',
                'description' => 'Cuti melahirkan untuk karyawan perempuan',
                'max_days' => 90,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cuti Menikah',
                'description' => 'Cuti untuk keperluan pernikahan',
                'max_days' => 3,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cuti Besar',
                'description' => 'Cuti besar untuk karyawan dengan masa kerja tertentu',
                'max_days' => 30,
                'requires_approval' => true,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                ['name' => $leaveType['name']],
                $leaveType
            );
        }
    }
}
