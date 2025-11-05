<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\SPD;
use App\Models\Project;
use App\Services\PaymentService;
use App\Traits\ChecksAuthorization;
use App\Enums\ApprovalStatus;
use App\Http\Requests\StoreSpdRequest;
use App\Http\Requests\UpdateSpdRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpdController extends Controller
{
    use ChecksAuthorization;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of SPDs
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Admin can see all SPDs, user only sees their own
        $query = SPD::with(['project', 'approvedBy', 'user']);
        if (!$this->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        
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
    public function show(SPD $spd)
    {
        // Eager load relationships to avoid N+1 queries
        $spd->load(['project', 'approvedBy']);
        
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        // Check if user is owner
        $isOwner = $spd->user_id === $user->id;
        
        // Check if user has Finance/Full access to the project
        $hasProjectAccess = false;
        if ($spd->project_id && $spd->project) {
            $accessType = $spd->project->getManagerAccessType($user->id);
            $hasProjectAccess = in_array($accessType, ['finance', 'full']);
        }
        
        if (!$isAdmin && !$isOwner && !$hasProjectAccess) {
            abort(403, 'Anda tidak memiliki akses ke SPD ini');
        }

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
    public function store(StoreSpdRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Process costs using service
            $costData = $this->paymentService->processCostsFromRequest($validated);

            $spd = SPD::create([
                'spd_number' => $this->paymentService->generateSpdNumber(),
                'user_id' => auth()->id(),
                'project_id' => $validated['project_id'],
                'destination' => $validated['destination'],
                'departure_date' => $validated['departure_date'],
                'return_date' => $validated['return_date'],
                'purpose' => $validated['purpose'],
                'transport_cost' => $costData['transport_cost'],
                'accommodation_cost' => $costData['accommodation_cost'],
                'meal_cost' => $costData['meal_cost'],
                'other_cost' => $costData['other_cost'],
                'other_cost_description' => $costData['other_cost_description'],
                'total_cost' => $costData['total_cost'],
                'notes' => $validated['notes'] ?? null,
                'costs' => $costData['costs'],
                'status' => ApprovalStatus::PENDING,
            ]);

            DB::commit();
            
            // Send notification to admins about new submission
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewSubmissionNotification(
                    $spd,
                    'spd',
                    auth()->user()
                ));
            }

            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.spd.index')->with('success', 'SPD berhasil diajukan!');
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
            \App\Helpers\LogHelper::logControllerError('creating', 'SPD', $e, null, $request->except(['_token', 'documents']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('creating', 'SPD', $e, null, $request->except(['_token', 'documents']));
            return back()->with('error', 'Terjadi kesalahan saat mengajukan SPD. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Update the specified SPD
     */
    public function update(UpdateSpdRequest $request, SPD $spd)
    {
        $this->authorize('update', $spd);

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Process costs using service
            $costData = $this->paymentService->processCostsFromRequest($validated);

            $spd->update([
                'project_id' => $validated['project_id'],
                'destination' => $validated['destination'],
                'departure_date' => $validated['departure_date'],
                'return_date' => $validated['return_date'],
                'purpose' => $validated['purpose'],
                'transport_cost' => $costData['transport_cost'],
                'accommodation_cost' => $costData['accommodation_cost'],
                'meal_cost' => $costData['meal_cost'],
                'other_cost' => $costData['other_cost'],
                'other_cost_description' => $costData['other_cost_description'],
                'total_cost' => $costData['total_cost'],
                'notes' => $validated['notes'] ?? null,
                'costs' => $costData['costs'],
            ]);

            DB::commit();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.spd.index')->with('success', 'SPD berhasil diupdate!');
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
            \App\Helpers\LogHelper::logControllerError('updating', 'SPD', $e, $spd->id, $request->except(['_token', 'documents']));
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Helpers\LogHelper::logControllerError('updating', 'SPD', $e, $spd->id, $request->except(['_token', 'documents']));
            return back()->with('error', 'Terjadi kesalahan saat mengupdate SPD. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified SPD
     */
    public function destroy(SPD $spd)
    {
        $this->authorize('delete', $spd);

        try {
            $spd->delete();
            
            // Redirect based on route prefix (admin or user)
            $routePrefix = request()->is('admin/*') ? 'admin' : 'user';
            return redirect()->route($routePrefix . '.spd.index')->with('success', 'SPD berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\App\Exceptions\PaymentException $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'SPD', $e, $spd->id);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Helpers\LogHelper::logControllerError('deleting', 'SPD', $e, $spd->id);
            return back()->with('error', 'Terjadi kesalahan saat menghapus SPD. Silakan coba lagi.');
        }
    }
}
