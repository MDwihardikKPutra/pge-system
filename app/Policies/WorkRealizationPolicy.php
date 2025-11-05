<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkRealization;

class WorkRealizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all, users can view their own (filtered in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkRealization $workRealization): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view their own
        if ($workRealization->user_id === $user->id) {
            return true;
        }

        // PM can view if they manage the project (with work access: pm or full)
        if ($workRealization->project_id) {
            $project = $workRealization->project;
            if ($project) {
                $accessType = $project->getManagerAccessType($user->id);
                if (in_array($accessType, ['pm', 'full'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create work realizations
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkRealization $workRealization): bool
    {
        // Admin can update all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only owner can update (PM can view but not edit)
        return $workRealization->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkRealization $workRealization): bool
    {
        // Admin can delete all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only owner can delete (PM can view but not delete)
        return $workRealization->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkRealization $workRealization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkRealization $workRealization): bool
    {
        return $user->hasRole('admin');
    }
}
