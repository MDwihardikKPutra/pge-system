<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\View\Composers\WorkNotificationComposer;
use App\Models\Project;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\LeaveRequest;
use App\Observers\ProjectObserver;
use App\Observers\ActivityObserver;
use App\Observers\ActivityLogObserver;
use App\Policies\WorkPlanPolicy;
use App\Policies\WorkRealizationPolicy;
use App\Policies\LeaveRequestPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        WorkPlan::class => WorkPlanPolicy::class,
        WorkRealization::class => WorkRealizationPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        VendorPayment::class => \App\Policies\VendorPaymentPolicy::class,
        Purchase::class => \App\Policies\PurchasePolicy::class,
        SPD::class => \App\Policies\SpdPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in non-production environments
        Model::preventLazyLoading(!app()->isProduction());

        // Log lazy loading violations for debugging
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $class = get_class($model);
            Log::warning("Lazy loading detected: [{$relation}] on [{$class}]", [
                'model' => $class,
                'relation' => $relation,
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);
        });

        // Register view composer for work notifications
        View::composer('layouts.app', WorkNotificationComposer::class);

        // Register model observers
        Project::observe(ProjectObserver::class);
        
        // Register activity observers for dashboard cache invalidation
        WorkPlan::observe(ActivityObserver::class);
        WorkRealization::observe(ActivityObserver::class);
        SPD::observe(ActivityObserver::class);
        Purchase::observe(ActivityObserver::class);
        VendorPayment::observe(ActivityObserver::class);
        LeaveRequest::observe(ActivityObserver::class);
        
        // Register activity log observers for automatic activity logging
        WorkPlan::observe(ActivityLogObserver::class);
        WorkRealization::observe(ActivityLogObserver::class);
        SPD::observe(ActivityLogObserver::class);
        Purchase::observe(ActivityLogObserver::class);
        VendorPayment::observe(ActivityLogObserver::class);
        LeaveRequest::observe(ActivityLogObserver::class);
    }
}
