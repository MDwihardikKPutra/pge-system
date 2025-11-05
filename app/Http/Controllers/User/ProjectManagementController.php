<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\ChecksAuthorization;
use Illuminate\Http\Request;

class ProjectManagementController extends Controller
{
    use ChecksAuthorization;
    
    /**
     * Display all projects with statistics
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin();

        $query = Project::with(['managers' => function($q) {
            $q->withPivot('access_type');
        }])->orderBy('name');

        // Non-admin users can only see projects where they are Project Manager (any access type)
        if (!$isAdmin) {
            $query->whereHas('managers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $projects = $query->paginate(20);

        return view('user.project-management.index', compact('projects', 'isAdmin'));
    }

    /**
     * Show project details with all related data
     */
    public function show(Request $request, $id)
    {
        $project = Project::with('managers')->findOrFail($id);

        $user = auth()->user();
        $isAdmin = $this->isAdmin();

        // Get user's access type for this project
        $accessType = $isAdmin ? 'full' : $project->getManagerAccessType($user->id);
        
        // Check if user has access to this project
        if (!$isAdmin && !$accessType) {
            abort(403, 'Anda tidak memiliki akses ke project ini');
        }

        // Get pagination parameters from request
        $workPlansPage = $request->get('work_plans_page', 1);
        $workRealizationsPage = $request->get('work_realizations_page', 1);
        $spdPage = $request->get('spd_page', 1);
        $purchasesPage = $request->get('purchases_page', 1);
        $vendorPaymentsPage = $request->get('vendor_payments_page', 1);

        // Load data based on access type with pagination
        $data = $this->loadProjectData($project, $user, $isAdmin, $accessType, [
            'work_plans_page' => $workPlansPage,
            'work_realizations_page' => $workRealizationsPage,
            'spd_page' => $spdPage,
            'purchases_page' => $purchasesPage,
            'vendor_payments_page' => $vendorPaymentsPage,
        ]);

        // Get all users for Project Manager assignment (only for admin) - cached
        $allUsers = collect();
        if ($isAdmin) {
            $allUsers = \App\Helpers\CacheHelper::getProjectManagementUsers();
        }

        return view('user.project-management.show', array_merge($data, [
            'project' => $project,
            'isAdmin' => $isAdmin,
            'allUsers' => $allUsers,
            'accessType' => $accessType,
        ]));
    }

    /**
     * Load project data based on access type with pagination
     * 
     * @param Project $project
     * @param \App\Models\User $user
     * @param bool $isAdmin
     * @param string|null $accessType
     * @param array $paginationParams
     * @return array
     */
    protected function loadProjectData(Project $project, $user, bool $isAdmin, ?string $accessType, array $paginationParams): array
    {
        $result = [
            'workPlans' => collect(),
            'workRealizations' => collect(),
            'spd' => collect(),
            'purchases' => collect(),
            'vendorPayments' => collect(),
            'workPlansPaginated' => null,
            'workRealizationsPaginated' => null,
            'spdPaginated' => null,
            'purchasesPaginated' => null,
            'vendorPaymentsPaginated' => null,
        ];

        // Determine which data to load based on access type
        $canAccessWork = $isAdmin || $accessType === 'pm' || $accessType === 'full';
        $canAccessPayments = $isAdmin || $accessType === 'finance' || $accessType === 'full';
        $onlyOwnData = !$isAdmin && !in_array($accessType, ['pm', 'finance', 'full']);

        // Load work plans
        if ($canAccessWork) {
            $query = $project->workPlans()->with('user');
            if ($onlyOwnData) {
                $query->where('user_id', $user->id);
            }
            $result['workPlansPaginated'] = $query
                ->orderBy('plan_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'work_plans_page', $paginationParams['work_plans_page']);
            $result['workPlans'] = $result['workPlansPaginated']->getCollection();
        }

        // Load work realizations
        if ($canAccessWork) {
            $query = $project->workRealizations()->with('user');
            if ($onlyOwnData) {
                $query->where('user_id', $user->id);
            }
            $result['workRealizationsPaginated'] = $query
                ->orderBy('realization_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'work_realizations_page', $paginationParams['work_realizations_page']);
            $result['workRealizations'] = $result['workRealizationsPaginated']->getCollection();
        }

        // Load SPD
        if ($canAccessPayments) {
            $query = $project->spd()->with('user');
            if ($onlyOwnData) {
                $query->where('user_id', $user->id);
            }
            $result['spdPaginated'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'spd_page', $paginationParams['spd_page']);
            $result['spd'] = $result['spdPaginated']->getCollection();
        }

        // Load purchases
        if ($canAccessPayments) {
            $query = $project->purchases()->with('user');
            if ($onlyOwnData) {
                $query->where('user_id', $user->id);
            }
            $result['purchasesPaginated'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'purchases_page', $paginationParams['purchases_page']);
            $result['purchases'] = $result['purchasesPaginated']->getCollection();
        }

        // Load vendor payments
        if ($canAccessPayments) {
            $query = $project->vendorPayments()->with(['user', 'vendor']);
            if ($onlyOwnData) {
                $query->where('user_id', $user->id);
            }
            $result['vendorPaymentsPaginated'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'vendor_payments_page', $paginationParams['vendor_payments_page']);
            $result['vendorPayments'] = $result['vendorPaymentsPaginated']->getCollection();
        }

        return $result;
    }

    /**
     * Show the form for creating a new project (Admin only)
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa membuat project');
        }

        return view('user.project-management.create');
    }

    /**
     * Store a newly created project (Admin only)
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa membuat project');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:projects,code',
            'client' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') || $request->input('is_active') === true || $request->input('is_active') === '1';

        Project::create($validated);

        $routePrefix = $request->is('admin/*') ? 'admin' : 'user';
        return redirect()->route($routePrefix . '.project-management.index')
            ->with('success', 'Project berhasil ditambahkan');
    }
}
