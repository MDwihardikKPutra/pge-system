<?php

namespace App\Policies;

use App\Models\VendorPayment;
use App\Models\User;
use App\Enums\ApprovalStatus;

class VendorPaymentPolicy
{
    /**
     * Determine if the user can update the Vendor Payment.
     */
    public function update(User $user, VendorPayment $vendorPayment): bool
    {
        return $user->id === $vendorPayment->user_id && $vendorPayment->status === ApprovalStatus::PENDING;
    }

    /**
     * Determine if the user can delete the Vendor Payment.
     */
    public function delete(User $user, VendorPayment $vendorPayment): bool
    {
        return $user->id === $vendorPayment->user_id && $vendorPayment->status === ApprovalStatus::PENDING;
    }
}
