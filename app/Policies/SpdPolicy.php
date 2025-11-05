<?php

namespace App\Policies;

use App\Models\SPD;
use App\Models\User;
use App\Enums\ApprovalStatus;

class SpdPolicy
{
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
