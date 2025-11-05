<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\VendorPayment;
use App\Models\Vendor;
use App\Models\Project;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StoreVendorPaymentRequest;
use App\Http\Requests\UpdateVendorPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorPaymentController extends Controller
{
    use ChecksAuthorization;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of Vendor Payments
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Admin can see all Vendor Payments, user only sees their own
        $query = VendorPayment::with(['vendor', 'project', 'approvedBy', 'user']);
        if (!$this->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        
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
    public function show(VendorPayment $vendorPayment)
    {
        // Eager load relationships to avoid N+1 queries
        $vendorPayment->load(['vendor', 'project', 'approvedBy']);
        
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        // Check if user is owner
        $isOwner = $vendorPayment->user_id === $user->id;
        
        // Check if user has Finance/Full access to the project
        $hasProjectAccess = false;
        if ($vendorPayment->project_id && $vendorPayment->project) {
            $accessType = $vendorPayment->project->getManagerAccessType($user->id);
            $hasProjectAccess = in_array($accessType, ['finance', 'full']);
        }
        
        if (!$isAdmin && !$isOwner && !$hasProjectAccess) {
            abort(403, 'Anda tidak memiliki akses ke pembayaran vendor ini');
        }

        $vpData = $vendorPayment->toArray();
        
        // Ensure amount is positive
        $vpData['amount'] = abs(floatval($vpData['amount'] ?? 0));

        return response()->json([
            'vendorPayment' => $vpData,
        ]);
    }

    /**
     * Store a newly created Vendor Payment
     */
    public function store(StoreVendorPaymentRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $vendorPayment = VendorPayment::create([
                'payment_number' => $this->paymentService->generateVendorPaymentNumber(),
                'user_id' => auth()->id(),
                'vendor_id' => $validated['vendor_id'],
                'project_id' => $validated['project_id'] ?? null,
                'payment_type' => $validated['payment_type'],
                'payment_date' => $validated['payment_date'],
                'invoice_number' => $validated['invoice_number'],
                'po_number' => $validated['po_number'] ?? null,
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'notes' => $validated['notes'] ?? null,
                'status' => ApprovalStatus::PENDING,
            ]);

            DB::commit();
            
            // Send notification to admins about new submission
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewSubmissionNotification(
                    $vendorPayment,
                    'vendor-payment',
                    auth()->user()
                ));
            }
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.vendor-payments.index')->with('success', 'Pembayaran vendor berhasil diajukan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\PaymentException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'VendorPayment', $e, null, $request->except(['_token']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'VendorPayment', $e, null, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat mengajukan pembayaran vendor. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Update the specified Vendor Payment
     */
    public function update(UpdateVendorPaymentRequest $request, VendorPayment $vendorPayment)
    {
        $this->authorize('update', $vendorPayment);

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $vendorPayment->update([
                'vendor_id' => $validated['vendor_id'],
                'project_id' => $validated['project_id'],
                'payment_type' => $validated['payment_type'],
                'payment_date' => $validated['payment_date'],
                'invoice_number' => $validated['invoice_number'],
                'po_number' => $validated['po_number'] ?? null,
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.vendor-payments.index')->with('success', 'Pembayaran vendor berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\PaymentException $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('updating', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('updating', 'VendorPayment', $e, $vendorPayment->id, $request->except(['_token']));
            return back()->with('error', 'Terjadi kesalahan saat mengupdate pembayaran vendor. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified Vendor Payment
     */
    public function destroy(VendorPayment $vendorPayment)
    {
        $this->authorize('delete', $vendorPayment);

        try {
            $vendorPayment->delete();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.vendor-payments.index')->with('success', 'Pembayaran vendor berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\PaymentException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'VendorPayment', $e, $vendorPayment->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'VendorPayment', $e, $vendorPayment->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus pembayaran vendor. Silakan coba lagi.');
        }
    }
}
