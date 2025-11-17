<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\LeaveRequest;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $workPlans = WorkPlan::all();
        $workRealizations = WorkRealization::all();
        $leaveRequests = LeaveRequest::all();
        $spds = SPD::all();
        $purchases = Purchase::all();
        $vendorPayments = VendorPayment::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('📝 Seeding activity logs...');

        // Create activity logs for Work Plans
        if ($workPlans->isNotEmpty()) {
            foreach ($workPlans->take(10) as $workPlan) {
                $user = $users->random();
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => WorkPlan::class,
                    'model_id' => $workPlan->id,
                    'description' => 'Membuat rencana kerja ' . ($workPlan->work_plan_number ?? '#' . $workPlan->id),
                    'properties' => [
                        'plan_date' => $workPlan->plan_date?->format('Y-m-d'),
                        'status' => $workPlan->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        // Create activity logs for Work Realizations
        if ($workRealizations->isNotEmpty()) {
            foreach ($workRealizations->take(10) as $realization) {
                $user = $users->random();
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => WorkRealization::class,
                    'model_id' => $realization->id,
                    'description' => 'Membuat realisasi kerja ' . ($realization->realization_number ?? '#' . $realization->id),
                    'properties' => [
                        'realization_date' => $realization->realization_date?->format('Y-m-d'),
                        'status' => $realization->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        // Create activity logs for Leave Requests
        if ($leaveRequests->isNotEmpty()) {
            foreach ($leaveRequests->take(15) as $leave) {
                $user = $users->random();
                
                // Created action
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => LeaveRequest::class,
                    'model_id' => $leave->id,
                    'description' => 'Membuat permohonan cuti ' . ($leave->leave_number ?? '#' . $leave->id),
                    'properties' => [
                        'status' => $leave->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);

                // Approve/Reject action (if status is approved or rejected)
                if ($leave->status && in_array($leave->status->value, ['approved', 'rejected'])) {
                    $admin = $users->where('email', 'admin@pge.local')->first() ?? $users->random();
                    ActivityLog::create([
                        'user_id' => $admin->id,
                        'action' => $leave->status->value === 'approved' ? 'approved' : 'rejected',
                        'model_type' => LeaveRequest::class,
                        'model_id' => $leave->id,
                        'description' => ($leave->status->value === 'approved' ? 'Menyetujui' : 'Menolak') . ' permohonan cuti ' . ($leave->leave_number ?? '#' . $leave->id),
                        'properties' => [
                            'status' => $leave->status->value,
                        ],
                        'ip_address' => $this->generateIpAddress(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 25))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        // Create activity logs for SPD
        if ($spds->isNotEmpty()) {
            foreach ($spds->take(10) as $spd) {
                $user = $users->random();
                
                // Created action
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => SPD::class,
                    'model_id' => $spd->id,
                    'description' => 'Membuat SPD ' . ($spd->spd_number ?? '#' . $spd->id),
                    'properties' => [
                        'status' => $spd->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);

                // Approve/Reject action
                if ($spd->status && in_array($spd->status->value, ['approved', 'rejected'])) {
                    $admin = $users->where('email', 'admin@pge.local')->first() ?? $users->random();
                    ActivityLog::create([
                        'user_id' => $admin->id,
                        'action' => $spd->status->value === 'approved' ? 'approved' : 'rejected',
                        'model_type' => SPD::class,
                        'model_id' => $spd->id,
                        'description' => ($spd->status->value === 'approved' ? 'Menyetujui' : 'Menolak') . ' SPD ' . ($spd->spd_number ?? '#' . $spd->id),
                        'properties' => [
                            'status' => $spd->status->value,
                        ],
                        'ip_address' => $this->generateIpAddress(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 25))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        // Create activity logs for Purchases
        if ($purchases->isNotEmpty()) {
            foreach ($purchases->take(10) as $purchase) {
                $user = $users->random();
                
                // Created action
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => Purchase::class,
                    'model_id' => $purchase->id,
                    'description' => 'Membuat pembelian ' . ($purchase->purchase_number ?? '#' . $purchase->id),
                    'properties' => [
                        'status' => $purchase->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);

                // Approve/Reject action
                if ($purchase->status && in_array($purchase->status->value, ['approved', 'rejected'])) {
                    $admin = $users->where('email', 'admin@pge.local')->first() ?? $users->random();
                    ActivityLog::create([
                        'user_id' => $admin->id,
                        'action' => $purchase->status->value === 'approved' ? 'approved' : 'rejected',
                        'model_type' => Purchase::class,
                        'model_id' => $purchase->id,
                        'description' => ($purchase->status->value === 'approved' ? 'Menyetujui' : 'Menolak') . ' pembelian ' . ($purchase->purchase_number ?? '#' . $purchase->id),
                        'properties' => [
                            'status' => $purchase->status->value,
                        ],
                        'ip_address' => $this->generateIpAddress(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 25))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        // Create activity logs for Vendor Payments
        if ($vendorPayments->isNotEmpty()) {
            foreach ($vendorPayments->take(10) as $payment) {
                $user = $users->random();
                
                // Created action
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'created',
                    'model_type' => VendorPayment::class,
                    'model_id' => $payment->id,
                    'description' => 'Membuat pembayaran vendor ' . ($payment->payment_number ?? '#' . $payment->id),
                    'properties' => [
                        'status' => $payment->status?->value ?? null,
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);

                // Approve/Reject action
                if ($payment->status && in_array($payment->status->value, ['approved', 'rejected'])) {
                    $admin = $users->where('email', 'admin@pge.local')->first() ?? $users->random();
                    ActivityLog::create([
                        'user_id' => $admin->id,
                        'action' => $payment->status->value === 'approved' ? 'approved' : 'rejected',
                        'model_type' => VendorPayment::class,
                        'model_id' => $payment->id,
                        'description' => ($payment->status->value === 'approved' ? 'Menyetujui' : 'Menolak') . ' pembayaran vendor ' . ($payment->payment_number ?? '#' . $payment->id),
                        'properties' => [
                            'status' => $payment->status->value,
                        ],
                        'ip_address' => $this->generateIpAddress(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 25))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        // Create some updated actions
        if ($workPlans->isNotEmpty()) {
            foreach ($workPlans->take(5) as $workPlan) {
                $user = $users->random();
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'updated',
                    'model_type' => WorkPlan::class,
                    'model_id' => $workPlan->id,
                    'description' => 'Memperbarui rencana kerja ' . ($workPlan->work_plan_number ?? '#' . $workPlan->id),
                    'properties' => [
                        'plan_date' => $workPlan->plan_date?->format('Y-m-d'),
                    ],
                    'ip_address' => $this->generateIpAddress(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => Carbon::now()->subDays(rand(1, 20))->subHours(rand(0, 23)),
                ]);
            }
        }

        $count = ActivityLog::count();
        $this->command->info("✅ Activity logs seeded: {$count} records");
    }

    /**
     * Generate random IP address
     */
    private function generateIpAddress(): string
    {
        return rand(192, 223) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }

    /**
     * Generate random user agent
     */
    private function generateUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
        ];

        return $userAgents[array_rand($userAgents)];
    }
}





