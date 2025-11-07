<?php

namespace App\Policies;

use App\Models\SPD;
use App\Models\User;
use App\Enums\ApprovalStatus;

class SpdPolicy
{
    /**
     * Determine if the user can view the SPD.
     */
    public function view(User $user, SPD $spd): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view their own
        if ($spd->user_id === $user->id) {
            return true;
        }

        // PM with finance/full access can view
        if ($spd->project_id && $spd->project) {
            $accessType = $spd->project->getManagerAccessType($user->id);
            if (in_array($accessType, ['finance', 'full'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can update the SPD.
     */
    public function update(User $user, SPD $spd): bool
    {
        return $user->id === $spd->user_id && $spd->status === ApprovalStatus::PENDING;
    }

    /**
     * Determine if the user can delete the SPD.
     */
    public function delete(User $user, SPD $spd): bool
    {
        return $user->id === $spd->user_id && $spd->status === ApprovalStatus::PENDING;
    }
}
