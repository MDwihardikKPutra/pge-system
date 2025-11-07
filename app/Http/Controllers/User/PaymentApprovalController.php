<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
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
     * Check if user has access to payment approval module
     */
    protected function checkAccess()
    {
        $user = auth()->user();
        // Admin always has access, or user with payment-approval module access
        if (!$user->hasRole('admin') && !$user->hasModuleAccess('payment-approval')) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }
    }

    /**
     * Display all payment submissions for approval
     */
    public function index(Request $request)
    {
        $this->checkAccess();
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

        // Use the same view as admin - shared view
        return view('admin.approvals.payments.index', compact('spds', 'purchases', 'vendorPayments', 'type', 'status'));
    }

    /**
     * Show SPD detail for approval (JSON for modal)
     */
    public function showSpd(SPD $spd)
    {
        $this->checkAccess();
        
        // Use admin controller method to avoid duplication
        $adminController = app(\App\Http\Controllers\Admin\PaymentApprovalController::class);
        return $adminController->showSpd($spd);
    }

    /**
     * Show Purchase detail for approval (JSON for modal)
     */
    public function showPurchase(Purchase $purchase)
    {
        $this->checkAccess();
        
        // Use admin controller method to avoid duplication
        $adminController = app(\App\Http\Controllers\Admin\PaymentApprovalController::class);
        return $adminController->showPurchase($purchase);
    }

    /**
     * Show Vendor Payment detail for approval (JSON for modal)
     */
    public function showVendorPayment(VendorPayment $vendorPayment)
    {
        $this->checkAccess();
        
        // Use admin controller method to avoid duplication
        $adminController = app(\App\Http\Controllers\Admin\PaymentApprovalController::class);
        return $adminController->showVendorPayment($vendorPayment);
    }

    /**
     * Approve SPD
     */
    public function approveSpd(ApprovePaymentRequest $request, SPD $spd)
    {
        $this->checkAccess();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $spd,
                'spd',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
        $this->checkAccess();
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

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
     * Approve Purchase
     */
    public function approvePurchase(ApprovePaymentRequest $request, Purchase $purchase)
    {
        $this->checkAccess();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $purchase,
                'purchase',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
        $this->checkAccess();
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

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
     * Approve Vendor Payment
     */
    public function approveVendorPayment(ApprovePaymentRequest $request, VendorPayment $vendorPayment)
    {
        $this->checkAccess();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $this->paymentService->updatePaymentStatus(
                $vendorPayment,
                'vendor-payment',
                ApprovalStatus::APPROVED,
                $validated['notes'] ?? null
            );

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
        $this->checkAccess();
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

            DB::commit();

            return redirect()->route('user.payment-approvals.index')
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
