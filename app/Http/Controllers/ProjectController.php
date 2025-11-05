<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Search projects for autocomplete/searchable select
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $projectId = $request->get('id');
        $limit = min((int) $request->get('limit', 20), 100); // Max 100 items

        // If ID is provided, fetch specific project
        if ($projectId) {
            // Validate projectId is numeric to prevent SQL injection
            if (!is_numeric($projectId)) {
                return response()->json([
                    'projects' => [],
                    'error' => 'Invalid project ID'
                ], 400);
            }

            $project = Project::active()
                ->where('id', (int) $projectId)
                ->first(['id', 'name', 'code']);

            return response()->json([
                'projects' => $project ? [[
                    'id' => $project->id,
                    'name' => $project->name,
                    'code' => $project->code,
                    'display' => $project->name . ' (' . $project->code . ')',
                ]] : [],
            ]);
        }

        $projects = Project::active()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('code', 'like', "%{$query}%");
                });
            })
            ->orderedByName()
            ->limit($limit)
            ->get(['id', 'name', 'code']);

        return response()->json([
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'code' => $project->code,
                    'display' => $project->name . ' (' . $project->code . ')',
                ];
            }),
        ]);
    }
}

