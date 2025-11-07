<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\PaymentApprovalController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\ProjectManagerController;
use App\Http\Controllers\Work\WorkPlanController;
use App\Http\Controllers\Work\WorkRealizationController;
use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\Payment\SpdController;
use App\Http\Controllers\Payment\PurchaseController;
use App\Http\Controllers\Payment\VendorPaymentController;
use App\Http\Controllers\User\ProjectManagementController;

Route::middleware('role:admin')->group(function () {
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
    Route::get('/leaves/{leave}/attachment/download', [LeaveController::class, 'downloadAttachment'])->name('leaves.attachment.download');
    Route::get('/leaves/{leave}/pdf', [LeaveController::class, 'downloadPDF'])->name('leaves.pdf');
    
    // Payment Submission Routes - Each module has its own route
    Route::resource('spd', SpdController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
        'index' => 'spd.index',
        'show' => 'spd.show',
        'store' => 'spd.store',
        'update' => 'spd.update',
        'destroy' => 'spd.destroy',
    ]);
    Route::get('/spd/{spd}/pdf', [SpdController::class, 'downloadPDF'])->name('spd.pdf');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
        'index' => 'purchases.index',
        'show' => 'purchases.show',
        'store' => 'purchases.store',
        'update' => 'purchases.update',
        'destroy' => 'purchases.destroy',
    ]);
    Route::get('/purchases/{purchase}/pdf', [PurchaseController::class, 'downloadPDF'])->name('purchases.pdf');
    Route::resource('vendor-payments', VendorPaymentController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->names([
        'index' => 'vendor-payments.index',
        'show' => 'vendor-payments.show',
        'store' => 'vendor-payments.store',
        'update' => 'vendor-payments.update',
        'destroy' => 'vendor-payments.destroy',
    ]);
    Route::get('/vendor-payments/{vendorPayment}/pdf', [VendorPaymentController::class, 'downloadPDF'])->name('vendor-payments.pdf');
    
    // Approval Routes (Admin can approve everything)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        // Leave Approvals
        Route::get('leaves', [ApprovalController::class, 'leaves'])->name('leaves');
        Route::get('leaves/{leave}', [ApprovalController::class, 'showLeaveRequest'])->name('leaves.show');
        Route::get('leaves/{leave}/attachment/download', [ApprovalController::class, 'downloadAttachment'])->name('leaves.attachment.download');
        Route::post('leaves/{leave}/approve', [ApprovalController::class, 'approveLeaveRequest'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [ApprovalController::class, 'rejectLeaveRequest'])->name('leaves.reject');
        
        // Payment Approvals
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentApprovalController::class, 'index'])->name('index');
            Route::get('/spd/{spd}', [PaymentApprovalController::class, 'showSpd'])->name('spd.show');
            Route::post('/spd/{spd}/approve', [PaymentApprovalController::class, 'approveSpd'])->name('spd.approve');
            Route::post('/spd/{spd}/reject', [PaymentApprovalController::class, 'rejectSpd'])->name('spd.reject');
            Route::get('/purchases/{purchase}', [PaymentApprovalController::class, 'showPurchase'])->name('purchase.show');
            Route::post('/purchases/{purchase}/approve', [PaymentApprovalController::class, 'approvePurchase'])->name('purchase.approve');
            Route::post('/purchases/{purchase}/reject', [PaymentApprovalController::class, 'rejectPurchase'])->name('purchase.reject');
            Route::get('/vendor-payments/{vendorPayment}', [PaymentApprovalController::class, 'showVendorPayment'])->name('vendor-payment.show');
            Route::post('/vendor-payments/{vendorPayment}/approve', [PaymentApprovalController::class, 'approveVendorPayment'])->name('vendor-payment.approve');
            Route::post('/vendor-payments/{vendorPayment}/reject', [PaymentApprovalController::class, 'rejectVendorPayment'])->name('vendor-payment.reject');
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
        Route::post('/projects/{project}/assign', [ProjectManagerController::class, 'assign'])->name('assign');
        Route::post('/projects/{project}/update-access-type', [ProjectManagerController::class, 'updateAccessType'])->name('update-access-type');
        Route::post('/projects/{project}/remove', [ProjectManagerController::class, 'remove'])->name('remove');
        Route::get('/projects/{project}/available-users', [ProjectManagerController::class, 'getAvailableUsers'])->name('available-users');
    });
    
    // EAR Routes (Accessible by Admin or User with EAR module)
    Route::get('/ear', [AdminDashboardController::class, 'ear'])->name('ear');
    Route::get('/ear/work-plan/{id}', [AdminDashboardController::class, 'getWorkPlanDetail'])->name('ear.work-plan.detail');
    Route::get('/ear/work-realization/{id}', [AdminDashboardController::class, 'getWorkRealizationDetail'])->name('ear.work-realization.detail');
});
