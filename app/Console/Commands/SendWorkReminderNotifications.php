<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Notifications\WorkPlanReminderNotification;
use App\Notifications\WorkRealizationReminderNotification;
use Carbon\Carbon;

class SendWorkReminderNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-work-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for work plans and work realizations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        $this->info('Sending work reminder notifications...');
        
        // Get all active users (non-admin)
        $users = User::where('is_active', true)
            ->whereHas('roles', function($query) {
                $query->where('name', 'user');
            })
            ->get();
        
        $workPlanReminders = 0;
        $workRealizationReminders = 0;
        
        foreach ($users as $user) {
            // Check if user has access to work-plan module
            if (!$user->hasModuleAccess('work-plan')) {
                continue;
            }
            
            // Check if user has work plan for today
            $hasWorkPlanToday = WorkPlan::where('user_id', $user->id)
                ->whereDate('plan_date', $today)
                ->exists();
            
            // Send reminder if no work plan for today (reminder appears anytime before 10 AM)
            if (!$hasWorkPlanToday && $now->hour < 10) {
                // Check if user already got notification today (to avoid duplicates)
                $hasNotificationToday = $user->notifications()
                    ->where('type', 'App\Notifications\WorkPlanReminderNotification')
                    ->whereDate('created_at', $today)
                    ->exists();
                
                if (!$hasNotificationToday) {
                    $user->notify(new WorkPlanReminderNotification());
                    $workPlanReminders++;
                }
            }
            
            // Check if user has access to work-realization module
            if (!$user->hasModuleAccess('work-realization')) {
                continue;
            }
            
            // Check if user has work realization for today
            $hasWorkRealizationToday = WorkRealization::where('user_id', $user->id)
                ->whereDate('realization_date', $today)
                ->exists();
            
            // Send reminder if no work realization and it's 4 PM or later (but before 5 PM)
            if (!$hasWorkRealizationToday && $now->hour >= 16 && $now->hour < 17) {
                // Check if user already got notification today
                $hasNotificationToday = $user->notifications()
                    ->where('type', 'App\Notifications\WorkRealizationReminderNotification')
                    ->whereDate('created_at', $today)
                    ->exists();
                
                if (!$hasNotificationToday) {
                    $user->notify(new WorkRealizationReminderNotification());
                    $workRealizationReminders++;
                }
            }
        }
        
        $this->info("Sent {$workPlanReminders} work plan reminders");
        $this->info("Sent {$workRealizationReminders} work realization reminders");
        
        return Command::SUCCESS;
    }
}

