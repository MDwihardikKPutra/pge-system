<?php

namespace App\Http\Controllers\Work;

use App\Http\Controllers\Controller;
use App\Models\WorkRealization;
use App\Models\WorkPlan;
use App\Models\Project;
use App\Services\WorkManagementService;
use App\Traits\ChecksAuthorization;
use App\Constants\WorkTimeLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class WorkRealizationController extends Controller
{
    use AuthorizesRequests, ChecksAuthorization;

    protected $workService;

    public function __construct(WorkManagementService $workService)
    {
        $this->workService = $workService;
    }

    /**
     * Display a listing of work realizations
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        // Get project IDs where user has work access (PM or Full)
        $managedProjectIds = $isAdmin ? [] : $user->managedProjectsWithWorkAccess()->pluck('projects.id')->toArray();
        
        $query = WorkRealization::with(['workPlan', 'project', 'user'])
            ->when($month, function ($q) use ($month) {
                $date = Carbon::parse($month . '-01');
                return $q->whereYear('realization_date', $date->year)
                            ->whereMonth('realization_date', $date->month);
            });
        
        // Filter: User can see their own work realizations OR work realizations from projects they manage
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query->where(function($q) use ($user, $managedProjectIds, $isAdmin) {
            $q->where('user_id', $user->id); // Own work realizations
            
            // OR work realizations from managed projects (for non-admin users)
            if (!$isAdmin && !empty($managedProjectIds)) {
                $q->orWhereIn('project_id', $managedProjectIds);
            }
        });
        
        $workRealizations = $query->orderBy('realization_date', 'desc')->paginate(20);
        
        // Get work plans for dropdown in modal (only own work plans)
        $workPlans = WorkPlan::where('user_id', auth()->id())
            ->whereDate('plan_date', '>=', now()->subDays(30))
            ->orderBy('plan_date', 'desc')
            ->get();
        
        // Get active projects for dropdown (cached)
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
            
        return view('work.work-realizations.index', compact('workRealizations', 'month', 'workPlans', 'projects'));
    }

    /**
     * Show the form for creating a new work realization
     */
    public function create()
    {
        $workPlans = WorkPlan::where('user_id', auth()->id())
            ->whereDate('plan_date', '>=', now()->subDays(30))
            ->orderBy('plan_date', 'desc')
            ->get();
        
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
            
        return view('work.work-realizations.create', compact('workPlans', 'projects'));
    }

    /**
     * Store a newly created work realization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'realization_date' => 'required|date',
            'work_plan_id' => 'nullable|exists:work_plans,id',
            'work_location' => 'required|string|in:site,office,wfh,wfa',
            'description' => 'required|string',
            'achievements' => 'nullable|array',
            'output_description' => 'nullable|string',
            'output_files' => 'nullable|array',
            'output_files.*' => 'file|max:10240', // Max 10MB per file
            'actual_duration_hours' => 'required|numeric|min:0.5|max:24',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Remove output_files from validated since we'll handle it separately
        unset($validated['output_files']);

        // Validasi waktu: Realisasi kerja harus diisi sebelum jam deadline
        $realizationDate = Carbon::parse($validated['realization_date']);
        $now = Carbon::now();
        
        if ($realizationDate->isToday() && $now->hour >= WorkTimeLimits::WORK_REALIZATION_DEADLINE_HOUR) {
            return back()->withErrors([
                'realization_date' => 'Realisasi kerja hari ini harus diisi sebelum jam ' . WorkTimeLimits::WORK_REALIZATION_DEADLINE_HOUR . ':00.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle file uploads
            if ($request->hasFile('output_files')) {
                $uploadedFiles = [];
                foreach ($request->file('output_files') as $file) {
                    if ($file->isValid()) {
                        $uploadedFiles[] = $file->store('work-realizations', 'public');
                    }
                }
                $validated['output_files'] = $uploadedFiles;
            }

            $validated['department'] = auth()->user()->department;
            $workRealization = $this->workService->createWorkRealization($validated, auth()->id());

            DB::commit();
            return redirect()->route('user.work-realizations.index')
                ->with('success', 'Realisasi kerja berhasil disimpan!');
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
            \App\Helpers\LogHelper::logControllerError('creating', 'WorkRealization', $e, null, $request->except(['_token', 'output_files']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'WorkRealization', $e, null, $request->except(['_token', 'output_files']));
            return back()->with('error', 'Terjadi kesalahan saat menyimpan realisasi kerja. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Display the specified work realization
     */
    public function show(WorkRealization $workRealization)
    {
        // Load basic relations first
        $workRealization->load(['user', 'workPlan', 'project']);
        
        // Load project managers if project exists (needed for policy check)
        if ($workRealization->project_id && $workRealization->project) {
            $workRealization->load('project.managers');
        }
        
        $this->authorize('view', $workRealization);
        
        // Return JSON for AJAX requests (preview modal)
        if (request()->wantsJson() || request()->ajax()) {
            // Ensure work_location is serialized as string value
            $workRealizationData = $workRealization->toArray();
            if (isset($workRealizationData['work_location']) && is_object($workRealization->work_location)) {
                $workRealizationData['work_location'] = $workRealization->work_location->value;
            }
            
            return response()->json([
                'workRealization' => $workRealizationData,
            ]);
        }
        
        return view('work.work-realizations.show', compact('workRealization'));
    }

    /**
     * Show the form for editing the specified work realization
     */
    public function edit(WorkRealization $workRealization)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workRealization->user_id !== auth()->id() && $workRealization->project_id) {
            $workRealization->load('project.managers');
        }
        
        $this->authorize('update', $workRealization);

        $workPlans = WorkPlan::where('user_id', auth()->id())
            ->whereDate('plan_date', '>=', now()->subDays(30))
            ->orderBy('plan_date', 'desc')
            ->get();

        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();

        return view('work.work-realizations.edit', compact('workRealization', 'workPlans', 'projects'));
    }

    /**
     * Update the specified work realization
     */
    public function update(Request $request, WorkRealization $workRealization)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workRealization->user_id !== auth()->id() && $workRealization->project_id) {
            $workRealization->load('project.managers');
        }
        
        $this->authorize('update', $workRealization);

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'realization_date' => 'required|date',
            'work_plan_id' => 'nullable|exists:work_plans,id',
            'work_location' => 'required|string|in:site,office,wfh,wfa',
            'description' => 'required|string',
            'achievements' => 'nullable|array',
            'output_description' => 'nullable|string',
            'output_files' => 'nullable|array',
            'output_files.*' => 'file|max:10240',
            'actual_duration_hours' => 'required|numeric|min:0.5|max:24',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Remove output_files from validated since we'll handle it separately
        unset($validated['output_files']);

        // Validasi waktu: Realisasi kerja harus diisi sebelum jam deadline
        $realizationDate = Carbon::parse($validated['realization_date']);
        $now = Carbon::now();
        
        if ($realizationDate->isToday() && $now->hour >= WorkTimeLimits::WORK_REALIZATION_DEADLINE_HOUR) {
            return back()->withErrors([
                'realization_date' => 'Realisasi kerja hari ini harus diisi sebelum jam ' . WorkTimeLimits::WORK_REALIZATION_DEADLINE_HOUR . ':00.'
            ])->withInput();
        }

        try {
            // Auto-generate title from description if not provided
            if (empty($validated['title'])) {
                $validated['title'] = mb_substr($validated['description'], 0, 50);
            }

            // Handle file uploads if new files are provided
            if ($request->hasFile('output_files')) {
                $uploadedFiles = $workRealization->output_files ?? [];
                foreach ($request->file('output_files') as $file) {
                    if ($file->isValid()) {
                        $uploadedFiles[] = $file->store('work-realizations', 'public');
                    }
                }
                $validated['output_files'] = $uploadedFiles;
            } else {
                // Keep existing files if no new files uploaded
                $validated['output_files'] = $workRealization->output_files ?? [];
            }

            $workRealization->update($validated);

            return redirect()->route('user.work-realizations.index')
                ->with('success', 'Realisasi kerja berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\WorkException $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'WorkRealization', $e, $workRealization->id, $request->except(['_token', 'output_files']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'WorkRealization', $e, $workRealization->id, $request->except(['_token', 'output_files']));
            return back()->with('error', 'Terjadi kesalahan saat memperbarui realisasi kerja. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified work realization
     */
    public function destroy(WorkRealization $workRealization)
    {
        // Load project managers only if needed for policy check (not owner)
        if ($workRealization->user_id !== auth()->id() && $workRealization->project_id) {
            $workRealization->load('project.managers');
        }
        
        $this->authorize('delete', $workRealization);

        try {
            $workRealization->delete();
            return redirect()->route('user.work-realizations.index')
                ->with('success', 'Realisasi kerja berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\WorkException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'WorkRealization', $e, $workRealization->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'WorkRealization', $e, $workRealization->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus realisasi kerja. Silakan coba lagi.');
        }
    }
}
