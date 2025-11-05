<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;
use App\Enums\ApprovalStatus;

class PurchasePolicy
{
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
