<?php

namespace App\Services;

use App\Models\SPD;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Enums\ApprovalStatus;

class PaymentService
{
    /**
     * Generate SPD number: SPD-YYYYMMDD-XXX
     */
    public function generateSpdNumber(): string
    {
        $date = date('Ymd');
        $lastSPD = SPD::whereDate('created_at', today())
            ->orderBy('spd_number', 'desc')
            ->first();

        if ($lastSPD && preg_match('/SPD-\d{8}-(\d+)/', $lastSPD->spd_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('SPD-%s-%03d', $date, $nextNumber);
    }

    /**
     * Generate Purchase number: PUR-YYYYMMDD-XXXX
     */
    public function generatePurchaseNumber(): string
    {
        $date = date('Ymd');
        $lastPurchase = Purchase::whereDate('created_at', today())
            ->orderBy('purchase_number', 'desc')
            ->first();

        if ($lastPurchase && preg_match('/PUR-\d{8}-(\d+)/', $lastPurchase->purchase_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('PUR-%s-%04d', $date, $nextNumber);
    }

    /**
     * Generate Vendor Payment number: VP-YYYY-MM-XXXX
     */
    public function generateVendorPaymentNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $latestPayment = VendorPayment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($latestPayment && preg_match('/VP-\d{4}-\d{2}-(\d+)/', $latestPayment->payment_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('VP-%s-%s-%04d', $year, $month, $sequence);
    }

    /**
     * Calculate total cost for SPD
     */
    public function calculateSpdTotalCost(float $transport, float $accommodation, float $meal, float $other): float
    {
        return $transport + $accommodation + $meal + $other;
    }

    /**
     * Process and calculate costs array from request data
     * Returns array with costs and total_cost
     */
    public function processCostsFromRequest(array $validated): array
    {
        $totalCost = 0;
        $costs = [];

        if (isset($validated['cost_amount']) && is_array($validated['cost_amount'])) {
            foreach ($validated['cost_amount'] as $index => $amount) {
                $amount = abs((float) $amount);
                $costs[] = [
                    'name' => $validated['cost_name'][$index] ?? '',
                    'description' => $validated['cost_description'][$index] ?? '',
                    'amount' => $amount,
                ];
                $totalCost += $amount;
            }
        }

        // For backward compatibility, store first 4 costs in separate fields
        $transportCost = abs($costs[0]['amount'] ?? 0);
        $accommodationCost = abs($costs[1]['amount'] ?? 0);
        $mealCost = abs($costs[2]['amount'] ?? 0);
        $otherCost = 0;
        $otherCostDescription = '';

        if (count($costs) > 3) {
            // Sum remaining costs as "other_cost"
            for ($i = 3; $i < count($costs); $i++) {
                $otherCost += abs($costs[$i]['amount']);
                if ($otherCostDescription) {
                    $otherCostDescription .= ', ';
                }
                $otherCostDescription .= $costs[$i]['name'];
            }
        }

        return [
            'costs' => $costs,
            'total_cost' => abs($totalCost),
            'transport_cost' => $transportCost,
            'accommodation_cost' => $accommodationCost,
            'meal_cost' => $mealCost,
            'other_cost' => abs($otherCost),
            'other_cost_description' => $otherCostDescription ?: null,
        ];
    }

    /**
     * Calculate total price for Purchase
     */
    public function calculatePurchaseTotalPrice(float $unitPrice, int $quantity): float
    {
        return $unitPrice * $quantity;
    }

    /**
     * Update payment status (for approval)
     */
    public function updatePaymentStatus($model, string $type, ApprovalStatus $status, ?string $notes = null, ?string $rejectionReason = null)
    {
        $updateData = [
            'status' => $status,
        ];

        if ($status === ApprovalStatus::APPROVED) {
            $updateData['notes'] = $notes;
            $updateData['rejection_reason'] = null;
            $updateData['approved_by'] = auth()->id();
            $updateData['approved_at'] = now();
        } elseif ($status === ApprovalStatus::REJECTED) {
            $updateData['rejection_reason'] = $rejectionReason;
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        }

        $model->update($updateData);
        
        // Send notification to user who submitted
        $model->refresh()->load('user');
        $approver = auth()->user();
        
        if ($model->user) {
            $model->user->notify(new \App\Notifications\SubmissionStatusNotification(
                $model,
                $type,
                $status->value,
                $approver
            ));
        }

        return $model;
    }

    /**
     * Create a new SPD
     *
     * @param array $validated
     * @param int $userId
     * @return SPD
     */
    public function createSpd(array $validated, int $userId): SPD
    {
        $costData = $this->processCostsFromRequest($validated);

        return SPD::create([
            'spd_number' => $this->generateSpdNumber(),
            'user_id' => $userId,
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
    }

    /**
     * Update an existing SPD
     *
     * @param SPD $spd
     * @param array $validated
     * @return SPD
     */
    public function updateSpd(SPD $spd, array $validated): SPD
    {
        $costData = $this->processCostsFromRequest($validated);

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

        return $spd;
    }

    /**
     * Create a new Purchase
     *
     * @param array $validated
     * @param int $userId
     * @return Purchase
     */
    public function createPurchase(array $validated, int $userId): Purchase
    {
        $totalPrice = $this->calculatePurchaseTotalPrice(
            $validated['unit_price'],
            $validated['quantity']
        );

        return Purchase::create([
            'purchase_number' => $this->generatePurchaseNumber(),
            'user_id' => $userId,
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
    }

    /**
     * Update an existing Purchase
     *
     * @param Purchase $purchase
     * @param array $validated
     * @return Purchase
     */
    public function updatePurchase(Purchase $purchase, array $validated): Purchase
    {
        $totalPrice = $this->calculatePurchaseTotalPrice(
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

        return $purchase;
    }

    /**
     * Create a new Vendor Payment
     *
     * @param array $validated
     * @param int $userId
     * @return VendorPayment
     */
    public function createVendorPayment(array $validated, int $userId): VendorPayment
    {
        return VendorPayment::create([
            'payment_number' => $this->generateVendorPaymentNumber(),
            'user_id' => $userId,
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
    }

    /**
     * Update an existing Vendor Payment
     *
     * @param VendorPayment $vendorPayment
     * @param array $validated
     * @return VendorPayment
     */
    public function updateVendorPayment(VendorPayment $vendorPayment, array $validated): VendorPayment
    {
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

        return $vendorPayment;
    }
}

