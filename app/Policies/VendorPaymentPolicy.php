<?php

namespace App\Policies;

use App\Models\VendorPayment;
use App\Models\User;
use App\Enums\ApprovalStatus;

class VendorPaymentPolicy
{
    /**
     * Determine if the user can view the Vendor Payment.
     */
    public function view(User $user, VendorPayment $vendorPayment): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view their own
        if ($vendorPayment->user_id === $user->id) {
            return true;
        }

        // PM with finance/full access can view
        if ($vendorPayment->project_id && $vendorPayment->project) {
            $accessType = $vendorPayment->project->getManagerAccessType($user->id);
            if (in_array($accessType, ['finance', 'full'])) {
                return true;
            }
        }

        return false;
    }

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
