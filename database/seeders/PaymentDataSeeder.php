<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Spd;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Enums\ApprovalStatus;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();
        $vendors = Vendor::all();

        if ($users->isEmpty() || $projects->isEmpty()) {
            $this->command->warn('⚠️  Users or Projects not found. Please run UserSeeder and ProjectSeeder first.');
            return;
        }

        // Create SPD
        $this->seedSpds($users, $projects);
        
        // Create Purchases
        $this->seedPurchases($users, $projects);
        
        // Create Vendor Payments
        $this->seedVendorPayments($users, $projects, $vendors);

        $this->command->info('✅ SPD seeded: ' . Spd::count());
        $this->command->info('✅ Purchases seeded: ' . Purchase::count());
        $this->command->info('✅ Vendor Payments seeded: ' . VendorPayment::count());
    }

    /**
     * Seed SPD data
     */
    private function seedSpds($users, $projects): void
    {
        $destinations = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar', 'Bali'];
        $spdCounter = 1;
        
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $departureDate = Carbon::now()->subDays(rand(0, 20));
                $returnDate = $departureDate->copy()->addDays(rand(1, 5));
                $destination = $destinations[array_rand($destinations)];
                
                $days = $returnDate->diffInDays($departureDate) + 1;
                $transportCost = abs(rand(500000, 2000000));
                $accommodationCostPerDay = abs(rand(300000, 1500000));
                $accommodationCost = $accommodationCostPerDay * $days;
                $mealCostPerDay = abs(rand(100000, 300000));
                $mealCost = $mealCostPerDay * $days;
                $otherCost = abs(rand(0, 500000));
                $totalCost = $transportCost + $accommodationCost + $mealCost + $otherCost;

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $spdNumber = 'SPD-' . Carbon::now()->format('Ymd') . '-' . str_pad($spdCounter++, 3, '0', STR_PAD_LEFT);
                if (Spd::where('spd_number', $spdNumber)->exists()) {
                    $spdNumber = 'SPD-' . Carbon::now()->format('Ymd') . '-' . str_pad($spdCounter++, 3, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                $costs = [
                    ['name' => 'Transport', 'description' => 'Biaya transportasi PP ' . $destination, 'amount' => $transportCost],
                    ['name' => 'Hotel', 'description' => 'Biaya akomodasi ' . $days . ' malam', 'amount' => $accommodationCost],
                    ['name' => 'Makan', 'description' => 'Biaya makan selama ' . $days . ' hari', 'amount' => $mealCost],
                ];
                if ($otherCost > 0) {
                    $costs[] = ['name' => 'Lainnya', 'description' => 'Biaya parkir, tol, dan lainnya', 'amount' => $otherCost];
                }
                
                $admin = User::role('admin')->first();
                
                Spd::create([
                    'spd_number' => $spdNumber,
                    'user_id' => $user->id,
                    'project_id' => $projects->random()->id,
                    'destination' => $destination,
                    'departure_date' => $departureDate,
                    'return_date' => $returnDate,
                    'purpose' => 'Perjalanan dinas untuk ' . $destination . ' dalam rangka koordinasi proyek dan meeting dengan stakeholder',
                    'transport_cost' => $transportCost,
                    'accommodation_cost' => $accommodationCost,
                    'meal_cost' => $mealCost,
                    'other_cost' => $otherCost,
                    'other_cost_description' => $otherCost > 0 ? 'Biaya parkir dan tol' : null,
                    'total_cost' => $totalCost,
                    'costs' => $costs,
                    'status' => $status,
                    'notes' => 'Mohon persetujuan untuk perjalanan dinas ini',
                    'approved_by' => $status === ApprovalStatus::APPROVED && $admin ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED && $admin ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'rejection_reason' => $status === ApprovalStatus::REJECTED ? 'Jadwal tidak sesuai atau budget tidak mencukupi' : null,
                ]);
            }
        }
    }

    /**
     * Seed Purchase data
     */
    private function seedPurchases($users, $projects): void
    {
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
            for ($i = 0; $i < 3; $i++) {
                $quantity = rand(10, 100);
                $unitPrice = rand(50000, 500000);
                $totalPrice = $quantity * $unitPrice;

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $purchaseNumber = 'PUR-' . Carbon::now()->format('Ymd') . '-' . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT);
                if (Purchase::where('purchase_number', $purchaseNumber)->exists()) {
                    $purchaseNumber = 'PUR-' . Carbon::now()->format('Ymd') . '-' . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                $admin = User::role('admin')->first();
                
                Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'user_id' => $user->id,
                    'project_id' => $projects->random()->id,
                    'type' => ['barang', 'jasa'][array_rand(['barang', 'jasa'])],
                    'category' => ['project', 'kantor', 'lainnya'][array_rand(['project', 'kantor', 'lainnya'])],
                    'item_name' => $itemNames[array_rand($itemNames)],
                    'quantity' => $quantity,
                    'unit' => ['Unit', 'Pcs', 'Set', 'Paket', 'Box', 'Meter', 'Liter', 'Kg'][array_rand(['Unit', 'Pcs', 'Set', 'Paket', 'Box', 'Meter', 'Liter', 'Kg'])],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'description' => 'Pengadaan ' . $itemNames[array_rand($itemNames)] . ' untuk keperluan proyek dengan spesifikasi sesuai kebutuhan',
                    'notes' => 'Mohon persetujuan untuk pembelian ini',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED && $admin ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED && $admin ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }
    }

    /**
     * Seed Vendor Payment data
     */
    private function seedVendorPayments($users, $projects, $vendors): void
    {
        if ($vendors->isEmpty()) {
            $this->command->warn('⚠️  No vendors found. Skipping vendor payments.');
            return;
        }

        $paymentCounter = 1;
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $amount = abs(rand(5000000, 50000000));
                $vendor = $vendors->random();

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('m');
                $paymentNumber = 'VP-' . $year . '-' . $month . '-' . str_pad($paymentCounter++, 4, '0', STR_PAD_LEFT);
                if (VendorPayment::where('payment_number', $paymentNumber)->exists()) {
                    $paymentNumber = 'VP-' . $year . '-' . $month . '-' . str_pad($paymentCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                $admin = User::role('admin')->first();
                
                VendorPayment::create([
                    'payment_number' => $paymentNumber,
                    'user_id' => $user->id,
                    'vendor_id' => $vendor->id,
                    'project_id' => $projects->random()->id,
                    'payment_type' => ['project', 'kantor', 'lainnya'][array_rand(['project', 'kantor', 'lainnya'])],
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'po_number' => 'PO-' . strtoupper(Str::random(6)),
                    'amount' => $amount,
                    'payment_date' => Carbon::now()->subDays(rand(5, 30)),
                    'description' => 'Pembayaran kepada ' . $vendor->name . ' untuk jasa yang telah diberikan sesuai dengan kontrak kerja',
                    'notes' => 'Mohon persetujuan untuk pembayaran ini',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED && $admin ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED && $admin ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'rejection_reason' => $status === ApprovalStatus::REJECTED ? 'Dokumen tidak lengkap atau tidak sesuai ketentuan' : null,
                ]);
            }
        }
    }
}

