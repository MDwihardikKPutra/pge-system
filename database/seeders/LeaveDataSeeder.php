<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Enums\ApprovalStatus;
use Carbon\Carbon;

class LeaveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  Users not found. Please run UserSeeder first.');
            return;
        }

        if ($leaveTypes->isEmpty()) {
            $this->command->warn('⚠️  Leave Types not found. Please run LeaveTypeSeeder first.');
            return;
        }

        $leaveCounter = 1;
        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $startDate = Carbon::now()->subDays(rand(0, 15));
                $endDate = $startDate->copy()->addDays(rand(1, 5));
                $leaveType = $leaveTypes->random();

                $statuses = [ApprovalStatus::PENDING, ApprovalStatus::APPROVED, ApprovalStatus::REJECTED];
                $status = $statuses[array_rand($statuses)];

                $leaveNumber = 'LV-' . Carbon::now()->format('Y') . '-' . str_pad($leaveCounter++, 4, '0', STR_PAD_LEFT);
                if (LeaveRequest::where('leave_number', $leaveNumber)->exists()) {
                    $leaveNumber = 'LV-' . Carbon::now()->format('Y') . '-' . str_pad($leaveCounter++, 4, '0', STR_PAD_LEFT) . '-' . time();
                }
                
                $admin = User::role('admin')->first();
                
                LeaveRequest::create([
                    'leave_number' => $leaveNumber,
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $startDate->diffInDays($endDate) + 1,
                    'reason' => 'Pengajuan cuti ' . $leaveType->name . ' untuk keperluan pribadi',
                    'status' => $status,
                    'approved_by' => $status === ApprovalStatus::APPROVED && $admin ? $admin->id : null,
                    'approved_at' => $status === ApprovalStatus::APPROVED && $admin ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'rejection_reason' => $status === ApprovalStatus::REJECTED ? 'Jadwal cuti tidak sesuai dengan workload proyek' : null,
                ]);
            }
        }

        $this->command->info('✅ Leave Requests seeded: ' . LeaveRequest::count());
    }
}





