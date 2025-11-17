<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Models\VendorPayment;
use App\Models\Vendor;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StoreVendorPaymentRequest;
use App\Http\Requests\UpdateVendorPaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorPaymentController extends BaseController
{
    use AuthorizesRequests, ChecksAuthorization;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of Vendor Payments
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query = VendorPayment::with(['vendor', 'project', 'approvedBy', 'user'])
            ->where('user_id', auth()->id());
        
        $vendorPayments = $query->when($status !== 'all', function ($q) use ($status) {
                $statusEnum = ApprovalStatus::from($status);
                return $q->where('status', $statusEnum);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
        
        return view('payment.vendor-payment.index', compact('vendorPayments', 'status', 'vendors', 'projects'));
    }

    /**
     * Display the specified Vendor Payment (for AJAX)
     */
    public function show(VendorPayment $vendorPayment): JsonResponse
    {
        return $this->handleOperation(function () use ($vendorPayment) {
            // Load project managers if project exists (needed for policy check)
            if ($vendorPayment->project_id && $vendorPayment->project) {
                $vendorPayment->load('project.managers');
            }
            
            $this->authorize('view', $vendorPayment);
            
            // Eager load relationships to avoid N+1 queries
            $vendorPayment->load(['vendor', 'project', 'approvedBy']);

            $vpData = $vendorPayment->toArray();
            
            // Ensure amount is positive
            $vpData['amount'] = abs(floatval($vpData['amount'] ?? 0));

            return response()->json([
                'vendorPayment' => $vpData,
            ]);
        }, 'showing', 'VendorPayment');
    }

    /**
     * Store a newly created Vendor Payment
     */
    public function store(StoreVendorPaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated) {
                $vendorPayment = $this->paymentService->createVendorPayment($validated, auth()->id());
                
                // Send notification to admins about new submission
                $this->notifyAdmins($vendorPayment, 'vendor-payment');
                
                return null; // Let handleTransaction handle redirect
            },
            'creating',
            'VendorPayment',
            $request,
            null,
            'Pembayaran vendor berhasil diajukan!',
            'Terjadi kesalahan saat mengajukan pembayaran vendor. Silakan coba lagi.',
            'vendor-payments.index'
        );
    }

    /**
     * Update the specified Vendor Payment
     */
    public function update(UpdateVendorPaymentRequest $request, VendorPayment $vendorPayment): RedirectResponse
    {
        $this->authorize('update', $vendorPayment);

        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated, $vendorPayment) {
                return $this->paymentService->updateVendorPayment($vendorPayment, $validated);
            },
            'updating',
            'VendorPayment',
            $request,
            $vendorPayment->id,
            'Pembayaran vendor berhasil diupdate!',
            'Terjadi kesalahan saat mengupdate pembayaran vendor. Silakan coba lagi.',
            'vendor-payments.index'
        );
    }

    /**
     * Remove the specified Vendor Payment
     */
    public function destroy(VendorPayment $vendorPayment): RedirectResponse
    {
        $this->authorize('delete', $vendorPayment);

        return $this->handleOperation(
            function () use ($vendorPayment) {
                $vendorPayment->delete();
                
                $routePrefix = $this->getRoutePrefix();
                $route = $routePrefix ? "{$routePrefix}.vendor-payments.index" : 'vendor-payments.index';
                return redirect()->route($route)->with('success', 'Pembayaran vendor berhasil dihapus!');
            },
            'deleting',
            'VendorPayment',
            null,
            $vendorPayment->id
        );
    }

    /**
     * Download PDF for approved Vendor Payment
     */
    public function downloadPDF(VendorPayment $vendorPayment)
    {
        // Admin can always access, bypass authorization check
        if (!auth()->user()->hasRole('admin')) {
            $this->authorize('view', $vendorPayment);
        }
        
        // Only allow download for approved Vendor Payment
        if ($vendorPayment->status->value !== 'approved') {
            abort(403, 'PDF hanya tersedia untuk Pembayaran Vendor yang sudah disetujui.');
        }

        $vendorPayment->load(['user', 'vendor', 'project', 'approvedBy']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.vendor-payment', compact('vendorPayment'))
            ->setPaper('a4', 'portrait');

        $filename = 'Pembayaran_Vendor_' . $vendorPayment->payment_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
