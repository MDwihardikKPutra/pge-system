<?php

namespace App\View\Composers;

use App\Models\WorkPlan;
use App\Models\WorkRealization;
use Illuminate\View\View;
use Carbon\Carbon;

class WorkNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $user = auth()->user();
        
        if (!$user || $user->hasRole('admin')) {
            $view->with('workPlanNeedsNotification', false);
            $view->with('workRealizationNeedsNotification', false);
            return;
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Check if user has work plan for today
        $hasWorkPlanToday = WorkPlan::where('user_id', $user->id)
            ->whereDate('plan_date', $today)
            ->exists();

        // Check if user has work realization for today
        $hasWorkRealizationToday = WorkRealization::where('user_id', $user->id)
            ->whereDate('realization_date', $today)
            ->exists();

        // Work plan: show notification if not filled
        $workPlanNeedsNotification = !$hasWorkPlanToday;

        // Work realization: show notification if not filled AND it's 4 PM or later
        $workRealizationNeedsNotification = !$hasWorkRealizationToday && $now->hour >= 16;

        $view->with('workPlanNeedsNotification', $workPlanNeedsNotification);
        $view->with('workRealizationNeedsNotification', $workRealizationNeedsNotification);
    }
}

