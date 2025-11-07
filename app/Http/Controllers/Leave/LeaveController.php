<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use App\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class LeaveController extends Controller
{
    use AuthorizesRequests;
    
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
        $isAdmin = auth()->user()->hasRole('admin');
        
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query = LeaveRequest::with(['leaveType', 'approvedBy', 'user'])
            ->where('user_id', auth()->id());

        if ($request->has('status')) {
            $status = ApprovalStatus::from($request->status);
            $query->where('status', $status);
        }

        $leaves = $query->latest()->paginate(15);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave.index', compact('leaves', 'leaveTypes', 'isAdmin'));
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
        $this->authorize('create', LeaveRequest::class);
        
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
            
            // Send notification to admins and users with leave-approval module about new submission
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewSubmissionNotification(
                    $leave,
                    'leave',
                    auth()->user()
                ));
            }
            
            // Send notification to users with leave-approval module access
            $approvers = \App\Models\User::whereHas('modules', function($q) {
                $q->where('modules.key', 'leave-approval');
            })->get();
            
            foreach ($approvers as $approver) {
                // Skip if already notified as admin
                if (!$approver->hasRole('admin')) {
                    $approver->notify(new \App\Notifications\NewSubmissionNotification(
                        $leave,
                        'leave',
                        auth()->user()
                    ));
                }
            }

            $routeName = auth()->user()->hasRole('admin') ? 'admin.leaves.index' : 'user.leaves.index';
            return redirect()->route($routeName)
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
    public function show($id)
    {
        // Find the leave request manually to handle both route parameter names
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        
        $this->authorize('view', $leave);
        
        $leave->load(['leaveType', 'user', 'approvedBy']);

        // Return JSON for AJAX requests (preview modal)
        if (request()->wantsJson() || request()->ajax()) {
            $isAdmin = auth()->user()->hasRole('admin');
            $routePrefix = $isAdmin ? 'admin.leaves' : 'user.leaves';
            
            return response()->json([
                'success' => true,
                'leave' => [
                    'id' => $leave->id,
                    'leave_number' => $leave->leave_number,
                    'leave_type_id' => $leave->leave_type_id,
                    'leave_type' => $leave->leaveType ? [
                        'id' => $leave->leaveType->id,
                        'name' => $leave->leaveType->name,
                    ] : null,
                    'user' => $leave->user ? [
                        'id' => $leave->user->id,
                        'name' => $leave->user->name,
                        'email' => $leave->user->email,
                        'employee_id' => $leave->user->employee_id,
                    ] : null,
                    'start_date' => $leave->start_date ? $leave->start_date->format('Y-m-d') : null,
                    'end_date' => $leave->end_date ? $leave->end_date->format('Y-m-d') : null,
                    'total_days' => $leave->total_days,
                    'reason' => $leave->reason,
                    'status' => $leave->status->value ?? $leave->status,
                    'attachment_path' => $leave->attachment_path,
                    'attachment_name' => $leave->attachment_path ? basename($leave->attachment_path) : null,
                    'attachment_url' => $leave->attachment_path ? route($routePrefix . '.attachment.download', $leave->id) : null,
                    'created_at' => $leave->created_at ? $leave->created_at->format('Y-m-d H:i:s') : null,
                    'approved_by' => $leave->approvedBy ? [
                        'id' => $leave->approvedBy->id,
                        'name' => $leave->approvedBy->name,
                    ] : null,
                    'approved_at' => $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i:s') : null,
                    'admin_notes' => $leave->admin_notes,
                    'rejection_reason' => $leave->rejection_reason,
                ],
            ]);
        }

        return view('leave.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        
        // Check if leave request is still pending (can't edit if already approved/rejected)
        if (!$leave->isPending()) {
            $routeName = auth()->user()->hasRole('admin') ? 'admin.leaves.index' : 'user.leaves.index';
            return redirect()->route($routeName)
                ->with('error', 'Pengajuan cuti yang sudah disetujui atau ditolak tidak dapat diubah.');
        }
        
        $this->authorize('update', $leave);

        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        
        // Check if leave request is still pending (can't edit if already approved/rejected)
        if (!$leave->isPending()) {
            $routeName = auth()->user()->hasRole('admin') ? 'admin.leaves.index' : 'user.leaves.index';
            return redirect()->route($routeName)
                ->with('error', 'Pengajuan cuti yang sudah disetujui atau ditolak tidak dapat diubah.');
        }
        
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

            $routeName = auth()->user()->hasRole('admin') ? 'admin.leaves.index' : 'user.leaves.index';
            return redirect()->route($routeName)
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
    public function destroy($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $this->authorize('delete', $leave);

        try {
            // Delete attachment if exists
            if ($leave->attachment_path) {
                \Storage::disk('public')->delete($leave->attachment_path);
            }

            $leave->delete();

            $routeName = auth()->user()->hasRole('admin') ? 'admin.leaves.index' : 'user.leaves.index';
            return redirect()->route($routeName)
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

    /**
     * Download attachment file
     */
    public function downloadAttachment($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $this->authorize('view', $leave);
        
        if (!$leave->attachment_path) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $leave->attachment_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, basename($leave->attachment_path));
    }

    /**
     * Download PDF for approved leave request
     */
    public function downloadPDF($id)
    {
        $leave = \App\Models\LeaveRequest::with(['user', 'leaveType', 'approvedBy'])->findOrFail($id);
        $this->authorize('view', $leave);

        // Only allow download for approved leave requests
        if ($leave->status->value !== 'approved') {
            abort(403, 'PDF hanya tersedia untuk pengajuan cuti yang sudah disetujui.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave', compact('leave'))
            ->setPaper('a4', 'portrait');

        $filename = 'Surat_Cuti_' . $leave->leave_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
