<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Work\WorkPlanController;
use App\Http\Controllers\Work\WorkRealizationController;
use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\User\LeaveApprovalController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Payment\SpdController;
use App\Http\Controllers\Payment\PurchaseController;
use App\Http\Controllers\Payment\VendorPaymentController;
use App\Http\Controllers\User\PaymentApprovalController;
use App\Http\Controllers\Admin\PaymentApprovalController as AdminPaymentApprovalController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\User\LogController as UserActivityLogController;
use App\Http\Controllers\User\ProjectManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Notifications (accessible by all authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Project search API (for searchable select)
    Route::get('/projects/search', [ProjectController::class, 'search'])->name('projects.search');
    
    // ============================================
    // USER ROUTES (/user/*)
    // ============================================
    Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Activity Log (bawaan - user hanya lihat milik sendiri)
        Route::get('/activity-log', [UserActivityLogController::class, 'index'])->name('activity-log.index');
        
        // Project Management Routes
        Route::get('/project-management', [ProjectManagementController::class, 'index'])->name('project-management.index');
        Route::get('/project-management/{id}', [ProjectManagementController::class, 'show'])->name('project-management.show');
        
        // Work Management Routes
        Route::resource('work-plans', WorkPlanController::class);
        Route::resource('work-realizations', WorkRealizationController::class);
        
        // Leave Management Routes
        Route::resource('leaves', LeaveController::class);
        
        // Leave Approval Routes
        Route::prefix('leave-approvals')->name('leave-approvals.')->middleware('permission:approve-leave')->group(function () {
            Route::get('/', [LeaveApprovalController::class, 'index'])->name('index');
            Route::get('/{leave}', [LeaveApprovalController::class, 'show'])->name('show');
            Route::post('/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('approve');
            Route::post('/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('reject');
        });
        
        // Payment Submission Routes - Each module has its own route
        Route::resource('spd', SpdController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('vendor-payments', VendorPaymentController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        
        // Payment Approval Routes
        Route::prefix('payment-approvals')->name('payment-approvals.')->middleware('permission:approve-payment-submission')->group(function () {
            Route::get('/', [PaymentApprovalController::class, 'index'])->name('index');
            Route::post('/spd/{spd}/approve', [PaymentApprovalController::class, 'approveSpd'])->name('spd.approve');
            Route::post('/spd/{spd}/reject', [PaymentApprovalController::class, 'rejectSpd'])->name('spd.reject');
            Route::post('/purchases/{purchase}/approve', [PaymentApprovalController::class, 'approvePurchase'])->name('purchase.approve');
            Route::post('/purchases/{purchase}/reject', [PaymentApprovalController::class, 'rejectPurchase'])->name('purchase.reject');
            Route::post('/vendor-payments/{vendorPayment}/approve', [PaymentApprovalController::class, 'approveVendorPayment'])->name('vendor-payment.approve');
            Route::post('/vendor-payments/{vendorPayment}/reject', [PaymentApprovalController::class, 'rejectVendorPayment'])->name('vendor-payment.reject');
        });
    });

    // ============================================
    // ADMIN ROUTES (/admin/*)
    // ============================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management Routes
        Route::resource('users', UserManagementController::class);
        
        // Work Management Routes (Admin can access too)
        Route::resource('work-plans', WorkPlanController::class)->names([
            'index' => 'work-plans.index',
            'create' => 'work-plans.create',
            'store' => 'work-plans.store',
            'show' => 'work-plans.show',
            'edit' => 'work-plans.edit',
            'update' => 'work-plans.update',
            'destroy' => 'work-plans.destroy',
        ]);
        Route::resource('work-realizations', WorkRealizationController::class)->names([
            'index' => 'work-realizations.index',
            'create' => 'work-realizations.create',
            'store' => 'work-realizations.store',
            'show' => 'work-realizations.show',
            'edit' => 'work-realizations.edit',
            'update' => 'work-realizations.update',
            'destroy' => 'work-realizations.destroy',
        ]);
        
        // Leave Management Routes
        Route::resource('leaves', LeaveController::class)->names([
            'index' => 'leaves.index',
            'create' => 'leaves.create',
            'store' => 'leaves.store',
            'show' => 'leaves.show',
            'edit' => 'leaves.edit',
            'update' => 'leaves.update',
            'destroy' => 'leaves.destroy',
        ]);
        
        // Payment Submission Routes - Each module has its own route
        Route::resource('spd', SpdController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
            'index' => 'spd.index',
            'show' => 'spd.show',
            'store' => 'spd.store',
            'update' => 'spd.update',
            'destroy' => 'spd.destroy',
        ]);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
            'index' => 'purchases.index',
            'show' => 'purchases.show',
            'store' => 'purchases.store',
            'update' => 'purchases.update',
            'destroy' => 'purchases.destroy',
        ]);
        Route::resource('vendor-payments', VendorPaymentController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
            'index' => 'vendor-payments.index',
            'show' => 'vendor-payments.show',
            'store' => 'vendor-payments.store',
            'update' => 'vendor-payments.update',
            'destroy' => 'vendor-payments.destroy',
        ]);
        
        // Approval Routes (Admin can approve everything)
        Route::prefix('approvals')->name('approvals.')->group(function () {
            // Leave Approvals
            Route::get('leaves', [ApprovalController::class, 'leaves'])->name('leaves');
            Route::get('leaves/{leave}', [ApprovalController::class, 'showLeaveRequest'])->name('leaves.show');
            Route::post('leaves/{leave}/approve', [ApprovalController::class, 'approveLeaveRequest'])->name('leaves.approve');
            Route::post('leaves/{leave}/reject', [ApprovalController::class, 'rejectLeaveRequest'])->name('leaves.reject');
            
            // Payment Approvals
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [AdminPaymentApprovalController::class, 'index'])->name('index');
                Route::get('/spd/{spd}', [AdminPaymentApprovalController::class, 'showSpd'])->name('spd.show');
                Route::post('/spd/{spd}/approve', [AdminPaymentApprovalController::class, 'approveSpd'])->name('spd.approve');
                Route::post('/spd/{spd}/reject', [AdminPaymentApprovalController::class, 'rejectSpd'])->name('spd.reject');
                Route::get('/purchases/{purchase}', [AdminPaymentApprovalController::class, 'showPurchase'])->name('purchase.show');
                Route::post('/purchases/{purchase}/approve', [AdminPaymentApprovalController::class, 'approvePurchase'])->name('purchase.approve');
                Route::post('/purchases/{purchase}/reject', [AdminPaymentApprovalController::class, 'rejectPurchase'])->name('purchase.reject');
                Route::get('/vendor-payments/{vendorPayment}', [AdminPaymentApprovalController::class, 'showVendorPayment'])->name('vendor-payment.show');
                Route::post('/vendor-payments/{vendorPayment}/approve', [AdminPaymentApprovalController::class, 'approveVendorPayment'])->name('vendor-payment.approve');
                Route::post('/vendor-payments/{vendorPayment}/reject', [AdminPaymentApprovalController::class, 'rejectVendorPayment'])->name('vendor-payment.reject');
            });
        });
        
        // Documentation Route
        Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation');
        
        // Activity Log (bawaan - admin lihat semua)
        Route::get('/activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');
        
        // Project Management Routes (Admin can access too)
        Route::get('/project-management', [ProjectManagementController::class, 'index'])->name('project-management.index');
        Route::get('/project-management/create', [ProjectManagementController::class, 'create'])->name('project-management.create');
        Route::post('/project-management', [ProjectManagementController::class, 'store'])->name('project-management.store');
        Route::get('/project-management/{id}', [ProjectManagementController::class, 'show'])->name('project-management.show');
        
        // Project Manager Management Routes (Admin only)
        Route::prefix('project-managers')->name('project-managers.')->group(function () {
            Route::post('/projects/{project}/assign', [\App\Http\Controllers\Admin\ProjectManagerController::class, 'assign'])->name('assign');
            Route::post('/projects/{project}/update-access-type', [\App\Http\Controllers\Admin\ProjectManagerController::class, 'updateAccessType'])->name('update-access-type');
            Route::post('/projects/{project}/remove', [\App\Http\Controllers\Admin\ProjectManagerController::class, 'remove'])->name('remove');
            Route::get('/projects/{project}/available-users', [\App\Http\Controllers\Admin\ProjectManagerController::class, 'getAvailableUsers'])->name('available-users');
        });
    });
    
    // ============================================
    // EAR ROUTES (/admin/ear) - Accessible by Admin or User with EAR module
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/ear', [AdminDashboardController::class, 'ear'])->name('ear');
        Route::get('/ear/work-plan/{id}', [AdminDashboardController::class, 'getWorkPlanDetail'])->name('ear.work-plan.detail');
        Route::get('/ear/work-realization/{id}', [AdminDashboardController::class, 'getWorkRealizationDetail'])->name('ear.work-realization.detail');
    });
});
