<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Models\Purchase;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchaseController extends BaseController
{
    use AuthorizesRequests, ChecksAuthorization;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of Purchases
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query = Purchase::with(['project', 'approvedBy', 'user'])
            ->where('user_id', auth()->id());
        
        $purchases = $query->when($status !== 'all', function ($q) use ($status) {
                $statusEnum = ApprovalStatus::from($status);
                return $q->where('status', $statusEnum);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
        
        return view('payment.purchase.index', compact('purchases', 'status', 'projects'));
    }

    /**
     * Display the specified Purchase (for AJAX)
     */
    public function show(Purchase $purchase): JsonResponse
    {
        // Eager load relationships to avoid N+1 queries
        $purchase->load(['project', 'approvedBy']);
        
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        // Check if user is owner
        $isOwner = $purchase->user_id === $user->id;
        
        // Check if user has Finance/Full access to the project
        $hasProjectAccess = false;
        if ($purchase->project_id && $purchase->project) {
            $accessType = $purchase->project->getManagerAccessType($user->id);
            $hasProjectAccess = in_array($accessType, ['finance', 'full']);
        }
        
        if (!$isAdmin && !$isOwner && !$hasProjectAccess) {
            abort(403, 'Anda tidak memiliki akses ke pembelian ini');
        }

        $purchaseData = $purchase->toArray();
        
        // Ensure all price values are positive
        $purchaseData['unit_price'] = abs(floatval($purchaseData['unit_price'] ?? 0));
        $purchaseData['total_price'] = abs(floatval($purchaseData['total_price'] ?? 0));

        return response()->json([
            'purchase' => $purchaseData,
        ]);
    }

    /**
     * Store a newly created Purchase
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated) {
                $purchase = $this->paymentService->createPurchase($validated, auth()->id());
                
                // Send notification to admins about new submission
                $this->notifyAdmins($purchase, 'purchase');
                
                return null; // Let handleTransaction handle redirect
            },
            'creating',
            'Purchase',
            $request,
            null,
            'Pembelian berhasil diajukan!',
            'Terjadi kesalahan saat mengajukan pembelian. Silakan coba lagi.',
            'purchases.index'
        );
    }

    /**
     * Update the specified Purchase
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $this->authorize('update', $purchase);

        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated, $purchase) {
                return $this->paymentService->updatePurchase($purchase, $validated);
            },
            'updating',
            'Purchase',
            $request,
            $purchase->id,
            'Pembelian berhasil diupdate!',
            'Terjadi kesalahan saat mengupdate pembelian. Silakan coba lagi.',
            'purchases.index'
        );
    }

    /**
     * Remove the specified Purchase
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        $this->authorize('delete', $purchase);

        return $this->handleOperation(
            function () use ($purchase) {
                $purchase->delete();
                
                $routePrefix = $this->getRoutePrefix();
                $route = $routePrefix ? "{$routePrefix}.purchases.index" : 'purchases.index';
                return redirect()->route($route)->with('success', 'Pembelian berhasil dihapus!');
            },
            'deleting',
            'Purchase',
            null,
            $purchase->id
        );
    }
}
