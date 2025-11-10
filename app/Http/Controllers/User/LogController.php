<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display activity logs for current user only
     */
    public function index(Request $request)
    {
        $query = ActivityLog::where('user_id', auth()->id())->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $activityLogs = $query->paginate(50)->withQueryString();

        // Get unique actions and model types for filters (only for current user, only non-null)
        $actions = ActivityLog::where('user_id', auth()->id())
            ->whereNotNull('action')
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();
        $modelTypes = ActivityLog::where('user_id', auth()->id())
            ->whereNotNull('model_type')
            ->distinct()
            ->pluck('model_type')
            ->filter()
            ->sort()
            ->values();

        return view('user.activity-log.index', compact('activityLogs', 'actions', 'modelTypes'));
    }
}
