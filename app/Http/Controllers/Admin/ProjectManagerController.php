<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    /**
     * Assign Project Manager to a project with access type
     */
    public function assign(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_type' => 'required|in:pm,finance,full'
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user already is Project Manager
        if ($project->hasManager($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah menjadi Project Manager untuk project ini. Silakan update access type jika perlu.'
            ], 422);
        }

        // Attach user as Project Manager with access type
        $project->managers()->attach($user->id, [
            'access_type' => $request->access_type
        ]);

        $accessTypeLabels = [
            'pm' => 'Project Manager (Work Plans & Realizations)',
            'finance' => 'Finance (Payments Only)',
            'full' => 'Full Access (All)'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Project Manager berhasil ditambahkan dengan akses: ' . $accessTypeLabels[$request->access_type],
            'manager' => $user->load('managedProjects')
        ]);
    }

    /**
     * Update access type for existing Project Manager
     */
    public function updateAccessType(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_type' => 'required|in:pm,finance,full'
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is Project Manager
        if (!$project->hasManager($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User bukan Project Manager untuk project ini'
            ], 422);
        }

        // Update access type
        $project->managers()->updateExistingPivot($user->id, [
            'access_type' => $request->access_type
        ]);

        $accessTypeLabels = [
            'pm' => 'Project Manager (Work Plans & Realizations)',
            'finance' => 'Finance (Payments Only)',
            'full' => 'Full Access (All)'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Access type berhasil diupdate menjadi: ' . $accessTypeLabels[$request->access_type]
        ]);
    }

    /**
     * Remove Project Manager from a project
     */
    public function remove(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is Project Manager
        if (!$project->hasManager($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User bukan Project Manager untuk project ini'
            ], 422);
        }

        // Detach user as Project Manager
        $project->managers()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Project Manager berhasil dihapus'
        ]);
    }

    /**
     * Get all users who can be assigned as Project Manager
     */
    public function getAvailableUsers(Project $project)
    {
        // Get all active users except admins
        $users = User::where('is_active', true)
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            })
            ->orderBy('name')
            ->get()
            ->map(function($user) use ($project) {
                $accessType = $project->getManagerAccessType($user->id);
                $accessTypeLabels = [
                    'pm' => 'Project Manager (Work Plans & Realizations)',
                    'finance' => 'Finance (Payments Only)',
                    'full' => 'Full Access (All)'
                ];
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_manager' => $project->hasManager($user->id),
                    'access_type' => $accessType,
                    'access_type_label' => $accessType ? ($accessTypeLabels[$accessType] ?? 'Unknown') : null
                ];
            });

        return response()->json($users);
    }
}
