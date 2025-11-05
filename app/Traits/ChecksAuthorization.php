<?php

namespace App\Traits;

trait ChecksAuthorization
{
    /**
     * Check if current user is admin
     */
    protected function isAdmin(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /**
     * Check if current user can access resource (owner, admin, or PM of related project)
     */
    protected function canAccessResource(int $resourceUserId, ?int $projectId = null): bool
    {
        $user = auth()->user();
        
        // Admin can access everything
        if ($this->isAdmin()) {
            return true;
        }
        
        // Owner can access their own resources
        if ($resourceUserId === $user->id) {
            return true;
        }
        
        // If resource has project_id, check if user is PM of that project
        if ($projectId) {
            $project = \App\Models\Project::find($projectId);
            if ($project && $project->hasManager($user->id)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Abort if user cannot access resource
     */
    protected function authorizeResourceAccess(int $resourceUserId, string $message = 'Anda tidak memiliki akses ke resource ini', ?int $projectId = null): void
    {
        if (!$this->canAccessResource($resourceUserId, $projectId)) {
            abort(403, $message);
        }
    }
}

