<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\Spd;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\Vendor;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Enums\ApprovalStatus;
use App\Enums\WorkLocation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FullDataSeeder extends Seeder
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
        $users = [];
        $userData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@pge.local', 'employee_id' => 'EMP002', 'position' => 'Project Manager', 'department' => 'Engineering'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@pge.local', 'employee_id' => 'EMP003', 'position' => 'Senior Engineer', 'department' => 'Engineering'],
            ['name' => 'Ahmad Yani', 'email' => 'ahmad@pge.local', 'employee_id' => 'EMP004', 'position' => 'Engineer', 'department' => 'Engineering'],
            ['name' => 'Dewi Sartika', 'email' => 'dewi@pge.local', 'employee_id' => 'EMP005', 'position' => 'Finance Officer', 'department' => 'Finance'],
            ['name' => 'Rizki Pratama', 'email' => 'rizki@pge.local', 'employee_id' => 'EMP006', 'position' => 'Procurement Officer', 'department' => 'Procurement'],
        ];

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

        // Create Vendors
        $vendors = [];
        $vendorData = [
            ['name' => 'PT Teknologi Jaya', 'email' => 'info@teknologijaya.com', 'phone' => '021-12345678', 'address' => 'Jl. Sudirman No. 123, Jakarta'],
            ['name' => 'CV Material Bangunan', 'email' => 'sales@materialbangunan.com', 'phone' => '021-87654321', 'address' => 'Jl. Gatot Subroto No. 456, Jakarta'],
            ['name' => 'PT Konsultan Proyek', 'email' => 'contact@konsultanproyek.com', 'phone' => '021-11223344', 'address' => 'Jl. Thamrin No. 789, Jakarta'],
            ['name' => 'CV Logistik Sejahtera', 'email' => 'info@logistiksejahtera.com', 'phone' => '021-55667788', 'address' => 'Jl. HR Rasuna Said No. 321, Jakarta'],
        ];

        foreach ($vendorData as $data) {
            $vendor = Vendor::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'is_active' => true,
                ]
            );
            $vendors[] = $vendor;
        }

        // Get existing Projects (from ProjectSeeder)
        $projects = Project::all();
        
        // Assign Project Managers with different access types
        if (isset($users[0]) && $projects->count() > 0) {
            // Budi (PM) - Full Access to first project
            $projects[0]->managers()->syncWithoutDetaching([
                $users[0]->id => ['access_type' => 'full']
            ]);
        }
        if (isset($users[1]) && $projects->count() > 1) {
            // Siti (Senior Engineer) - PM Access to second project
            $projects[1]->managers()->syncWithoutDetaching([
                $users[1]->id => ['access_type' => 'pm']
            ]);
        }
        if (isset($users[3]) && $projects->count() > 2) {
            // Dewi (Finance) - Finance Access to third project
            $projects[2]->managers()->syncWithoutDetaching([
                $users[3]->id => ['access_type' => 'finance']
            ]);
        }
        if (isset($users[0]) && $projects->count() > 3) {
            // Budi juga PM di project keempat
            $projects[3]->managers()->syncWithoutDetaching([
                $users[0]->id => ['access_type' => 'pm']
            ]);
        }

        // Get Leave Types (should be seeded by LeaveTypeSeeder)
        $leaveTypes = LeaveType::all();
        if ($leaveTypes->isEmpty()) {
            $leaveTypes = collect([
                LeaveType::create(['name' => 'Cuti Tahunan', 'max_days' => 12, 'is_active' => true]),
                LeaveType::create(['name' => 'Cuti Sakit', 'max_days' => 30, 'is_active' => true]),
                LeaveType::create(['name' => 'Cuti Melahirkan', 'max_days' => 90, 'is_active' => true]),
                LeaveType::create(['name' => 'Cuti Khusus', 'max_days' => 5, 'is_active' => true]),
            ]);
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
            for ($i = 0; $i < 8; $i++) {
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
            for ($i = 0; $i < 6; $i++) {
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

        // Create SPD
        $destinations = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar', 'Bali'];
        $spdCounter = 1;
        
        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $departureDate = Carbon::now()->subDays(rand(0, 20));
                $returnDate = $departureDate->copy()->addDays(rand(1, 5));
                $destination = $destinations[array_rand($destinations)];
                
                $days = $returnDate->diffInDays($departureDate) + 1;
                $transportCost = rand(500000, 2000000);
                $accommodationCostPerDay = rand(300000, 1500000);
                $accommodationCost = $accommodationCostPerDay * $days;
                $mealCostPerDay = rand(100000, 300000);
                $mealCost = $mealCostPerDay * $days;
                $otherCost = rand(0, 500000);
                $totalCost = $transportCost + $accommodationCost + $mealCost + $otherCost;
                
                // Ensure all costs are positive
                $transportCost = abs($transportCost);
                $accommodationCost = abs($accommodationCost);
                $mealCost = abs($mealCost);
                $otherCost = abs($otherCost);
                $totalCost = abs($totalCost);

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $spdNumber = 'SPD-' . Carbon::now()->format('Y') . '-' . str_pad($spdCounter++, 4, '0', STR_PAD_LEFT);
                // Check if exists, if yes add timestamp
                if (Spd::where('spd_number', $spdNumber)->exists()) {
                    $spdNumber = 'SPD-' . Carbon::now()->format('Y') . '-' . str_pad($spdCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                $spd = Spd::create([
                    'spd_number' => $spdNumber,
                    'user_id' => $user->id,
                    'project_id' => $projects->random()->id,
                    'destination' => $destination,
                    'departure_date' => $departureDate,
                    'return_date' => $returnDate,
                    'purpose' => 'Perjalanan dinas untuk ' . $destination . ' dalam rangka koordinasi proyek',
                    'transport_cost' => $transportCost,
                    'accommodation_cost' => $accommodationCost,
                    'meal_cost' => $mealCost,
                    'other_cost' => $otherCost,
                    'other_cost_description' => $otherCost > 0 ? 'Biaya parkir dan tol' : null,
                    'total_cost' => $totalCost,
                    'status' => $status,
                    'notes' => 'Mohon persetujuan untuk perjalanan dinas ini',
                    'approved_by' => $status === ApprovalStatus::APPROVED ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }

        // Create Purchases
        $itemNames = [
            'Material Beton Ready Mix',
            'Besi Beton Ulir',
            'Kayu Papan',
            'Semen Portland',
            'Pipa PVC',
            'Kabel Listrik',
            'Cat Tembok',
            'Keramik Lantai',
        ];
        $purchaseCounter = 1;

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $quantity = rand(10, 100);
                $unitPrice = rand(50000, 500000);
                $totalPrice = $quantity * $unitPrice;

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $purchaseNumber = 'PUR-' . Carbon::now()->format('Y') . '-' . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT);
                if (Purchase::where('purchase_number', $purchaseNumber)->exists()) {
                    $purchaseNumber = 'PUR-' . Carbon::now()->format('Y') . '-' . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'user_id' => $user->id,
                    'project_id' => $projects->random()->id,
                    'type' => ['material', 'service', 'equipment'][array_rand(['material', 'service', 'equipment'])],
                    'category' => ['construction', 'electrical', 'plumbing'][array_rand(['construction', 'electrical', 'plumbing'])],
                    'item_name' => $itemNames[array_rand($itemNames)],
                    'quantity' => $quantity,
                    'unit' => ['kg', 'pcs', 'm', 'liter'][array_rand(['kg', 'pcs', 'm', 'liter'])],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'description' => 'Pengadaan ' . $itemNames[array_rand($itemNames)] . ' untuk keperluan proyek',
                    'notes' => 'Mohon persetujuan untuk pembelian ini',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }

        // Create Vendor Payments
        $paymentCounter = 1;
        foreach ($users as $user) {
            for ($i = 0; $i < 4; $i++) {
                $amount = rand(5000000, 50000000);
                $vendor = $vendors[array_rand($vendors)];

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $paymentNumber = 'PAY-' . Carbon::now()->format('Y') . '-' . str_pad($paymentCounter++, 4, '0', STR_PAD_LEFT);
                if (VendorPayment::where('payment_number', $paymentNumber)->exists()) {
                    $paymentNumber = 'PAY-' . Carbon::now()->format('Y') . '-' . str_pad($paymentCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                VendorPayment::create([
                    'payment_number' => $paymentNumber,
                    'user_id' => $user->id,
                    'vendor_id' => $vendor->id,
                    'project_id' => $projects->random()->id,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'amount' => $amount,
                    'payment_date' => Carbon::now()->subDays(rand(5, 30)),
                    'description' => 'Pembayaran kepada ' . $vendor->name . ' untuk jasa yang telah diberikan',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }

        // Create Leave Requests
        $leaveCounter = 1;
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $startDate = Carbon::now()->subDays(rand(0, 15));
                $endDate = $startDate->copy()->addDays(rand(1, 5));
                $leaveType = $leaveTypes->random();

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $leaveNumber = 'LV-' . Carbon::now()->format('Y') . '-' . str_pad($leaveCounter++, 4, '0', STR_PAD_LEFT);
                if (LeaveRequest::where('leave_number', $leaveNumber)->exists()) {
                    $leaveNumber = 'LV-' . Carbon::now()->format('Y') . '-' . str_pad($leaveCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                LeaveRequest::create([
                    'leave_number' => $leaveNumber,
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $startDate->diffInDays($endDate) + 1,
                    'reason' => 'Pengajuan cuti ' . $leaveType->name . ' untuk keperluan pribadi',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }

        $this->command->info('✅ Seeder data berhasil dijalankan!');
        $this->command->info('📊 Data yang dibuat:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - Projects: ' . Project::count());
        $this->command->info('   - Work Plans: ' . WorkPlan::count());
        $this->command->info('   - Work Realizations: ' . WorkRealization::count());
        $this->command->info('   - SPD: ' . Spd::count());
        $this->command->info('   - Purchases: ' . Purchase::count());
        $this->command->info('   - Vendor Payments: ' . VendorPayment::count());
        $this->command->info('   - Leave Requests: ' . LeaveRequest::count());
    }
}

