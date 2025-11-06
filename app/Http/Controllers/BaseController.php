<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Helpers\LogHelper;
use App\Exceptions\PaymentException;
use App\Exceptions\LeaveRequestException;
use App\Exceptions\LeaveApprovalException;
use App\Exceptions\PaymentApprovalException;
use App\Exceptions\WorkException;

abstract class BaseController extends Controller
{
    /**
     * Execute a callback within a database transaction with error handling
     *
     * @param callable $callback
     * @param string $operation Operation name (e.g., 'creating', 'updating', 'deleting')
     * @param string $modelName Model name (e.g., 'Purchase', 'VendorPayment')
     * @param Request|null $request Request instance for logging
     * @param int|null $modelId Model ID for logging
     * @param string|null $successMessage Success message for redirect
     * @param string|null $errorMessage Generic error message
     * @param string|null $redirectRoute Route name for redirect (without prefix)
     * @return RedirectResponse
     */
    protected function handleTransaction(
        callable $callback,
        string $operation,
        string $modelName,
        ?Request $request = null,
        ?int $modelId = null,
        ?string $successMessage = null,
        ?string $errorMessage = null,
        ?string $redirectRoute = null
    ): RedirectResponse {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            // Get route prefix
            $routePrefix = $this->getRoutePrefix();
            
            // Determine redirect route
            if ($redirectRoute) {
                $fullRoute = $routePrefix ? "{$routePrefix}.{$redirectRoute}" : $redirectRoute;
                return redirect()->route($fullRoute)->with('success', $successMessage ?? 'Operasi berhasil dilakukan!');
            }
            
            return $result instanceof RedirectResponse 
                ? $result 
                : back()->with('success', $successMessage ?? 'Operasi berhasil dilakukan!');
                
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
            
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Data tidak ditemukan.')->withInput();
            
        } catch (AuthorizationException $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
            
        } catch (PaymentException|LeaveRequestException|LeaveApprovalException|PaymentApprovalException|WorkException $e) {
            DB::rollBack();
            $this->logError($operation, $modelName, $e, $modelId, $request);
            return back()->with('error', $e->getMessage())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError($operation, $modelName, $e, $modelId, $request);
            
            $errorMsg = $errorMessage ?? "Terjadi kesalahan saat melakukan operasi. Silakan coba lagi.";
            return back()->with('error', $errorMsg)->withInput();
        }
    }

    /**
     * Handle operations without transaction (for read operations)
     *
     * @param callable $callback
     * @param string $operation
     * @param string $modelName
     * @param Request|null $request
     * @param int|null $modelId
     * @return mixed
     */
    protected function handleOperation(
        callable $callback,
        string $operation,
        string $modelName,
        ?Request $request = null,
        ?int $modelId = null
    ): mixed {
        try {
            return $callback();
            
        } catch (ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Data tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Data tidak ditemukan.');
            
        } catch (AuthorizationException $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 403);
            }
            abort(403, $e->getMessage());
            
        } catch (PaymentException|LeaveRequestException|LeaveApprovalException|PaymentApprovalException|WorkException $e) {
            $this->logError($operation, $modelName, $e, $modelId, $request);
            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
            
        } catch (\Exception $e) {
            $this->logError($operation, $modelName, $e, $modelId, $request);
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan saat melakukan operasi. Silakan coba lagi.'], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat melakukan operasi. Silakan coba lagi.');
        }
    }

    /**
     * Log error using LogHelper
     *
     * @param string $operation
     * @param string $modelName
     * @param \Exception $exception
     * @param int|null $modelId
     * @param Request|null $request
     * @return void
     */
    protected function logError(
        string $operation,
        string $modelName,
        \Exception $exception,
        ?int $modelId = null,
        ?Request $request = null
    ): void {
        $requestData = $request ? $request->except(['_token', 'password', 'password_confirmation', 'documents', 'attachment', 'output_files']) : null;
        LogHelper::logControllerError($operation, $modelName, $exception, $modelId, $requestData);
    }

    /**
     * Get route prefix based on current request
     *
     * @return string|null
     */
    protected function getRoutePrefix(): ?string
    {
        if (request()->is('admin/*')) {
            return 'admin';
        }
        
        if (request()->is('user/*')) {
            return 'user';
        }
        
        return null;
    }

    /**
     * Send notification to admins about new submission
     *
     * @param mixed $model
     * @param string $type
     * @return void
     */
    protected function notifyAdmins($model, string $type): void
    {
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewSubmissionNotification(
                $model,
                $type,
                auth()->user()
            ));
        }
    }
}
