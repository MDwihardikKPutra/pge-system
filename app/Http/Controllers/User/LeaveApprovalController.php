<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\LeaveApprovalException;

class LeaveApprovalController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
        // Check if user has access to leave approval module
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            // Admin always has access, or user with leave-approval module access
            if ($user->hasRole('admin') || $user->hasModuleAccess('leave-approval')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        });
    }

    /**
     * Show Leave Requests for approval
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $leaveList = LeaveRequest::with(['user', 'leaveType', 'approvedBy'])
            ->when($status !== 'all', function ($query) use ($status) {
                $statusEnum = ApprovalStatus::from($status);
                return $query->where('status', $statusEnum);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.leave-approvals.index', compact('leaveList', 'status'));
    }

    /**
     * Show specific Leave Request for approval
     */
    public function show(LeaveRequest $leave)
    {
        $leave->load(['user', 'leaveType', 'approvedBy']);
        
        return view('user.leave-approvals.show', compact('leave'));
    }

    /**
     * Approve Leave Request
     */
    public function approve(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $this->leaveService->updateLeaveRequestStatus(
                $leave,
                ApprovalStatus::APPROVED,
                $request->admin_notes
            );

            DB::commit();

            return redirect()->route('user.leave-approvals.index')
                ->with('success', 'Disetujui oleh ' . auth()->user()->name);
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Permintaan cuti tidak ditemukan.');
        } catch (LeaveApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'LeaveRequest', $e, $leave->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'LeaveRequest', $e, $leave->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menyetujui cuti. Silakan coba lagi.');
        }
    }

    /**
     * Reject Leave Request
     */
    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $this->leaveService->updateLeaveRequestStatus(
                $leave,
                ApprovalStatus::REJECTED,
                null,
                $request->rejection_reason
            );

            DB::commit();

            return redirect()->route('user.leave-approvals.index')
                ->with('success', 'Ditolak oleh ' . auth()->user()->name);
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Permintaan cuti tidak ditemukan.');
        } catch (LeaveApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'LeaveRequest', $e, $leave->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'LeaveRequest', $e, $leave->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menolak cuti. Silakan coba lagi.');
        }
    }
}
