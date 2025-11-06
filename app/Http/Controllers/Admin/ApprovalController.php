<?php

namespace App\Http\Controllers\Admin;

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

class ApprovalController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Show pending Leave Requests
     */
    public function leaves(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $leaveList = LeaveRequest::with(['user', 'leaveType', 'approvedBy'])
            ->when($status !== 'all', function ($query) use ($status) {
                $statusEnum = ApprovalStatus::from($status);
                return $query->where('status', $statusEnum);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.approvals.leaves.index', compact('leaveList', 'status'));
    }

    /**
     * Show specific Leave Request for approval
     */
    public function showLeaveRequest(LeaveRequest $leave)
    {
        $leave->load(['user', 'leaveType', 'approvedBy']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $leave->id,
                'leave_number' => $leave->leave_number,
                'user' => $leave->user->name,
                'employee_id' => $leave->user->employee_id ?? '-',
                'leave_type' => $leave->leaveType->name,
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'total_days' => $leave->total_days,
                'reason' => $leave->reason,
                'attachment_path' => $leave->attachment_path,
                'attachment_name' => $leave->attachment_path ? basename($leave->attachment_path) : null,
                'attachment_url' => $leave->attachment_path ? (request()->routeIs('admin.*') 
                    ? route('admin.approvals.leaves.attachment.download', $leave->id)
                    : route('user.leave-approvals.attachment.download', $leave->id)) : null,
                'admin_notes' => $leave->admin_notes,
                'rejection_reason' => $leave->rejection_reason,
                'status' => $leave->status->value,
                'approved_by' => $leave->approvedBy->name ?? '-',
                'approved_at' => $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : '-',
                'created_at' => $leave->created_at->format('d M Y H:i'),
                'pdf_path' => $leave->pdf_path ?? null,
            ]
        ]);
    }

    /**
     * Approve Leave Request
     */
    public function approveLeaveRequest(Request $request, LeaveRequest $leave)
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

            return redirect()->route('admin.approvals.leaves')
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
    public function rejectLeaveRequest(Request $request, LeaveRequest $leave)
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

            return redirect()->route('admin.approvals.leaves')
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

    /**
     * Download attachment file for leave request
     */
    public function downloadAttachment(LeaveRequest $leave)
    {
        // Check access - admin always has access, or user with leave-approval module
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('leave-approval')) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }
        
        if (!$leave->attachment_path) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $leave->attachment_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, basename($leave->attachment_path));
    }
}
