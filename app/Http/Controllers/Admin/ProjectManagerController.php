<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Constants\ProjectAccessType;
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
            'access_type' => ['required', 'in:' . implode(',', ProjectAccessType::all())]
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

        return response()->json([
            'success' => true,
            'message' => 'Project Manager berhasil ditambahkan dengan akses: ' . ProjectAccessType::label($request->access_type),
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
            'access_type' => ['required', 'in:' . implode(',', ProjectAccessType::all())]
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

        return response()->json([
            'success' => true,
            'message' => 'Access type berhasil diupdate menjadi: ' . ProjectAccessType::label($request->access_type)
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
        // Get all active users except admins - always fresh from database
        // Use fresh() to ensure we get the latest data from database
        $users = User::where('is_active', true)
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            })
            ->orderBy('name')
            ->get()
            ->fresh() // Force fresh data
            ->map(function($user) use ($project) {
                $accessType = $project->getManagerAccessType($user->id);
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_manager' => $project->hasManager($user->id),
                    'access_type' => $accessType,
                    'access_type_label' => $accessType ? ProjectAccessType::label($accessType) : null
                ];
            });

        // Prevent caching of this response
        return response()->json($users)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
