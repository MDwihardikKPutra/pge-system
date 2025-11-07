<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Models\SPD;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StoreSpdRequest;
use App\Http\Requests\UpdateSpdRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SpdController extends BaseController
{
    use AuthorizesRequests, ChecksAuthorization;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of SPDs
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        
        // Admin juga hanya melihat data mereka sendiri (pribadi)
        $query = SPD::with(['project', 'approvedBy', 'user'])
            ->where('user_id', auth()->id());
        
        $spds = $query->when($status !== 'all', function ($q) use ($status) {
                $statusEnum = ApprovalStatus::from($status);
                return $q->where('status', $statusEnum);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
        
        return view('payment.spd.index', compact('spds', 'status', 'projects'));
    }

    /**
     * Display the specified SPD (for AJAX)
     */
    public function show(SPD $spd): JsonResponse
    {
        // Load project managers if project exists (needed for policy check)
        if ($spd->project_id && $spd->project) {
            $spd->load('project.managers');
        }
        
        $this->authorize('view', $spd);
        
        // Eager load relationships to avoid N+1 queries
        $spd->load(['project', 'approvedBy']);

        $spdData = $spd->toArray();
        
        // Parse costs from array if exists, otherwise build from individual fields
        if ($spd->costs && is_array($spd->costs)) {
            // Ensure all amounts are positive
            $spdData['costs'] = array_map(function($cost) {
                if (isset($cost['amount'])) {
                    $cost['amount'] = abs(floatval($cost['amount']));
                }
                return $cost;
            }, $spd->costs);
        } else {
            // Build costs array from individual fields for backward compatibility
            $spdData['costs'] = [];
            if (abs($spd->transport_cost) > 0) {
                $spdData['costs'][] = ['name' => 'Transport', 'description' => '', 'amount' => abs($spd->transport_cost)];
            }
            if (abs($spd->accommodation_cost) > 0) {
                $spdData['costs'][] = ['name' => 'Akomodasi', 'description' => '', 'amount' => abs($spd->accommodation_cost)];
            }
            if (abs($spd->meal_cost) > 0) {
                $spdData['costs'][] = ['name' => 'Makan', 'description' => '', 'amount' => abs($spd->meal_cost)];
            }
            if (abs($spd->other_cost) > 0) {
                $spdData['costs'][] = ['name' => 'Lainnya', 'description' => $spd->other_cost_description ?? '', 'amount' => abs($spd->other_cost)];
            }
            if (empty($spdData['costs'])) {
                $spdData['costs'] = [['name' => '', 'description' => '', 'amount' => 0]];
            }
        }
        
        // Ensure total_cost is positive
        $spdData['total_cost'] = abs(floatval($spdData['total_cost'] ?? 0));
        
        return response()->json([
            'spd' => $spdData,
        ]);
    }

    /**
     * Store a newly created SPD
     */
    public function store(StoreSpdRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated) {
                $spd = $this->paymentService->createSpd($validated, auth()->id());
                
                // Send notification to admins about new submission
                $this->notifyAdmins($spd, 'spd');
                
                return null; // Let handleTransaction handle redirect
            },
            'creating',
            'SPD',
            $request,
            null,
            'SPD berhasil diajukan!',
            'Terjadi kesalahan saat mengajukan SPD. Silakan coba lagi.',
            'spd.index'
        );
    }

    /**
     * Update the specified SPD
     */
    public function update(UpdateSpdRequest $request, SPD $spd): RedirectResponse
    {
        $this->authorize('update', $spd);

        $validated = $request->validated();

        return $this->handleTransaction(
            function () use ($validated, $spd) {
                return $this->paymentService->updateSpd($spd, $validated);
            },
            'updating',
            'SPD',
            $request,
            $spd->id,
            'SPD berhasil diupdate!',
            'Terjadi kesalahan saat mengupdate SPD. Silakan coba lagi.',
            'spd.index'
        );
    }

    /**
     * Remove the specified SPD
     */
    public function destroy(SPD $spd): RedirectResponse
    {
        $this->authorize('delete', $spd);

        return $this->handleOperation(
            function () use ($spd) {
                $spd->delete();
                
                $routePrefix = $this->getRoutePrefix();
                $route = $routePrefix ? "{$routePrefix}.spd.index" : 'spd.index';
                return redirect()->route($route)->with('success', 'SPD berhasil dihapus!');
            },
            'deleting',
            'SPD',
            null,
            $spd->id
        );
    }

    /**
     * Download PDF for approved SPD
     */
    public function downloadPDF(SPD $spd)
    {
        $this->authorize('view', $spd);
        
        // Only allow download for approved SPD
        if ($spd->status->value !== 'approved') {
            abort(403, 'PDF hanya tersedia untuk SPD yang sudah disetujui.');
        }

        $spd->load(['user', 'project', 'approvedBy']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.spd', compact('spd'))
            ->setPaper('a4', 'portrait');

        $filename = 'SPD_' . $spd->spd_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
