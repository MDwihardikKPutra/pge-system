<?php

namespace App\Observers;

use App\Models\Project;
use App\Helpers\CacheHelper;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        CacheHelper::clearProjectCaches();
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        CacheHelper::clearProjectCaches();
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        CacheHelper::clearProjectCaches();
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        CacheHelper::clearProjectCaches();
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        CacheHelper::clearProjectCaches();
    }
}


