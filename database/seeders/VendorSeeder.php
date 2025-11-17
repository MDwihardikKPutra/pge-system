<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendorData = [
            [
                'name' => 'PT Teknologi Jaya',
                'company' => 'PT Teknologi Jaya Sejahtera',
                'email' => 'info@teknologijaya.com',
                'phone' => '021-12345678',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat 10220',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'PT Teknologi Jaya Sejahtera',
            ],
            [
                'name' => 'CV Material Bangunan',
                'company' => 'CV Material Bangunan Abadi',
                'email' => 'sales@materialbangunan.com',
                'phone' => '021-87654321',
                'address' => 'Jl. Gatot Subroto No. 456, Jakarta Selatan 12930',
                'bank_name' => 'Mandiri',
                'account_number' => '2345678901',
                'account_holder' => 'CV Material Bangunan Abadi',
            ],
            [
                'name' => 'PT Konsultan Proyek',
                'company' => 'PT Konsultan Proyek Indonesia',
                'email' => 'contact@konsultanproyek.com',
                'phone' => '021-11223344',
                'address' => 'Jl. Thamrin No. 789, Jakarta Pusat 10310',
                'bank_name' => 'BNI',
                'account_number' => '3456789012',
                'account_holder' => 'PT Konsultan Proyek Indonesia',
            ],
            [
                'name' => 'CV Logistik Sejahtera',
                'company' => 'CV Logistik Sejahtera Makmur',
                'email' => 'info@logistiksejahtera.com',
                'phone' => '021-55667788',
                'address' => 'Jl. HR Rasuna Said No. 321, Jakarta Selatan 12950',
                'bank_name' => 'BRI',
                'account_number' => '4567890123',
                'account_holder' => 'CV Logistik Sejahtera Makmur',
            ],
        ];

        $vendors = [];
        foreach ($vendorData as $data) {
            $vendor = Vendor::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'company' => $data['company'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'account_holder' => $data['account_holder'],
                    'is_active' => true,
                ]
            );
            $vendors[] = $vendor;
        }

        $this->command->info('✅ Vendors seeded: ' . count($vendors));
    }
}





