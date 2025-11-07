<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;
use App\Enums\ApprovalStatus;

class PurchasePolicy
{
    /**
     * Determine if the user can view the Purchase.
     */
    public function view(User $user, Purchase $purchase): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view their own
        if ($purchase->user_id === $user->id) {
            return true;
        }

        // PM with finance/full access can view
        if ($purchase->project_id && $purchase->project) {
            $accessType = $purchase->project->getManagerAccessType($user->id);
            if (in_array($accessType, ['finance', 'full'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can update the Purchase.
     */
    public function update(User $user, Purchase $purchase): bool
    {
        return $user->id === $purchase->user_id && $purchase->status === ApprovalStatus::PENDING;
    }

    /**
     * Determine if the user can delete the Purchase.
     */
    public function delete(User $user, Purchase $purchase): bool
    {
        return $user->id === $purchase->user_id && $purchase->status === ApprovalStatus::PENDING;
    }
}
