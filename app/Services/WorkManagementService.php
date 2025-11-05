<?php

namespace App\Services;

use App\Models\WorkPlan;
use App\Models\WorkRealization;
use Illuminate\Support\Facades\Storage;

class WorkManagementService
{
    /**
     * Generate Work Plan number (Format: RK-YYYYMMDD-XXXX)
     */
    public function generateWorkPlanNumber(): string
    {
        $date = date('Ymd');
        $lastPlan = WorkPlan::whereDate('created_at', today())
            ->latest('work_plan_number')
            ->first();

        if ($lastPlan && $lastPlan->work_plan_number) {
            $lastNumber = (int) substr($lastPlan->work_plan_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RK-' . $date . '-' . $newNumber;
    }

    /**
     * Generate Work Realization number (Format: RL-YYYYMMDD-XXXX)
     */
    public function generateWorkRealizationNumber(): string
    {
        $date = date('Ymd');
        $lastRealization = WorkRealization::whereDate('created_at', today())
            ->latest('realization_number')
            ->first();

        if ($lastRealization && $lastRealization->realization_number) {
            $lastNumber = (int) substr($lastRealization->realization_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RL-' . $date . '-' . $newNumber;
    }

    /**
     * Create Work Plan
     */
    public function createWorkPlan(array $data, int $userId): WorkPlan
    {
        $data['user_id'] = $userId;
        $data['work_plan_number'] = $this->generateWorkPlanNumber();
        
        // Set title from description if not provided
        if (empty($data['title']) && !empty($data['description'])) {
            $data['title'] = mb_substr($data['description'], 0, 50);
        }

        return WorkPlan::create($data);
    }

    /**
     * Create Work Realization
     */
    public function createWorkRealization(array $data, int $userId): WorkRealization
    {
        $data['user_id'] = $userId;
        $data['realization_number'] = $this->generateWorkRealizationNumber();
        
        // Set title from description if not provided
        if (empty($data['title']) && !empty($data['description'])) {
            $data['title'] = mb_substr($data['description'], 0, 50);
        }

        // output_files sudah di-handle di controller sebagai array of paths
        // Tidak perlu di-handle lagi di sini

        return WorkRealization::create($data);
    }
}

