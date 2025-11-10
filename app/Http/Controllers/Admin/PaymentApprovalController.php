<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\ActivityLog;
use App\Services\PaymentService;
use App\Enums\ApprovalStatus;
use App\Http\Requests\ApprovePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\PaymentApprovalException;

class PaymentApprovalController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display all payment submissions for approval
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, spd, purchase, vendor-payment
        $status = $request->get('status', 'pending'); // pending, approved, rejected, all

        $spds = collect();
        $purchases = collect();
        $vendorPayments = collect();

        // Get SPDs
        if ($type === 'all' || $type === 'spd') {
            $spdQuery = SPD::with(['user', 'project', 'approvedBy']);
            if ($status !== 'all') {
                $statusEnum = ApprovalStatus::from($status);
                $spdQuery->where('status', $statusEnum);
            }
            $spds = $spdQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'spd_page');
        }

        // Get Purchases
        if ($type === 'all' || $type === 'purchase') {
            $purchaseQuery = Purchase::with(['user', 'project', 'approvedBy']);
            if ($status !== 'all') {
                $statusEnum = ApprovalStatus::from($status);
                $purchaseQuery->where('status', $statusEnum);
            }
            $purchases = $purchaseQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'purchase_page');
        }

        // Get Vendor Payments
        if ($type === 'all' || $type === 'vendor-payment') {
            $vpQuery = VendorPayment::with(['user', 'vendor', 'project', 'approvedBy']);
            if ($status !== 'all') {
                $statusEnum = ApprovalStatus::from($status);
                $vpQuery->where('status', $statusEnum);
            }
            $vendorPayments = $vpQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'vp_page');
        }

        return view('admin.approvals.payments.index', compact('spds', 'purchases', 'vendorPayments', 'type', 'status'));
    }

    /**
     * Show SPD detail for approval
     */
    public function showSpd(SPD $spd)
    {
        // Check access - admin always has access, or user with payment-approval module
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('payment-approval')) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }
        
        $spd->load(['user', 'project', 'approvedBy']);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $spd->id,
                'spd_number' => $spd->spd_number,
                'user' => $spd->user->name,
                'project' => $spd->project->name ?? '-',
                'destination' => $spd->destination,
                'purpose' => $spd->purpose,
                'departure_date' => $spd->departure_date->format('d M Y'),
                'return_date' => $spd->return_date->format('d M Y'),
                'transport_cost' => number_format($spd->transport_cost, 0, ',', '.'),
                'accommodation_cost' => number_format($spd->accommodation_cost, 0, ',', '.'),
                'meal_cost' => number_format($spd->meal_cost, 0, ',', '.'),
                'other_cost' => number_format($spd->other_cost, 0, ',', '.'),
                'other_cost_description' => $spd->other_cost_description,
                'total_cost' => number_format($spd->total_cost, 0, ',', '.'),
                'notes' => $spd->notes,
                'rejection_reason' => $spd->rejection_reason,
                'status' => $spd->status->value,
                'approved_by' => $spd->approvedBy->name ?? '-',
                'approved_at' => $spd->approved_at ? $spd->approved_at->format('d M Y H:i') : '-',
                'created_at' => $spd->created_at->format('d M Y H:i'),
            ]
        ]);
    }

    /**
     * Approve SPD
     */
    public function approveSpd(ApprovePaymentRequest $request, SPD $spd)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $spd,
                'spd',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'approved',
                'model_type' => SPD::class,
                'model_id' => $spd->id,
                'description' => 'Menyetujui SPD ' . $spd->spd_number,
                'properties' => ['status' => 'approved', 'notes' => $validated['notes'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'SPD berhasil disetujui!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'SPD tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'SPD', $e, $spd->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'SPD', $e, $spd->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menyetujui SPD. Silakan coba lagi.');
        }
    }

    /**
     * Reject SPD
     */
    public function rejectSpd(ApprovePaymentRequest $request, SPD $spd)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $spd,
                'spd',
                ApprovalStatus::REJECTED,
                null,
                $validated['rejection_reason'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'model_type' => SPD::class,
                'model_id' => $spd->id,
                'description' => 'Menolak SPD ' . $spd->spd_number,
                'properties' => ['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'SPD berhasil ditolak!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'SPD tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'SPD', $e, $spd->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'SPD', $e, $spd->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menolak SPD. Silakan coba lagi.');
        }
    }

    /**
     * Show Purchase detail for approval
     */
    public function showPurchase(Purchase $purchase)
    {
        // Check access - admin always has access, or user with payment-approval module
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('payment-approval')) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }
        
        $purchase->load(['user', 'project', 'approvedBy']);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $purchase->id,
                'purchase_number' => $purchase->purchase_number,
                'user' => $purchase->user->name,
                'project' => $purchase->project->name ?? '-',
                'type' => $purchase->type,
                'category' => $purchase->category,
                'item_name' => $purchase->item_name,
                'quantity' => $purchase->quantity,
                'unit' => $purchase->unit,
                'unit_price' => number_format($purchase->unit_price, 0, ',', '.'),
                'total_price' => number_format($purchase->total_price, 0, ',', '.'),
                'description' => $purchase->description,
                'notes' => $purchase->notes,
                'rejection_reason' => $purchase->rejection_reason,
                'status' => $purchase->status->value,
                'approved_by' => $purchase->approvedBy->name ?? '-',
                'approved_at' => $purchase->approved_at ? $purchase->approved_at->format('d M Y H:i') : '-',
                'created_at' => $purchase->created_at->format('d M Y H:i'),
            ]
        ]);
    }

    /**
     * Approve Purchase
     */
    public function approvePurchase(ApprovePaymentRequest $request, Purchase $purchase)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $purchase,
                'purchase',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'approved',
                'model_type' => Purchase::class,
                'model_id' => $purchase->id,
                'description' => 'Menyetujui pembelian ' . $purchase->purchase_number,
                'properties' => ['status' => 'approved', 'notes' => $validated['notes'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'Pembelian berhasil disetujui!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Pembelian tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'Purchase', $e, $purchase->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'Purchase', $e, $purchase->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menyetujui pembelian. Silakan coba lagi.');
        }
    }

    /**
     * Reject Purchase
     */
    public function rejectPurchase(ApprovePaymentRequest $request, Purchase $purchase)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $purchase,
                'purchase',
                ApprovalStatus::REJECTED,
                null,
                $validated['rejection_reason'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'model_type' => Purchase::class,
                'model_id' => $purchase->id,
                'description' => 'Menolak pembelian ' . $purchase->purchase_number,
                'properties' => ['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'Pembelian berhasil ditolak!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Pembelian tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'Purchase', $e, $purchase->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'Purchase', $e, $purchase->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menolak pembelian. Silakan coba lagi.');
        }
    }

    /**
     * Show Vendor Payment detail for approval
     */
    public function showVendorPayment(VendorPayment $vendorPayment)
    {
        // Check access - admin always has access, or user with payment-approval module
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('payment-approval')) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }
        
        $vendorPayment->load(['user', 'vendor', 'project', 'approvedBy']);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $vendorPayment->id,
                'payment_number' => $vendorPayment->payment_number,
                'user' => $vendorPayment->user->name,
                'vendor' => $vendorPayment->vendor->name,
                'project' => $vendorPayment->project->name ?? '-',
                'invoice_number' => $vendorPayment->invoice_number,
                'payment_date' => $vendorPayment->payment_date->format('d M Y'),
                'amount' => number_format($vendorPayment->amount, 0, ',', '.'),
                'description' => $vendorPayment->description,
                'notes' => $vendorPayment->notes,
                'rejection_reason' => $vendorPayment->rejection_reason,
                'status' => $vendorPayment->status->value,
                'approved_by' => $vendorPayment->approvedBy->name ?? '-',
                'approved_at' => $vendorPayment->approved_at ? $vendorPayment->approved_at->format('d M Y H:i') : '-',
                'created_at' => $vendorPayment->created_at->format('d M Y H:i'),
            ]
        ]);
    }

    /**
     * Approve Vendor Payment
     */
    public function approveVendorPayment(ApprovePaymentRequest $request, VendorPayment $vendorPayment)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $vendorPayment,
                'vendor-payment',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'approved',
                'model_type' => VendorPayment::class,
                'model_id' => $vendorPayment->id,
                'description' => 'Menyetujui pembayaran vendor ' . $vendorPayment->payment_number,
                'properties' => ['status' => 'approved', 'notes' => $validated['notes'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'Pembayaran vendor berhasil disetujui!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Pembayaran vendor tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('approving', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menyetujui pembayaran vendor. Silakan coba lagi.');
        }
    }

    /**
     * Reject Vendor Payment
     */
    public function rejectVendorPayment(ApprovePaymentRequest $request, VendorPayment $vendorPayment)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $vendorPayment,
                'vendor-payment',
                ApprovalStatus::REJECTED,
                null,
                $validated['rejection_reason'] ?? null
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'model_type' => VendorPayment::class,
                'model_id' => $vendorPayment->id,
                'description' => 'Menolak pembayaran vendor ' . $vendorPayment->payment_number,
                'properties' => ['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason'] ?? null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.approvals.payments.index')
                ->with('success', 'Pembayaran vendor berhasil ditolak!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Pembayaran vendor tidak ditemukan.');
        } catch (PaymentApprovalException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('rejecting', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat menolak pembayaran vendor. Silakan coba lagi.');
        }
    }
}

