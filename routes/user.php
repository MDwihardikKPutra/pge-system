<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProjectManagementController;
use App\Http\Controllers\Work\WorkPlanController;
use App\Http\Controllers\Work\WorkRealizationController;
use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\User\LeaveApprovalController;
use App\Http\Controllers\Payment\SpdController;
use App\Http\Controllers\Payment\PurchaseController;
use App\Http\Controllers\Payment\VendorPaymentController;
use App\Http\Controllers\User\PaymentApprovalController;
use App\Http\Controllers\User\LogController as UserActivityLogController;

Route::middleware('role:user')->group(function () {
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
    Route::get('/leaves/{leave}/attachment/download', [LeaveController::class, 'downloadAttachment'])->name('user.leaves.attachment.download');
    
    // Leave Approval Routes (permission checked in controller)
    // Uses same view as admin - shared view at admin.approvals.leaves.index
    Route::prefix('leave-approvals')->name('leave-approvals.')->group(function () {
        Route::get('/', [LeaveApprovalController::class, 'index'])->name('index');
        Route::get('/{leave}', [LeaveApprovalController::class, 'show'])->name('show');
        Route::get('/{leave}/attachment/download', [LeaveApprovalController::class, 'downloadAttachment'])->name('attachment.download');
        Route::post('/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('reject');
    });
    
    // Payment Submission Routes - Each module has its own route
    Route::resource('spd', SpdController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('vendor-payments', VendorPaymentController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    
    // Payment Approval Routes (permission checked in controller)
    // Uses same view as admin - shared view at admin.approvals.payments.index
    Route::prefix('payment-approvals')->name('payment-approvals.')->group(function () {
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
