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
use Carbon\Carbon;

class WorkPlanController extends Controller
{
    use ChecksAuthorization;

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
        
        $query = WorkPlan::with(['project', 'user'])
            ->when($month, function ($q) use ($month) {
                $date = Carbon::parse($month . '-01');
                return $q->whereYear('plan_date', $date->year)
                        ->whereMonth('plan_date', $date->month);
            });
        
        // Filter: User can see their own work plans OR work plans from projects they manage
        if ($isAdmin) {
            // Admin sees all
        } else {
            $query->where(function($q) use ($user, $managedProjectIds) {
                $q->where('user_id', $user->id); // Own work plans
                
                // OR work plans from managed projects
                if (!empty($managedProjectIds)) {
                    $q->orWhereIn('project_id', $managedProjectIds);
                }
            });
        }
        
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

        // Validasi waktu: Rencana kerja harus diisi sebelum jam deadline
        $planDate = Carbon::parse($validated['plan_date']);
        $now = Carbon::now();
        
        if ($planDate->isToday() && $now->hour >= WorkTimeLimits::WORK_PLAN_DEADLINE_HOUR) {
            return back()->withErrors([
                'plan_date' => 'Rencana kerja hari ini harus diisi sebelum jam ' . WorkTimeLimits::WORK_PLAN_DEADLINE_HOUR . ':00 pagi.'
            ])->withInput();
        }

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
        $this->authorize('view', $workPlan);

        $workPlan->load(['user', 'realizations', 'project']);
        
        // Return JSON for AJAX requests (preview modal)
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'workPlan' => $workPlan,
            ]);
        }
        
        return view('work.work-plans.show', compact('workPlan'));
    }

    /**
     * Show the form for editing the specified work plan
     */
    public function edit(WorkPlan $workPlan)
    {
        $this->authorize('update', $workPlan);

        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();

        return view('work.work-plans.edit', compact('workPlan', 'projects'));
    }

    /**
     * Update the specified work plan
     */
    public function update(Request $request, WorkPlan $workPlan)
    {
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
