<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * Observer untuk membuat activity log otomatis
 * ketika ada perubahan data (create, update, delete, approve, reject)
 */
class ActivityLogObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created($model): void
    {
        if (Auth::check()) {
            $this->logActivity('created', $this->getDescription('created', $model), $model);
        }
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated($model): void
    {
        if (Auth::check()) {
            $this->logActivity('updated', $this->getDescription('updated', $model), $model);
        }
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model): void
    {
        if (Auth::check()) {
            $this->logActivity('deleted', $this->getDescription('deleted', $model), $model);
        }
    }

    /**
     * Log activity
     */
    protected function logActivity(string $action, string $description, $model): void
    {
        try {
            if (!Auth::check()) {
                return; // Skip if no authenticated user
            }

            $modelType = get_class($model);
            if (!$modelType) {
                return; // Skip if model type cannot be determined
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $model->id ?? null,
                'description' => $description,
                'properties' => $this->getProperties($model),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent breaking the main operation
            \Log::error('Failed to create activity log: ' . $e->getMessage());
        }
    }

    /**
     * Get description for activity log
     */
    protected function getDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $actionLabel = match($action) {
            'created' => 'membuat',
            'updated' => 'memperbarui',
            'deleted' => 'menghapus',
            default => $action,
        };

        // Get identifier from model
        $identifier = $this->getModelIdentifier($model);

        return ucfirst($actionLabel) . ' ' . $modelName . ($identifier ? ' ' . $identifier : '');
    }

    /**
     * Get model identifier (number, name, etc)
     */
    protected function getModelIdentifier($model): ?string
    {
        // Try common identifier fields
        if (isset($model->work_plan_number)) {
            return $model->work_plan_number;
        }
        if (isset($model->realization_number)) {
            return $model->realization_number;
        }
        if (isset($model->spd_number)) {
            return $model->spd_number;
        }
        if (isset($model->purchase_number)) {
            return $model->purchase_number;
        }
        if (isset($model->payment_number)) {
            return $model->payment_number;
        }
        if (isset($model->leave_number)) {
            return $model->leave_number;
        }
        if (isset($model->name)) {
            return $model->name;
        }
        if (isset($model->title)) {
            return $model->title;
        }
        if (isset($model->id)) {
            return '#' . $model->id;
        }

        return null;
    }

    /**
     * Get properties for activity log
     */
    protected function getProperties($model): array
    {
        // Only include safe, non-sensitive properties
        $properties = [];
        
        if (isset($model->status)) {
            $properties['status'] = is_object($model->status) ? $model->status->value : $model->status;
        }
        
        if (isset($model->plan_date)) {
            $properties['plan_date'] = $model->plan_date;
        }
        
        if (isset($model->realization_date)) {
            $properties['realization_date'] = $model->realization_date;
        }

        return $properties;
    }
}

