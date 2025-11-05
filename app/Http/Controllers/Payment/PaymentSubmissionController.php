<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\Project;
use App\Models\Vendor;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;

class PaymentSubmissionController extends Controller
{
    use ChecksAuthorization;

    /**
     * Display the payment submission index page
     */
    public function index(Request $request)
    {
        $isAdmin = $this->isAdmin();
        $routePrefix = $isAdmin ? 'admin' : 'user';
        
        // Get active tab
        $activeTab = $request->get('tab', 'spd');
        
        // Get SPD data
        $spdQuery = SPD::with(['project', 'approvedBy', 'user']);
        if (!$isAdmin) {
            $spdQuery->where('user_id', auth()->id());
        }
        $spds = $spdQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'spd_page');
        
        // Get Purchase data
        $purchaseQuery = Purchase::with(['project', 'approvedBy', 'user']);
        if (!$isAdmin) {
            $purchaseQuery->where('user_id', auth()->id());
        }
        $purchases = $purchaseQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'purchase_page');
        
        // Get Vendor Payment data
        $vpQuery = VendorPayment::with(['project', 'vendor', 'approvedBy', 'user']);
        if (!$isAdmin) {
            $vpQuery->where('user_id', auth()->id());
        }
        $vendorPayments = $vpQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'vp_page');
        
        // Get options for forms
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
        $vendors = Vendor::active()->orderedByName()->get();
        
        // Stats
        $spdCount = $isAdmin ? SPD::count() : SPD::where('user_id', auth()->id())->count();
        $purchaseCount = $isAdmin ? Purchase::count() : Purchase::where('user_id', auth()->id())->count();
        $vendorPaymentCount = $isAdmin ? VendorPayment::count() : VendorPayment::where('user_id', auth()->id())->count();
        
        return view('payment.index', compact(
            'isAdmin', 
            'routePrefix', 
            'activeTab',
            'spds', 
            'purchases', 
            'vendorPayments',
            'projects',
            'vendors',
            'spdCount',
            'purchaseCount',
            'vendorPaymentCount'
        ));
    }
}

