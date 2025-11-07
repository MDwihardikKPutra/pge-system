<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view leave requests (filtered by ownership in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // User can only view their own leave requests
        return $leaveRequest->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create leave requests
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        // Can only update if status is pending (applies to both admin and user)
        if ($leaveRequest->status->value !== 'pending') {
            return false;
        }

        // Admin can update all pending leave requests
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only owner can update their own pending leave requests
        return $leaveRequest->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        // Admin can delete all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only owner can delete, and only if status is pending
        if ($leaveRequest->user_id !== $user->id) {
            return false;
        }

        // Can only delete if status is pending
        return $leaveRequest->status->value === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('admin');
    }
}
