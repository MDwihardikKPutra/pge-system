<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\LeaveRequest;
use App\Models\Project;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{

    /**
     * Build module data for dashboard
     */
    private function buildModuleData($activeModules, $startOfMonth, $endOfMonth)
    {
        $data = [];
        
        foreach ($activeModules as $module) {
            $moduleKey = $module->key;
            $moduleInfo = [
                'module' => $module,
                'count' => 0,
                'recent' => collect(),
                'route' => null,
            ];
            
            // Get route
            $routes = $module->routes ?? [];
            if (isset($routes['index'])) {
                $moduleInfo['route'] = $routes['index'];
                // Convert user.* to admin.* for admin
                if (strpos($moduleInfo['route'], 'user.') === 0) {
                    if ($moduleInfo['route'] === 'user.payment-approvals.index') {
                        $moduleInfo['route'] = 'admin.approvals.payments.index';
                    } else {
                        $moduleInfo['route'] = str_replace('user.', 'admin.', $moduleInfo['route']);
                    }
                }
            }
            
            // Get data based on module key - optimized queries using date range instead of whereMonth/whereYear
            switch ($moduleKey) {
                case 'work-plan':
                    $query = WorkPlan::whereBetween('plan_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = WorkPlan::with('user')
                        ->whereBetween('plan_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->orderBy('plan_date', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'work-realization':
                    $query = WorkRealization::whereBetween('realization_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = WorkRealization::with('user')
                        ->whereBetween('realization_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                        ->orderBy('realization_date', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'spd':
                    $query = Spd::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = Spd::with('user')
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'purchase':
                    $query = Purchase::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = Purchase::with('user')
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'vendor-payment':
                    $query = VendorPayment::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = VendorPayment::with(['user', 'vendor'])
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'leave':
                    $query = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    $moduleInfo['count'] = $query->count();
                    $moduleInfo['recent'] = LeaveRequest::with(['user', 'leaveType'])
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                        
                case 'project-management':
                case 'project-monitoring':
                    $moduleInfo['count'] = Project::where('is_active', true)->count();
                    $moduleInfo['recent'] = Project::where('is_active', true)
                        ->orderBy('name')
                        ->limit(3)
                        ->get();
                    break;
                        
                case 'payment-approval':
                    $moduleInfo['count'] = Spd::where('status', ApprovalStatus::PENDING)->count() +
                                       Purchase::where('status', ApprovalStatus::PENDING)->count() +
                                       VendorPayment::where('status', ApprovalStatus::PENDING)->count();
                    $moduleInfo['recent'] = collect();
                    break;
                        
                case 'user':
                    $moduleInfo['count'] = User::where('is_active', true)->count();
                    $moduleInfo['recent'] = User::where('is_active', true)
                        ->orderBy('name')
                        ->limit(3)
                        ->get();
                    break;
            }
            
            $data[$moduleKey] = $moduleInfo;
        }
        
        return $data;
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get active modules for admin (all modules)
        $activeModules = $user->getActiveModules();
        
        // Get current month statistics (cached for 5 minutes to reduce queries)
        $currentMonth = now()->format('Y-m');
        $startOfMonth = Carbon::parse($currentMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($currentMonth . '-01')->endOfMonth();
        
        // Cache module data for 5 minutes with cache tags for grouped invalidation (if supported)
        $moduleDataCacheKey = 'admin_dashboard_module_data_' . $currentMonth;
        $supportsTagging = in_array(config('cache.default'), ['redis', 'memcached', 'dynamodb']);
        
        if ($supportsTagging) {
            $moduleData = \Illuminate\Support\Facades\Cache::tags(['dashboard', 'dashboard-module-data', "dashboard-module-{$currentMonth}"])
                ->remember($moduleDataCacheKey, \App\Helpers\CacheHelper::CACHE_5_MINUTES, function() use ($activeModules, $startOfMonth, $endOfMonth) {
                    return $this->buildModuleData($activeModules, $startOfMonth, $endOfMonth);
                });
        } else {
            $moduleData = \Illuminate\Support\Facades\Cache::remember($moduleDataCacheKey, \App\Helpers\CacheHelper::CACHE_5_MINUTES, function() use ($activeModules, $startOfMonth, $endOfMonth) {
                return $this->buildModuleData($activeModules, $startOfMonth, $endOfMonth);
            });
        }

        // Cache recent activities for 10 minutes with cache tags (if supported)
        $allRecentActivities = \App\Helpers\CacheHelper::getAdminDashboardRecentActivities(function() {
            $activities = collect();
        
            // Work Plans - Get all recent work plans
            $workPlans = WorkPlan::with(['user', 'project'])
            ->orderBy('plan_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($workPlans as $plan) {
                $activities->push([
                    'id' => $plan->id,
                'type' => 'work-plan',
                'icon' => '📋',
                'title' => 'Rencana Kerja',
                'user' => $plan->user->name ?? '-',
                    'user_email' => $plan->user->email ?? '-',
                'description' => $plan->title ?? \Illuminate\Support\Str::limit($plan->description, 40),
                    'full_description' => $plan->description ?? '-',
                    'number' => $plan->work_plan_number ?? '-',
                    'project' => $plan->project->name ?? '-',
                    'project_code' => $plan->project->code ?? '-',
                    'location' => $plan->work_location ?? '-',
                    'duration' => $plan->planned_duration_hours ?? 0,
                'date' => $plan->plan_date,
                'extra' => null,
                'route' => route('admin.work-plans.index'),
            ]);
        }
        
            // Work Realizations
            $workRealizations = WorkRealization::with(['user', 'project'])
            ->orderBy('realization_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($workRealizations as $realization) {
                $activities->push([
                    'id' => $realization->id,
                'type' => 'work-realization',
                'icon' => '✅',
                    'title' => 'Realisasi Kerja',
                'user' => $realization->user->name ?? '-',
                    'user_email' => $realization->user->email ?? '-',
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
                'route' => route('admin.work-realizations.index'),
            ]);
        }
        
            // SPD
            $spds = Spd::with(['user', 'project', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($spds as $spd) {
            $statusValue = is_object($spd->status ?? null) ? $spd->status->value : ($spd->status ?? 'pending');
                $activities->push([
                    'id' => $spd->id,
                'type' => 'spd',
                'icon' => '✈️',
                'title' => 'SPD',
                'user' => $spd->user->name ?? '-',
                    'user_email' => $spd->user->email ?? '-',
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
                'route' => route('admin.spd.index'),
            ]);
        }
        
            // Purchases
            $purchases = Purchase::with(['user', 'project', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($purchases as $purchase) {
            $statusValue = is_object($purchase->status ?? null) ? $purchase->status->value : ($purchase->status ?? 'pending');
                $activities->push([
                    'id' => $purchase->id,
                'type' => 'purchase',
                'icon' => '🛒',
                'title' => 'Pembelian',
                'user' => $purchase->user->name ?? '-',
                    'user_email' => $purchase->user->email ?? '-',
                'description' => $purchase->purchase_number . ' - ' . $purchase->item_name,
                    'number' => $purchase->purchase_number ?? '-',
                    'item_name' => $purchase->item_name ?? '-',
                    'project' => $purchase->project->name ?? '-',
                    'project_code' => $purchase->project->code ?? '-',
                    'quantity' => $purchase->quantity ?? 0,
                    'unit' => $purchase->unit ?? '-',
                    'unit_price' => $purchase->unit_price ?? 0,
                    'total_price' => $purchase->total_price ?? 0,
                    'notes' => $purchase->notes ?? '-',
                    'status' => $statusValue,
                    'approved_by' => $purchase->approvedBy->name ?? null,
                    'approved_at' => $purchase->approved_at ? $purchase->approved_at->format('d M Y H:i') : null,
                'date' => $purchase->created_at,
                'extra' => $statusValue,
                'route' => route('admin.purchases.index'),
            ]);
        }
        
            // Vendor Payments
            $vendorPayments = VendorPayment::with(['user', 'vendor', 'project', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($vendorPayments as $vendorPayment) {
            $statusValue = is_object($vendorPayment->status ?? null) ? $vendorPayment->status->value : ($vendorPayment->status ?? 'pending');
                $activities->push([
                    'id' => $vendorPayment->id,
                'type' => 'vendor-payment',
                'icon' => '💳',
                'title' => 'Pembayaran Vendor',
                'user' => $vendorPayment->user->name ?? '-',
                    'user_email' => $vendorPayment->user->email ?? '-',
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
                'route' => route('admin.vendor-payments.index'),
            ]);
        }
        
            // Leaves
            $leaves = LeaveRequest::with(['user', 'leaveType', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($leaves as $leave) {
            $statusValue = is_object($leave->status ?? null) ? $leave->status->value : ($leave->status ?? 'pending');
                $activities->push([
                    'id' => $leave->id,
                'type' => 'leave',
                'icon' => '🏝️',
                    'title' => 'Cuti & Izin',
                'user' => $leave->user->name ?? '-',
                    'user_email' => $leave->user->email ?? '-',
                'description' => ($leave->leaveType->name ?? '-') . ' - ' . $leave->leave_number,
                    'number' => $leave->leave_number ?? '-',
                    'leave_type' => $leave->leaveType->name ?? '-',
                    'start_date' => $leave->start_date ? $leave->start_date->format('d M Y') : '-',
                    'end_date' => $leave->end_date ? $leave->end_date->format('d M Y') : '-',
                    'total_days' => $leave->total_days ?? 0,
                    'reason' => $leave->reason ?? '-',
                    'status' => $statusValue,
                    'approved_by' => $leave->approvedBy->name ?? null,
                    'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : null,
                'date' => $leave->created_at,
                'extra' => $statusValue,
                'route' => route('admin.approvals.leaves'),
            ]);
        }
        
        // Sort by date (most recent first) and take top 20
            return $activities->sortByDesc(function ($activity) {
            return $activity['date'] instanceof \Carbon\Carbon ? $activity['date']->timestamp : strtotime($activity['date']);
        })->take(20)->values();
        });

        // Cache users list for 30 minutes
        $users = \App\Helpers\CacheHelper::getDashboardUsersList();

        return view('admin.dashboard', compact('moduleData', 'activeModules', 'allRecentActivities', 'users'));
    }

    /**
     * Display EAR (Employee Activity Report) page - All work plans and realizations
     */
    public function ear(Request $request)
    {
        // Check if user has EAR module access (admin always has access)
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('ear')) {
            abort(403, 'Anda tidak memiliki akses ke modul EAR');
        }
        
        // Validate input
        $validated = $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'user_id' => 'nullable|integer|exists:users,id',
            'project_id' => 'nullable|integer|exists:projects,id',
            'search' => 'nullable|string|max:255',
            'plans_page' => 'nullable|integer|min:1',
            'realizations_page' => 'nullable|integer|min:1',
        ]);
        
        $month = $validated['month'] ?? now()->format('Y-m');
        $userId = $validated['user_id'] ?? null;
        $projectId = $validated['project_id'] ?? null;
        $search = $validated['search'] ?? null;
        $plansPage = $validated['plans_page'] ?? 1;
        $realizationsPage = $validated['realizations_page'] ?? 1;
        
        try {
            $date = Carbon::parse($month . '-01');
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            $date = Carbon::now()->startOfMonth();
            $month = $date->format('Y-m');
        } catch (\Exception $e) {
            // Fallback for any other exception
            $date = Carbon::now()->startOfMonth();
            $month = $date->format('Y-m');
        }
        
        // Get Work Plans - optimized using date range instead of whereYear/whereMonth
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();
        
        $workPlansQuery = WorkPlan::with(['user', 'project'])
            ->whereBetween('plan_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
        
        if ($userId) {
            $workPlansQuery->where('user_id', $userId);
        }
        
        if ($projectId) {
            $workPlansQuery->where('project_id', $projectId);
        }
        
        if ($search) {
            // Optimize search: only search in title and number (not description for better performance)
            $workPlansQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('work_plan_number', 'like', "%{$search}%")
                  ->orWhere('work_location', 'like', "%{$search}%");
            });
        }
        
        // Paginate with 50 items per page
        $workPlansPaginated = $workPlansQuery->orderBy('plan_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'plans_page', $plansPage);
        
        $workPlans = $workPlansPaginated->getCollection()->map(function($plan) {
            return [
                'id' => $plan->id,
                'type' => 'Rencana Kerja',
                'type_code' => 'work-plan',
                'date' => $plan->plan_date,
                'user' => $plan->user->name ?? '-',
                'user_email' => $plan->user->email ?? '-',
                'number' => $plan->work_plan_number ?? '-',
                'project' => $plan->project->name ?? '-',
                'project_code' => $plan->project->code ?? '-',
                'title' => $plan->title ?? '-',
                'description' => $plan->description ?? '-',
                'location' => $plan->work_location ? $this->getLocationLabel($plan->work_location) : '-',
                'duration' => $plan->planned_duration_hours ?? 0,
                'progress' => null,
                'created_at' => $plan->created_at,
            ];
        });
        
        // Get Work Realizations - optimized using date range instead of whereYear/whereMonth
        $workRealizationsQuery = WorkRealization::with(['user', 'project'])
            ->whereBetween('realization_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
        
        if ($userId) {
            $workRealizationsQuery->where('user_id', $userId);
        }
        
        if ($projectId) {
            $workRealizationsQuery->where('project_id', $projectId);
        }
        
        if ($search) {
            // Optimize search: only search in title and number (not description for better performance)
            $workRealizationsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('realization_number', 'like', "%{$search}%")
                  ->orWhere('work_location', 'like', "%{$search}%");
            });
        }
        
        // Paginate with 50 items per page
        $workRealizationsPaginated = $workRealizationsQuery->orderBy('realization_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'realizations_page', $realizationsPage);
        
        $workRealizations = $workRealizationsPaginated->getCollection()->map(function($realization) {
            return [
                'id' => $realization->id,
                'type' => 'Realisasi Kerja',
                'type_code' => 'work-realization',
                'date' => $realization->realization_date,
                'user' => $realization->user->name ?? '-',
                'user_email' => $realization->user->email ?? '-',
                'number' => $realization->realization_number ?? '-',
                'project' => $realization->project->name ?? '-',
                'project_code' => $realization->project->code ?? '-',
                'title' => $realization->title ?? '-',
                'description' => $realization->description ?? '-',
                'location' => $realization->work_location ? $this->getLocationLabel($realization->work_location) : '-',
                'duration' => $realization->actual_duration_hours ?? 0,
                'progress' => $realization->progress_percentage ?? 0,
                'created_at' => $realization->created_at,
            ];
        });
        
        // Get all users for filter dropdown (cached for 1 hour)
        $users = \App\Helpers\CacheHelper::getEarUsersDropdown();
        
        // Get all projects for filter dropdown (cached for 1 hour)
        $projects = \App\Helpers\CacheHelper::getEarProjectsDropdown();
        
        // Get total counts for header (from paginated results)
        $totalPlans = $workPlansPaginated->total();
        $totalRealizations = $workRealizationsPaginated->total();
        
        return view('admin.ear.index', [
            'workPlans' => $workPlans,
            'workRealizations' => $workRealizations,
            'workPlansPaginated' => $workPlansPaginated,
            'workRealizationsPaginated' => $workRealizationsPaginated,
            'users' => $users,
            'projects' => $projects,
            'selectedMonth' => $month,
            'selectedUserId' => $userId,
            'selectedProjectId' => $projectId,
            'searchQuery' => $search,
            'totalPlans' => $totalPlans,
            'totalRealizations' => $totalRealizations,
        ]);
    }

    /**
     * Get Work Plan detail for modal
     */
    public function getWorkPlanDetail($id)
    {
        // Validate ID
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 400);
        }
        
        // Authorization check - admin or user with EAR module access
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('ear')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $workPlan = WorkPlan::with(['user', 'project'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $workPlan->id,
                'type' => 'Rencana Kerja',
                'title' => $workPlan->title,
                'number' => $workPlan->work_plan_number,
                'date' => $workPlan->plan_date->format('d F Y'),
                'user' => $workPlan->user->name ?? '-',
                'user_email' => $workPlan->user->email ?? '-',
                'project' => $workPlan->project->name ?? '-',
                'project_code' => $workPlan->project->code ?? '-',
                'location' => $workPlan->work_location ? $this->getLocationLabel($workPlan->work_location) : '-',
                'duration' => $workPlan->planned_duration_hours ?? 0,
                'department' => $workPlan->department ?? '-',
                'description' => $workPlan->description ?? '-',
                'expected_output' => $workPlan->expected_output ?? null,
                'created_at' => $workPlan->created_at ? $workPlan->created_at->format('d F Y H:i') : '-',
            ]
        ]);
    }

    /**
     * Get Work Realization detail for modal
     */
    public function getWorkRealizationDetail($id)
    {
        // Validate ID
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 400);
        }
        
        // Authorization check - admin or user with EAR module access
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('ear')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $workRealization = WorkRealization::with(['user', 'project', 'workPlan'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $workRealization->id,
                'type' => 'Realisasi Kerja',
                'title' => $workRealization->title,
                'number' => $workRealization->realization_number,
                'date' => $workRealization->realization_date->format('d F Y'),
                'user' => $workRealization->user->name ?? '-',
                'user_email' => $workRealization->user->email ?? '-',
                'project' => $workRealization->project->name ?? '-',
                'project_code' => $workRealization->project->code ?? '-',
                'location' => $workRealization->work_location ? $this->getLocationLabel($workRealization->work_location) : '-',
                'duration' => $workRealization->actual_duration_hours ?? 0,
                'progress' => min(max($workRealization->progress_percentage ?? 0, 0), 100), // Ensure 0-100 range
                'description' => $workRealization->description ?? '-',
                'output_description' => $workRealization->output_description ?? null,
                'output_files' => $workRealization->output_files ?? [],
                'related_work_plan' => $workRealization->workPlan ? [
                    'id' => $workRealization->workPlan->id,
                    'number' => $workRealization->workPlan->work_plan_number ?? '-',
                    'title' => $workRealization->workPlan->title ?? '-',
                ] : null,
                'created_at' => $workRealization->created_at ? $workRealization->created_at->format('d F Y H:i') : '-',
            ]
        ]);
    }

    /**
     * Get location label from enum or string
     */
    private function getLocationLabel($location)
    {
        if (is_object($location)) {
            return $location->label() ?? ucfirst($location->value ?? '-');
        }
        
        $locationLabels = [
            'office' => 'Office',
            'site' => 'Site',
            'wfh' => 'WFH',
            'wfa' => 'WFA'
        ];
        
        return $locationLabels[$location] ?? ucfirst($location ?? '-');
    }
}
