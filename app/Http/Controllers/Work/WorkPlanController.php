<?php

namespace App\Http\Controllers\Work;

use App\Http\Controllers\Controller;
use App\Models\WorkPlan;
use App\Models\Project;
use App\Services\WorkManagementService;
use App\Traits\ChecksAuthorization;
use App\Constants\WorkTimeLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class WorkPlanController extends Controller
{
    use AuthorizesRequests, ChecksAuthorization;

    protected $workService;

    public function __construct(WorkManagementService $workService)
    {
        $this->workService = $workService;
    }

    /**
     * Display a listing of work plans
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        // Get project IDs where user has work access (PM or Full)
        $managedProjectIds = $isAdmin ? [] : $user->managedProjectsWithWorkAccess()->pluck('projects.id')->toArray();
        
        $query = WorkPlan::with(['project.managers', 'user'])
            ->when($month, function ($q) use ($month) {
                $date = Carbon::parse($month . '-01');
                return $q->whereYear('plan_date', $date->year)
                        ->whereMonth('plan_date', $date->month);
            });
        
        // Filter: User can see their own work plans OR work plans from projects they manage
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query->where(function($q) use ($user, $managedProjectIds, $isAdmin) {
            $q->where('user_id', $user->id); // Own work plans
            
            // OR work plans from managed projects (for non-admin users)
            if (!$isAdmin && !empty($managedProjectIds)) {
                $q->orWhereIn('project_id', $managedProjectIds);
            }
        });
        
        $workPlans = $query->orderBy('plan_date', 'desc')->paginate(20);
        
        // Get active projects for dropdown (cached)
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
            
        return view('work.work-plans.index', compact('workPlans', 'month', 'projects'));
    }

    /**
     * Show the form for creating a new work plan
     */
    public function create()
    {
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
            
        return view('work.work-plans.create', compact('projects'));
    }

    /**
     * Store a newly created work plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_date' => 'required|date',
            'work_location' => 'required|string|in:site,office,wfh,wfa',
            'description' => 'required|string',
            'objectives' => 'nullable|array',
            'expected_output' => 'nullable|string',
            'planned_duration_hours' => 'required|numeric|min:0.5|max:24',
        ]);

        try {
            DB::beginTransaction();

            $validated['department'] = auth()->user()->department;
            $workPlan = $this->workService->createWorkPlan($validated, auth()->id());

            DB::commit();

            return redirect()->route('user.work-plans.index')
                ->with('success', 'Rencana kerja berhasil diajukan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\WorkException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'WorkPlan', $e, null, $request->except(['_token']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'WorkPlan', $e, null, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat mengajukan rencana kerja. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Display the specified work plan
     */
    public function show(WorkPlan $workPlan)
    {
        // Load basic relations first
        $workPlan->load(['user', 'realizations', 'project']);
        
        // Load project managers if project exists (needed for policy check)
        if ($workPlan->project_id && $workPlan->project) {
            $workPlan->load('project.managers');
        }
        
        $this->authorize('view', $workPlan);
        
        // Return JSON for AJAX requests (preview modal)
        if (request()->wantsJson() || request()->ajax()) {
            // Ensure work_location is serialized as string value
            $workPlanData = $workPlan->toArray();
            if (isset($workPlanData['work_location']) && is_object($workPlan->work_location)) {
                $workPlanData['work_location'] = $workPlan->work_location->value;
            }
            
            return response()->json([
                'workPlan' => $workPlanData,
            ]);
        }
        
        return view('work.work-plans.show', compact('workPlan'));
    }

    /**
     * Show the form for editing the specified work plan
     */
    public function edit(WorkPlan $workPlan)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workPlan->user_id !== auth()->id() && $workPlan->project_id) {
            $workPlan->load('project.managers');
        }
        
        $this->authorize('update', $workPlan);

        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();

        return view('work.work-plans.edit', compact('workPlan', 'projects'));
    }

    /**
     * Update the specified work plan
     */
    public function update(Request $request, WorkPlan $workPlan)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workPlan->user_id !== auth()->id() && $workPlan->project_id) {
            $workPlan->load('project.managers');
        }
        
        $this->authorize('update', $workPlan);

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'plan_date' => 'required|date',
            'work_location' => 'required|string|in:site,office,wfh,wfa',
            'description' => 'required|string',
            'objectives' => 'nullable|array',
            'expected_output' => 'nullable|string',
            'planned_duration_hours' => 'required|numeric|min:0.5|max:24',
        ]);

        try {
            // Auto-generate title from description if not provided
            if (empty($validated['title'])) {
                $validated['title'] = mb_substr($validated['description'], 0, 50);
            }

            $workPlan->update($validated);

            return redirect()->route('user.work-plans.index')
                ->with('success', 'Rencana kerja berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\WorkException $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'WorkPlan', $e, $workPlan->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'WorkPlan', $e, $workPlan->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat memperbarui rencana kerja. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified work plan
     */
    public function destroy(WorkPlan $workPlan)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workPlan->user_id !== auth()->id() && $workPlan->project_id) {
            $workPlan->load('project.managers');
        }
        
        $this->authorize('delete', $workPlan);

        try {
            $workPlan->delete();

            return redirect()->route('user.work-plans.index')
                ->with('success', 'Rencana kerja berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\WorkException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'WorkPlan', $e, $workPlan->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'WorkPlan', $e, $workPlan->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus rencana kerja. Silakan coba lagi.');
        }
    }
}
