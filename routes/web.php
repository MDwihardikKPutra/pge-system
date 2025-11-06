<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

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
    
    // Include user routes
    Route::prefix('user')->name('user.')->group(function () {
        require __DIR__ . '/user.php';
    });
    
    // Include admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        require __DIR__ . '/admin.php';
    });
    
    // EAR Routes (Accessible by Admin or User with EAR module)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/ear', [AdminDashboardController::class, 'ear'])->name('ear');
        Route::get('/ear/work-plan/{id}', [AdminDashboardController::class, 'getWorkPlanDetail'])->name('ear.work-plan.detail');
        Route::get('/ear/work-realization/{id}', [AdminDashboardController::class, 'getWorkRealizationDetail'])->name('ear.work-realization.detail');
    });
});
