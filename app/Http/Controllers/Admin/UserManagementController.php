<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Module;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();
        $stats = $this->userService->getUserStats();
        $assignableModules = Module::assignableToUser()
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => $stats['total'],
            'activeUsers' => $stats['active'],
            'assignableModules' => $assignableModules,
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $assignableModules = Module::assignableToUser()
            ->orderBy('sort_order')
            ->get();
        
        if (request()->ajax()) {
            return response()->json([
                'employee_id' => $this->userService->generateEmployeeId(),
                'assignableModules' => $assignableModules,
            ]);
        }
            
        return view('admin.users.create', compact('assignableModules'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->userService->validateUserData($request->all()));
            
            $validated['is_active'] = $request->has('is_active') || $request->input('is_active') === true || $request->input('is_active') === '1';
            
            $user = $this->userService->createUser($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil ditambahkan',
                    'user' => $user->load('modules'),
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('modules');
        
        if (request()->ajax()) {
            return response()->json([
                'user' => $user,
            ]);
        }
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $assignableModules = Module::assignableToUser()
            ->orderBy('sort_order')
            ->get();
        
        if (request()->ajax()) {
            $user->load('modules');
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'employee_id' => $user->employee_id,
                    'role' => $user->getRoleNames()->first(),
                    'roles' => $user->roles,
                    'department' => $user->department,
                    'position' => $user->position,
                    'phone' => $user->phone,
                    'join_date' => $user->join_date?->format('Y-m-d'),
                    // 'annual_leave_quota' => $user->annual_leave_quota, // REMOVED - Kuota cuti feature removed
                    'address' => $user->address,
                    'is_active' => $user->is_active,
                    'modules' => $user->modules,
                ],
                'assignableModules' => $assignableModules,
            ]);
        }
            
        return view('admin.users.edit', compact('user', 'assignableModules'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate($this->userService->validateUserData($request->all(), $user->id));
            
            $validated['is_active'] = $request->has('is_active') || $request->input('is_active') === true || $request->input('is_active') === '1';
            
            $updatedUser = $this->userService->updateUser($user, $validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil diupdate',
                    'user' => $updatedUser->load('modules'),
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user, auth()->id());

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil dihapus',
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->route('admin.users.index')
                ->with('error', $e->getMessage());
        }
    }
}
