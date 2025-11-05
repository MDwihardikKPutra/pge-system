<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::where('user_id', auth()->id())
            ->with(['leaveType', 'approvedBy']);

        if ($request->has('status')) {
            $status = ApprovalStatus::from($request->status);
            $query->where('status', $status);
        }

        $leaves = $query->latest()->paginate(15);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave.index', compact('leaves', 'leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();
        return view('leave.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $leaveType = LeaveType::find($validated['leave_type_id']);
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $totalDays = $this->leaveService->calculateTotalDays($validated['start_date'], $validated['end_date']);

            // Check quota for annual leave
            if (!$this->leaveService->hasSufficientQuota(auth()->user(), $totalDays, $leaveType->name)) {
                return back()->with('error', 'Kuota cuti tidak mencukupi. Sisa kuota: ' . auth()->user()->remaining_leave . ' hari')->withInput();
            }

            $data = [
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'reason' => $validated['reason'],
            ];

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment');
            }

            $leave = $this->leaveService->createLeaveRequest($data, auth()->id());

            DB::commit();
            
            // Send notification to admins about new submission
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewSubmissionNotification(
                    $leave,
                    'leave',
                    auth()->user()
                ));
            }

            return redirect()->route('user.leaves.index')
                ->with('success', 'Pengajuan cuti berhasil diajukan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\LeaveRequestException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'LeaveRequest', $e, null, $request->except(['_token', 'attachment']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'LeaveRequest', $e, null, $request->except(['_token', 'attachment']));
            return back()->with('error', 'Terjadi kesalahan saat mengajukan cuti. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leave)
    {
        $this->authorize('view', $leave);
        
        $leave->load(['leaveType', 'user', 'approvedBy']);

        // Return JSON for AJAX requests (preview modal)
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'leave' => $leave,
            ]);
        }

        return view('leave.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leave)
    {
        $this->authorize('update', $leave);

        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leave)
    {
        $this->authorize('update', $leave);

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $totalDays = $this->leaveService->calculateTotalDays($validated['start_date'], $validated['end_date']);

            $leaveType = LeaveType::find($validated['leave_type_id']);

            // Check quota for annual leave
            if (!$this->leaveService->hasSufficientQuota(auth()->user(), $totalDays, $leaveType->name)) {
                return back()->with('error', 'Kuota cuti tidak mencukupi. Sisa kuota: ' . auth()->user()->remaining_leave . ' hari')->withInput();
            }

            $updateData = [
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'reason' => $validated['reason'],
            ];

            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($leave->attachment_path) {
                    \Storage::disk('public')->delete($leave->attachment_path);
                }
                $updateData['attachment_path'] = $request->file('attachment')->store('leave-requests', 'public');
            }

            $leave->update($updateData);

            return redirect()->route('user.leaves.index')
                ->with('success', 'Pengajuan cuti berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\LeaveRequestException $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'LeaveRequest', $e, $leave->id, $request->except(['_token', 'attachment']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('updating', 'LeaveRequest', $e, $leave->id, $request->except(['_token', 'attachment']));
            return back()->with('error', 'Terjadi kesalahan saat mengupdate cuti. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leave)
    {
        $this->authorize('delete', $leave);

        try {
            // Delete attachment if exists
            if ($leave->attachment_path) {
                \Storage::disk('public')->delete($leave->attachment_path);
            }

            $leave->delete();

            return redirect()->route('user.leaves.index')
                ->with('success', 'Pengajuan cuti berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\LeaveRequestException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'LeaveRequest', $e, $leave->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'LeaveRequest', $e, $leave->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus cuti. Silakan coba lagi.');
        }
    }
}
