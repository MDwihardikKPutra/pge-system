<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\Project;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\LeaveRequest;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ChecksAuthorization;
    /**
     * Display user dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get month filter from request, default to current month
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($month . '-01');
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Get active modules for this user
        $activeModules = $user->getActiveModules();
        
        // Get recent activities from all user's assigned modules (filtered by month)
        $recentActivities = $this->getUserRecentActivities($user, $activeModules, $startOfMonth, $endOfMonth);
        
        // Get statistics for dashboard
        $stats = $this->getUserStatistics($user, $activeModules);

        // Check work plan and work realization status for today
        $today = Carbon::today();
        $now = Carbon::now();
        
        $hasWorkPlanToday = false;
        $hasWorkRealizationToday = false;
        
        if ($user->hasModuleAccess('work-plan')) {
            $hasWorkPlanToday = WorkPlan::where('user_id', $user->id)
                ->whereDate('plan_date', $today)
                ->exists();
        }
        
        if ($user->hasModuleAccess('work-realization')) {
            $hasWorkRealizationToday = WorkRealization::where('user_id', $user->id)
                ->whereDate('realization_date', $today)
                ->exists();
        }
        
        $workNotifications = [
            'needs_work_plan' => !$hasWorkPlanToday && $user->hasModuleAccess('work-plan'),
            'needs_work_realization' => !$hasWorkRealizationToday && $user->hasModuleAccess('work-realization') && $now->hour >= 16,
        ];

        return view('user.dashboard', compact('recentActivities', 'activeModules', 'stats', 'workNotifications', 'month'));
    }
    
    /**
     * Get user statistics for dashboard
     */
    protected function getUserStatistics($user, $activeModules)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        
        $stats = [
            'pending_approvals' => 0,
            'approved_this_month' => 0,
            'total_submissions' => 0,
            'work_plans' => 0,
            'work_realizations' => 0,
        ];
        
        foreach ($activeModules as $module) {
            $moduleKey = $module->key;
            
            switch ($moduleKey) {
                case 'work-plan':
                    $stats['work_plans'] = WorkPlan::where('user_id', $user->id)
                        ->where('plan_date', '>=', $startOfMonth)
                        ->count();
                    break;
                    
                case 'work-realization':
                    $stats['work_realizations'] = WorkRealization::where('user_id', $user->id)
                        ->where('realization_date', '>=', $startOfMonth)
                        ->count();
                    break;
                    
                case 'spd':
                    $pendingSpd = SPD::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::PENDING)
                        ->count();
                    $approvedSpd = SPD::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::APPROVED)
                        ->where('created_at', '>=', $startOfMonth)
                        ->count();
                    $stats['pending_approvals'] += $pendingSpd;
                    $stats['approved_this_month'] += $approvedSpd;
                    break;
                    
                case 'purchase':
                    $pendingPurchase = Purchase::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::PENDING)
                        ->count();
                    $approvedPurchase = Purchase::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::APPROVED)
                        ->where('created_at', '>=', $startOfMonth)
                        ->count();
                    $stats['pending_approvals'] += $pendingPurchase;
                    $stats['approved_this_month'] += $approvedPurchase;
                    break;
                    
                case 'vendor-payment':
                    $pendingVp = VendorPayment::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::PENDING)
                        ->count();
                    $approvedVp = VendorPayment::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::APPROVED)
                        ->where('created_at', '>=', $startOfMonth)
                        ->count();
                    $stats['pending_approvals'] += $pendingVp;
                    $stats['approved_this_month'] += $approvedVp;
                    break;
                    
                case 'leave':
                    $pendingLeave = LeaveRequest::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::PENDING)
                        ->count();
                    $approvedLeave = LeaveRequest::where('user_id', $user->id)
                        ->where('status', ApprovalStatus::APPROVED)
                        ->where('created_at', '>=', $startOfMonth)
                        ->count();
                    $stats['pending_approvals'] += $pendingLeave;
                    $stats['approved_this_month'] += $approvedLeave;
                    break;
            }
        }
        
        $stats['total_submissions'] = $stats['pending_approvals'] + $stats['approved_this_month'];
        
        return $stats;
    }

    /**
     * Get recent activities from all user's assigned modules
     * Shows user's own activities as personal log
     * Filtered by month to match table views
     */
    protected function getUserRecentActivities($user, $activeModules, $startOfMonth = null, $endOfMonth = null)
    {
        $activities = collect();
        
        // If no date range provided, use current month
        if (!$startOfMonth || !$endOfMonth) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }
        
        foreach ($activeModules as $module) {
            $moduleKey = $module->key;
            
            switch ($moduleKey) {
                case 'work-plan':
                    // Show user's own work plans as personal log (filtered by month)
                    $workPlans = WorkPlan::where('user_id', $user->id)
                        ->whereBetween('plan_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->with(['project', 'user'])
                        ->orderBy('plan_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($workPlans as $plan) {
                        $activities->push([
                            'id' => $plan->id,
                            'type' => 'work-plan',
                            'icon' => 'clipboard-document',
                            'title' => 'Rencana Kerja',
                            'description' => $plan->title ?? \Illuminate\Support\Str::limit($plan->description, 40),
                            'full_description' => $plan->description ?? '-',
                            'number' => $plan->work_plan_number ?? '-',
                            'project' => $plan->project->name ?? '-',
                            'project_code' => $plan->project->code ?? '-',
                            'location' => $plan->work_location ?? '-',
                            'duration' => $plan->planned_duration_hours ?? 0,
                            'date' => $plan->plan_date,
                            'extra' => null,
                            'user' => $plan->user->name ?? '-',
                            'user_email' => $plan->user->email ?? '-',
                            'route' => route('user.work-plans.index'),
                        ]);
                    }
                    break;
                    
                case 'work-realization':
                    // Show user's own work realizations as personal log (filtered by month)
                    $workRealizations = WorkRealization::where('user_id', $user->id)
                        ->whereBetween('realization_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->with(['project', 'user'])
                        ->orderBy('realization_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($workRealizations as $realization) {
                        $activities->push([
                            'id' => $realization->id,
                            'type' => 'work-realization',
                            'icon' => 'check-circle',
                            'title' => 'Realisasi Kerja',
                            'description' => $realization->title ?? \Illuminate\Support\Str::limit($realization->description, 40),
                            'full_description' => $realization->description ?? '-',
                            'number' => $realization->realization_number ?? '-',
                            'project' => $realization->project->name ?? '-',
                            'project_code' => $realization->project->code ?? '-',
                            'location' => $realization->work_location ?? '-',
                            'duration' => $realization->actual_duration_hours ?? 0,
                            'progress' => $realization->progress_percentage ?? 0,
                            'date' => $realization->realization_date,
                            'extra' => $realization->progress_percentage ?? 0,
                            'user' => $realization->user->name ?? '-',
                            'user_email' => $realization->user->email ?? '-',
                            'route' => route('user.work-realizations.index'),
                        ]);
                    }
                    break;
                    
                case 'spd':
                    // Show user's own SPDs as personal log (filtered by month)
                    $spds = SPD::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                        ->with(['project', 'approvedBy', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($spds as $spd) {
                        $statusValue = is_object($spd->status ?? null) ? $spd->status->value : ($spd->status ?? 'pending');
                        $activities->push([
                            'id' => $spd->id,
                            'type' => 'spd',
                            'icon' => 'paper-airplane',
                            'title' => 'SPD',
                            'description' => $spd->spd_number . ' - ' . $spd->destination,
                            'number' => $spd->spd_number ?? '-',
                            'destination' => $spd->destination ?? '-',
                            'project' => $spd->project->name ?? '-',
                            'project_code' => $spd->project->code ?? '-',
                            'purpose' => $spd->purpose ?? '-',
                            'departure_date' => $spd->departure_date ? $spd->departure_date->format('d M Y') : '-',
                            'return_date' => $spd->return_date ? $spd->return_date->format('d M Y') : '-',
                            'total_cost' => $spd->total_cost ?? 0,
                            'status' => $statusValue,
                            'approved_by' => $spd->approvedBy->name ?? null,
                            'approved_at' => $spd->approved_at ? $spd->approved_at->format('d M Y H:i') : null,
                            'date' => $spd->created_at,
                            'extra' => $statusValue,
                            'user' => $spd->user->name ?? '-',
                            'user_email' => $spd->user->email ?? '-',
                            'route' => route('user.spd.index'),
                        ]);
                    }
                    break;
                    
                case 'purchase':
                    // Show user's own purchases as personal log (filtered by month)
                    $purchases = Purchase::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                        ->with(['project', 'approvedBy', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($purchases as $purchase) {
                        $statusValue = is_object($purchase->status ?? null) ? $purchase->status->value : ($purchase->status ?? 'pending');
                        $activities->push([
                            'id' => $purchase->id,
                            'type' => 'purchase',
                            'icon' => 'shopping-cart',
                            'title' => 'Pembelian',
                            'description' => $purchase->purchase_number . ' - ' . $purchase->item_name,
                            'number' => $purchase->purchase_number ?? '-',
                            'item_name' => $purchase->item_name ?? '-',
                            'project' => $purchase->project->name ?? '-',
                            'project_code' => $purchase->project->code ?? '-',
                            'total_price' => $purchase->total_price ?? 0,
                            'status' => $statusValue,
                            'approved_by' => $purchase->approvedBy->name ?? null,
                            'approved_at' => $purchase->approved_at ? $purchase->approved_at->format('d M Y H:i') : null,
                            'date' => $purchase->created_at,
                            'extra' => $statusValue,
                            'user' => $purchase->user->name ?? '-',
                            'user_email' => $purchase->user->email ?? '-',
                            'route' => route('user.purchases.index'),
                        ]);
                    }
                    break;
                    
                case 'vendor-payment':
                    // Show user's own vendor payments as personal log (filtered by month)
                    $vendorPayments = VendorPayment::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                        ->with(['user', 'vendor', 'project', 'approvedBy'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($vendorPayments as $vendorPayment) {
                        $statusValue = is_object($vendorPayment->status ?? null) ? $vendorPayment->status->value : ($vendorPayment->status ?? 'pending');
                        $activities->push([
                            'id' => $vendorPayment->id,
                            'type' => 'vendor-payment',
                            'icon' => 'credit-card',
                            'title' => 'Pembayaran Vendor',
                            'description' => $vendorPayment->payment_number . ' - ' . ($vendorPayment->vendor->name ?? '-'),
                            'number' => $vendorPayment->payment_number ?? '-',
                            'vendor' => $vendorPayment->vendor->name ?? '-',
                            'vendor_email' => $vendorPayment->vendor->email ?? '-',
                            'project' => $vendorPayment->project->name ?? '-',
                            'project_code' => $vendorPayment->project->code ?? '-',
                            'invoice_number' => $vendorPayment->invoice_number ?? '-',
                            'amount' => $vendorPayment->amount ?? 0,
                            'payment_date' => $vendorPayment->payment_date ? $vendorPayment->payment_date->format('d M Y') : '-',
                            'description_text' => $vendorPayment->description ?? '-',
                            'status' => $statusValue,
                            'approved_by' => $vendorPayment->approvedBy->name ?? null,
                            'approved_at' => $vendorPayment->approved_at ? $vendorPayment->approved_at->format('d M Y H:i') : null,
                            'date' => $vendorPayment->created_at,
                            'extra' => $statusValue,
                            'user' => $vendorPayment->user->name ?? '-',
                            'user_email' => $vendorPayment->user->email ?? '-',
                            'route' => route('user.vendor-payments.index'),
                        ]);
                    }
                    break;
                    
                case 'leave':
                    // Show user's own leave requests as personal log (filtered by month)
                    $leaves = LeaveRequest::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                        ->with(['leaveType', 'approvedBy', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    foreach ($leaves as $leave) {
                        $statusValue = is_object($leave->status ?? null) ? $leave->status->value : ($leave->status ?? 'pending');
                        $activities->push([
                            'id' => $leave->id,
                            'type' => 'leave',
                            'icon' => 'calendar',
                            'title' => 'Cuti & Izin',
                            'description' => $leave->leave_number . ' - ' . ($leave->leaveType->name ?? '-'),
                            'full_description' => $leave->reason ?? '-',
                            'number' => $leave->leave_number ?? '-',
                            'leave_type' => $leave->leaveType->name ?? '-',
                            'total_days' => $leave->total_days ?? 0,
                            'start_date' => $leave->start_date ? $leave->start_date->format('d M Y') : '-',
                            'end_date' => $leave->end_date ? $leave->end_date->format('d M Y') : '-',
                            'reason' => $leave->reason ?? '-',
                            'status' => $statusValue,
                            'approved_by' => $leave->approvedBy->name ?? null,
                            'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : null,
                            'date' => $leave->created_at,
                            'extra' => $statusValue,
                            'user' => $leave->user->name ?? '-',
                            'user_email' => $leave->user->email ?? '-',
                            'route' => route('user.leaves.index'),
                        ]);
                    }
                    break;
                    
                case 'project-management':
                case 'project-monitoring':
                    $projects = Project::whereHas('managers', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->active()
                    ->orderedByName()
                    ->limit(10)
                    ->get();
                    
                    foreach ($projects as $project) {
                        $activities->push([
                            'id' => $project->id,
                            'type' => 'project',
                            'icon' => 'folder',
                            'title' => 'Project Management',
                            'description' => $project->name,
                            'full_description' => $project->description ?? '-',
                            'number' => $project->code ?? '-',
                            'project' => $project->name ?? '-',
                            'project_code' => $project->code ?? '-',
                            'client' => $project->client ?? '-',
                            'date' => $project->created_at,
                            'extra' => null,
                            'route' => route('user.project-management.index'),
                        ]);
                    }
                    break;
                    
                case 'ear':
                    // If user has EAR module, show ALL work plans and work realizations from ALL users (collective data)
                    // Show all work plans (filtered by month) - data kolektif untuk monitoring
                    $workPlans = WorkPlan::whereBetween('plan_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->with(['project', 'user'])
                        ->orderBy('plan_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(20)
                        ->get();
                    
                    foreach ($workPlans as $plan) {
                        $activities->push([
                            'id' => $plan->id,
                            'type' => 'work-plan',
                            'icon' => 'clipboard-document',
                            'title' => 'Rencana Kerja',
                            'description' => ($plan->user->name ?? '-') . ' - ' . ($plan->title ?? \Illuminate\Support\Str::limit($plan->description, 30)),
                            'full_description' => $plan->description ?? '-',
                            'number' => $plan->work_plan_number ?? '-',
                            'project' => $plan->project->name ?? '-',
                            'project_code' => $plan->project->code ?? '-',
                            'location' => $plan->work_location ?? '-',
                            'duration' => $plan->planned_duration_hours ?? 0,
                            'date' => $plan->plan_date,
                            'extra' => null,
                            'user' => $plan->user->name ?? '-',
                            'user_email' => $plan->user->email ?? '-',
                            'route' => route('admin.ear'),
                        ]);
                    }
                    
                    // Show all work realizations (filtered by month) - data kolektif untuk monitoring
                    $workRealizations = WorkRealization::whereBetween('realization_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->with(['project', 'user'])
                        ->orderBy('realization_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(20)
                        ->get();
                    
                    foreach ($workRealizations as $realization) {
                        $activities->push([
                            'id' => $realization->id,
                            'type' => 'work-realization',
                            'icon' => 'check-circle',
                            'title' => 'Realisasi Kerja',
                            'description' => ($realization->user->name ?? '-') . ' - ' . ($realization->title ?? \Illuminate\Support\Str::limit($realization->description, 30)),
                            'full_description' => $realization->description ?? '-',
                            'number' => $realization->realization_number ?? '-',
                            'project' => $realization->project->name ?? '-',
                            'project_code' => $realization->project->code ?? '-',
                            'location' => $realization->work_location ?? '-',
                            'duration' => $realization->actual_duration_hours ?? 0,
                            'progress' => $realization->progress_percentage ?? 0,
                            'date' => $realization->realization_date,
                            'extra' => $realization->progress_percentage ?? 0,
                            'user' => $realization->user->name ?? '-',
                            'user_email' => $realization->user->email ?? '-',
                            'route' => route('admin.ear'),
                        ]);
                    }
                    break;
                    
                case 'leave-approval':
                    // If user has leave-approval module, show all leave requests from other users in recent activity
                    // Show all leave requests (not just own) for approval monitoring (filtered by month)
                    $leaves = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                        ->with(['leaveType', 'approvedBy', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->limit(20)
                        ->get();
                    
                    foreach ($leaves as $leave) {
                        $statusValue = is_object($leave->status ?? null) ? $leave->status->value : ($leave->status ?? 'pending');
                        $activities->push([
                            'id' => $leave->id,
                            'type' => 'leave',
                            'icon' => 'calendar',
                            'title' => 'Pengajuan Cuti & Izin',
                            'description' => ($leave->user->name ?? '-') . ' - ' . ($leave->leaveType->name ?? '-'),
                            'full_description' => $leave->reason ?? '-',
                            'number' => $leave->leave_number ?? '-',
                            'leave_type' => $leave->leaveType->name ?? '-',
                            'total_days' => $leave->total_days ?? 0,
                            'start_date' => $leave->start_date ? $leave->start_date->format('d M Y') : '-',
                            'end_date' => $leave->end_date ? $leave->end_date->format('d M Y') : '-',
                            'reason' => $leave->reason ?? '-',
                            'status' => $statusValue,
                            'approved_by' => $leave->approvedBy->name ?? null,
                            'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : null,
                            'date' => $leave->created_at,
                            'extra' => $statusValue,
                            'user' => $leave->user->name ?? '-',
                            'user_email' => $leave->user->email ?? '-',
                            'route' => route('user.leave-approvals.index'),
                        ]);
                    }
                    break;
            }
        }
        
        // Add approval activities for user's submissions (filtered by month)
        $approvalActivities = $this->getApprovalActivities($user, $activeModules, $startOfMonth, $endOfMonth);
        $activities = $activities->concat($approvalActivities);
        
        // Sort by date (most recent first) and take top 20
        return $activities->sortByDesc(function ($activity) {
            return $activity['date'] instanceof \Carbon\Carbon ? $activity['date']->timestamp : strtotime($activity['date']);
        })->take(20)->values();
    }
    
    /**
     * Get approval activities for user's submissions
     * Shows when user's submissions are approved by admin or approver
     * Filtered by month to match table views
     */
    protected function getApprovalActivities($user, $activeModules, $startOfMonth = null, $endOfMonth = null)
    {
        $activities = collect();
        $moduleKeys = $activeModules->pluck('key')->toArray();
        
        // If no date range provided, use current month
        if (!$startOfMonth || !$endOfMonth) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }
        
        // SPD Approvals
        if (in_array('spd', $moduleKeys)) {
            $approvedSpds = SPD::where('user_id', $user->id)
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                ->with(['project', 'approvedBy', 'user'])
                ->orderBy('approved_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($approvedSpds as $spd) {
                $activities->push([
                    'id' => $spd->id,
                    'type' => 'spd-approval',
                    'icon' => 'check-circle',
                    'title' => 'SPD Disetujui',
                    'description' => $spd->spd_number . ' - ' . $spd->destination . ' telah disetujui',
                    'number' => $spd->spd_number ?? '-',
                    'destination' => $spd->destination ?? '-',
                    'project' => $spd->project->name ?? '-',
                    'project_code' => $spd->project->code ?? '-',
                    'status' => 'approved',
                    'approved_by' => $spd->approvedBy->name ?? '-',
                    'approved_at' => $spd->approved_at ? $spd->approved_at->format('d M Y H:i') : null,
                    'date' => $spd->approved_at,
                    'extra' => 'approved',
                    'is_approval' => true,
                    'route' => route('user.spd.index'),
                ]);
            }
        }
        
        // Purchase Approvals
        if (in_array('purchase', $moduleKeys)) {
            $approvedPurchases = Purchase::where('user_id', $user->id)
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                ->with(['project', 'approvedBy', 'user'])
                ->orderBy('approved_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($approvedPurchases as $purchase) {
                $activities->push([
                    'id' => $purchase->id,
                    'type' => 'purchase-approval',
                    'icon' => 'check-circle',
                    'title' => 'Pembelian Disetujui',
                    'description' => $purchase->purchase_number . ' - ' . $purchase->item_name . ' telah disetujui',
                    'number' => $purchase->purchase_number ?? '-',
                    'item_name' => $purchase->item_name ?? '-',
                    'project' => $purchase->project->name ?? '-',
                    'project_code' => $purchase->project->code ?? '-',
                    'status' => 'approved',
                    'approved_by' => $purchase->approvedBy->name ?? '-',
                    'approved_at' => $purchase->approved_at ? $purchase->approved_at->format('d M Y H:i') : null,
                    'date' => $purchase->approved_at,
                    'extra' => 'approved',
                    'is_approval' => true,
                    'route' => route('user.purchases.index'),
                ]);
            }
        }
        
        // Vendor Payment Approvals
        if (in_array('vendor-payment', $moduleKeys)) {
            $approvedVendorPayments = VendorPayment::where('user_id', $user->id)
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                ->with(['user', 'vendor', 'project', 'approvedBy'])
                ->orderBy('approved_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($approvedVendorPayments as $vendorPayment) {
                $activities->push([
                    'id' => $vendorPayment->id,
                    'type' => 'vendor-payment-approval',
                    'icon' => 'check-circle',
                    'title' => 'Pembayaran Vendor Disetujui',
                    'description' => $vendorPayment->payment_number . ' - ' . ($vendorPayment->vendor->name ?? '-') . ' telah disetujui',
                    'number' => $vendorPayment->payment_number ?? '-',
                    'vendor' => $vendorPayment->vendor->name ?? '-',
                    'project' => $vendorPayment->project->name ?? '-',
                    'project_code' => $vendorPayment->project->code ?? '-',
                    'status' => 'approved',
                    'approved_by' => $vendorPayment->approvedBy->name ?? '-',
                    'approved_at' => $vendorPayment->approved_at ? $vendorPayment->approved_at->format('d M Y H:i') : null,
                    'date' => $vendorPayment->approved_at,
                    'extra' => 'approved',
                    'is_approval' => true,
                    'route' => route('user.vendor-payments.index'),
                ]);
            }
        }
        
        // Leave Approvals
        if (in_array('leave', $moduleKeys)) {
            $approvedLeaves = LeaveRequest::where('user_id', $user->id)
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$startOfMonth, $endOfMonth->copy()->endOfDay()])
                ->with(['leaveType', 'approvedBy', 'user'])
                ->orderBy('approved_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($approvedLeaves as $leave) {
                $activities->push([
                    'id' => $leave->id,
                    'type' => 'leave-approval',
                    'icon' => 'check-circle',
                    'title' => 'Cuti & Izin Disetujui',
                    'description' => $leave->leave_number . ' - ' . ($leave->leaveType->name ?? '-') . ' telah disetujui',
                    'number' => $leave->leave_number ?? '-',
                    'leave_type' => $leave->leaveType->name ?? '-',
                    'status' => 'approved',
                    'approved_by' => $leave->approvedBy->name ?? '-',
                    'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : null,
                    'date' => $leave->approved_at,
                    'extra' => 'approved',
                    'is_approval' => true,
                    'route' => route('user.leaves.index'),
                ]);
            }
        }
        
        return $activities;
    }

    /**
     * Display activity logs (submissions history)
     */
    public function log(Request $request)
    {
        $userId = auth()->id();
        $isAdmin = $this->isAdmin();
        $routePrefix = $isAdmin ? 'admin' : 'user';
        
        // Query untuk SPD (admin bisa lihat semua, user hanya milik sendiri)
        $spdQuery = SPD::query();
        if (!$isAdmin) {
            $spdQuery->where('user_id', $userId);
        }
        
        // Query untuk Purchase
        $purchaseQuery = Purchase::query();
        if (!$isAdmin) {
            $purchaseQuery->where('user_id', $userId);
        }
        
        // Query untuk Vendor Payment
        $vendorPaymentQuery = VendorPayment::with('vendor');
        if (!$isAdmin) {
            $vendorPaymentQuery->where('user_id', $userId);
        }
        
        // Query untuk Leave Request
        $leaveQuery = LeaveRequest::query();
        if (!$isAdmin) {
            $leaveQuery->where('user_id', $userId);
        }
        
        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $spdQuery->where(function($q) use ($search) {
                $q->where('spd_number', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
            $purchaseQuery->where(function($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
            $vendorPaymentQuery->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%");
            });
            $leaveQuery->where(function($q) use ($search) {
                $q->where('leave_number', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $spdQuery->where('status', $request->status);
            $purchaseQuery->where('status', $request->status);
            $vendorPaymentQuery->where('status', $request->status);
            $leaveQuery->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $spdQuery->whereDate('created_at', '>=', $request->date_from);
            $purchaseQuery->whereDate('created_at', '>=', $request->date_from);
            $vendorPaymentQuery->whereDate('created_at', '>=', $request->date_from);
            $leaveQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $spdQuery->whereDate('created_at', '<=', $request->date_to);
            $purchaseQuery->whereDate('created_at', '<=', $request->date_to);
            $vendorPaymentQuery->whereDate('created_at', '<=', $request->date_to);
            $leaveQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $allActivities = match($request->type) {
                'SPD' => $spdQuery->latest()->limit(100)->get()->map(fn($item) => [
                    'date' => $item->created_at,
                    'type' => 'SPD',
                    'description' => $item->spd_number . ' - ' . $item->destination,
                    'amount' => $item->total_cost ?? 0,
                    'status' => $item->status,
                    'url' => route($routePrefix . '.spd.show', $item),
                ]),
                'Purchase' => $purchaseQuery->latest()->limit(100)->get()->map(fn($item) => [
                    'date' => $item->created_at,
                    'type' => 'Purchase',
                    'description' => $item->purchase_number . ' - ' . $item->item_name,
                    'amount' => $item->total_price ?? 0,
                    'status' => $item->status,
                    'url' => route($routePrefix . '.purchases.show', $item),
                ]),
                'Vendor Payment' => $vendorPaymentQuery->latest()->limit(100)->get()->map(fn($item) => [
                    'date' => $item->created_at,
                    'type' => 'Vendor Payment',
                    'description' => $item->payment_number . ' - ' . ($item->vendor->name ?? 'N/A'),
                    'amount' => $item->amount ?? 0,
                    'status' => $item->status,
                    'url' => route($routePrefix . '.vendor-payments.show', $item),
                ]),
                'Leave' => $leaveQuery->latest()->limit(100)->get()->map(fn($item) => [
                    'date' => $item->created_at,
                    'type' => 'Leave',
                    'description' => $item->leave_number . ' - ' . ($item->leaveType->name ?? 'N/A'),
                    'amount' => 0, // Leave tidak punya amount
                    'status' => $item->status,
                    'url' => route($routePrefix . '.leaves.show', $item),
                ]),
                default => collect()
            };
        } else {
            // Combine all activities
            $spdActivities = $spdQuery->latest()->limit(20)->get()->map(fn($item) => [
                'date' => $item->created_at,
                'type' => 'SPD',
                'description' => $item->spd_number . ' - ' . $item->destination,
                'amount' => $item->total_cost ?? 0,
                'status' => $item->status,
                'url' => route($routePrefix . '.spd.show', $item),
            ]);
            
            $purchaseActivities = $purchaseQuery->latest()->limit(20)->get()->map(fn($item) => [
                'date' => $item->created_at,
                'type' => 'Purchase',
                'description' => $item->purchase_number . ' - ' . $item->item_name,
                'amount' => $item->total_price ?? 0,
                'status' => $item->status,
                'url' => route($routePrefix . '.purchases.show', $item),
            ]);
            
            $vendorPaymentActivities = $vendorPaymentQuery->latest()->limit(20)->get()->map(fn($item) => [
                'date' => $item->created_at,
                'type' => 'Vendor Payment',
                'description' => $item->payment_number . ' - ' . ($item->vendor->name ?? 'N/A'),
                'amount' => $item->amount ?? 0,
                'status' => $item->status,
                'url' => route($routePrefix . '.vendor-payments.show', $item),
            ]);
            
            $leaveActivities = $leaveQuery->latest()->limit(20)->get()->map(fn($item) => [
                'date' => $item->created_at,
                'type' => 'Leave',
                'description' => $item->leave_number . ' - ' . ($item->leaveType->name ?? 'N/A'),
                'amount' => 0,
                'status' => $item->status,
                'url' => route($routePrefix . '.leaves.show', $item),
            ]);
            
            $allActivities = $spdActivities->concat($purchaseActivities)
                ->concat($vendorPaymentActivities)
                ->concat($leaveActivities)
                ->sortByDesc('date')
                ->take(50)
                ->values();
        }
        
        return view('user.log', compact('allActivities'));
    }
}
