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
        
        // Get current month statistics
        $currentMonth = now()->format('Y-m');
        $startOfMonth = Carbon::parse($currentMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($currentMonth . '-01')->endOfMonth();
        
        // Prepare module data with preview
        $moduleData = [];
        
        foreach ($activeModules as $module) {
            $moduleKey = $module->key;
            $data = [
                'module' => $module,
                'count' => 0,
                'recent' => collect(),
                'route' => null,
            ];
            
            // Get route
            $routes = $module->routes ?? [];
            if (isset($routes['index'])) {
                $data['route'] = $routes['index'];
            }
            
            // Get data based on module key
            switch ($moduleKey) {
                case 'work-plan':
                    $data['count'] = WorkPlan::where('user_id', $user->id)
                        ->whereBetween('plan_date', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = WorkPlan::where('user_id', $user->id)
                        ->orderBy('plan_date', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'work-realization':
                    $data['count'] = WorkRealization::where('user_id', $user->id)
                        ->whereBetween('realization_date', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = WorkRealization::where('user_id', $user->id)
                        ->orderBy('realization_date', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'spd':
                    $data['count'] = Spd::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = Spd::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'purchase':
                    $data['count'] = Purchase::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = Purchase::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'vendor-payment':
                    $data['count'] = VendorPayment::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = VendorPayment::where('user_id', $user->id)
                        ->with('vendor')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'leave':
                    $data['count'] = LeaveRequest::where('user_id', $user->id)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                    $data['recent'] = LeaveRequest::where('user_id', $user->id)
                        ->with('leaveType')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    break;
                    
                case 'project-management':
                case 'project-monitoring':
                    $managedProjects = Project::whereHas('managers', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })->active()->count();
                    $data['count'] = $managedProjects;
                    $data['recent'] = Project::whereHas('managers', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })->active()
                      ->orderedByName()
                      ->limit(3)
                      ->get();
                    break;
                    
                case 'payment-approval':
                    // Get pending approvals count (only for user's own if not admin)
                    if ($this->isAdmin()) {
                        $data['count'] = \App\Models\SPD::where('status', 'pending')->count() +
                                       \App\Models\Purchase::where('status', 'pending')->count() +
                                       \App\Models\VendorPayment::where('status', 'pending')->count();
                    } else {
                        // For non-admin, count only approvals assigned to them
                        $data['count'] = 0; // Will be handled by approval controller
                    }
                    $data['recent'] = collect();
                    break;
            }
            
            $moduleData[$moduleKey] = $data;
        }

        return view('user.dashboard', compact('moduleData', 'activeModules'));
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
        $spdQuery = Spd::query();
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
