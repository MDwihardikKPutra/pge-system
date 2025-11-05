<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cache;

/**
 * Observer untuk clear cache dashboard recent activities dan module data
 * ketika ada perubahan data yang mempengaruhi dashboard
 */
class ActivityObserver
{
    /**
     * Clear dashboard cache when any activity is created, updated, or deleted
     */
    public function created($model): void
    {
        $this->clearDashboardCaches();
    }

    public function updated($model): void
    {
        $this->clearDashboardCaches();
    }

    public function deleted($model): void
    {
        $this->clearDashboardCaches();
    }

    /**
     * Clear all dashboard-related caches using cache tags
     */
    protected function clearDashboardCaches(): void
    {
        // Use cache tags for efficient grouped invalidation
        CacheHelper::clearDashboardCaches();
    }
}

