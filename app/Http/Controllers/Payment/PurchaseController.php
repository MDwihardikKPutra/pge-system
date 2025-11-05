<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Project;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    use ChecksAuthorization;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of Purchases
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Admin can see all Purchases, user only sees their own
        $query = Purchase::with(['project', 'approvedBy', 'user']);
        if (!$this->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        
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
    public function show(Purchase $purchase)
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
    public function store(StorePurchaseRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $totalPrice = $this->paymentService->calculatePurchaseTotalPrice(
                $validated['unit_price'],
                $validated['quantity']
            );

            $purchase = Purchase::create([
                'purchase_number' => $this->paymentService->generatePurchaseNumber(),
                'user_id' => auth()->id(),
                'project_id' => $validated['project_id'],
                'type' => $validated['type'],
                'category' => $validated['category'],
                'item_name' => $validated['item_name'],
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
                'status' => ApprovalStatus::PENDING,
            ]);

            DB::commit();
            
            // Send notification to admins about new submission
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewSubmissionNotification(
                    $purchase,
                    'purchase',
                    auth()->user()
                ));
            }
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.purchases.index')->with('success', 'Pembelian berhasil diajukan!');
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
            \App\Helpers\LogHelper::logControllerError('creating', 'Purchase', $e, null, $request->except(['_token', 'documents']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'Purchase', $e, null, $request->except(['_token', 'documents']));
            return back()->with('error', 'Terjadi kesalahan saat mengajukan pembelian. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Update the specified Purchase
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize('update', $purchase);

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $totalPrice = $this->paymentService->calculatePurchaseTotalPrice(
                $validated['unit_price'],
                $validated['quantity']
            );

            $purchase->update([
                'project_id' => $validated['project_id'],
                'type' => $validated['type'],
                'category' => $validated['category'],
                'item_name' => $validated['item_name'],
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.purchases.index')->with('success', 'Pembelian berhasil diupdate!');
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
            \App\Helpers\LogHelper::logControllerError('updating', 'Purchase', $e, $purchase->id, $request->except(['_token', 'documents']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('updating', 'Purchase', $e, $purchase->id, $request->except(['_token', 'documents']));
            return back()->with('error', 'Terjadi kesalahan saat mengupdate pembelian. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified Purchase
     */
    public function destroy(Purchase $purchase)
    {
        $this->authorize('delete', $purchase);

        try {
            $purchase->delete();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.purchases.index')->with('success', 'Pembelian berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\PaymentException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'Purchase', $e, $purchase->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'Purchase', $e, $purchase->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus pembelian. Silakan coba lagi.');
        }
    }
}
