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
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ChecksAuthorization;
    /**
     * Display user dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get active modules for this user
        $activeModules = $user->getActiveModules();
        
        // Get recent activities from all user's assigned modules
        $recentActivities = $this->getUserRecentActivities($user, $activeModules);

        return view('user.dashboard', compact('recentActivities', 'activeModules'));
    }

    /**
     * Get recent activities from all user's assigned modules
     */
    protected function getUserRecentActivities($user, $activeModules)
    {
        $activities = collect();
        
        foreach ($activeModules as $module) {
            $moduleKey = $module->key;
            
            switch ($moduleKey) {
                case 'work-plan':
                    $workPlans = WorkPlan::where('user_id', $user->id)
                        ->with('project')
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
                            'description' => $plan->title ?? \Illuminate\Support\Str::limit($plan->description, 40),
                            'full_description' => $plan->description ?? '-',
                            'number' => $plan->work_plan_number ?? '-',
                            'project' => $plan->project->name ?? '-',
                            'project_code' => $plan->project->code ?? '-',
                            'location' => $plan->work_location ?? '-',
                            'duration' => $plan->planned_duration_hours ?? 0,
                            'date' => $plan->plan_date,
                            'extra' => null,
                            'route' => route('user.work-plans.index'),
                        ]);
                    }
                    break;
                    
                case 'work-realization':
                    $workRealizations = WorkRealization::where('user_id', $user->id)
                        ->with('project')
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
                            'route' => route('user.work-realizations.index'),
                        ]);
                    }
                    break;
                    
                case 'spd':
                    $spds = SPD::where('user_id', $user->id)
                        ->with(['project', 'approvedBy'])
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
                            'route' => route('user.spd.index'),
                        ]);
                    }
                    break;
                    
                case 'purchase':
                    $purchases = Purchase::where('user_id', $user->id)
                        ->with(['project', 'approvedBy'])
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
                            'route' => route('user.purchases.index'),
                        ]);
                    }
                    break;
                    
                case 'vendor-payment':
                    $vendorPayments = VendorPayment::where('user_id', $user->id)
                        ->with(['user', 'vendor', 'project', 'approvedBy'])
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
                            'route' => route('user.vendor-payments.index'),
                        ]);
                    }
                    break;
                    
                case 'leave':
                    $leaves = LeaveRequest::where('user_id', $user->id)
                        ->with(['leaveType', 'approvedBy'])
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
                            'description' => $leave->leave_number . ' - ' . ($leave->leaveType->name ?? '-'),
                            'number' => $leave->leave_number ?? '-',
                            'leave_type' => $leave->leaveType->name ?? '-',
                            'start_date' => $leave->start_date ? $leave->start_date->format('d M Y') : '-',
                            'end_date' => $leave->end_date ? $leave->end_date->format('d M Y') : '-',
                            'reason' => $leave->reason ?? '-',
                            'status' => $statusValue,
                            'approved_by' => $leave->approvedBy->name ?? null,
                            'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : null,
                            'date' => $leave->created_at,
                            'extra' => $statusValue,
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
                            'icon' => '📁',
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
            }
        }
        
        // Sort by date (most recent first) and take top 20
        return $activities->sortByDesc(function ($activity) {
            return $activity['date'] instanceof \Carbon\Carbon ? $activity['date']->timestamp : strtotime($activity['date']);
        })->take(20)->values();
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
