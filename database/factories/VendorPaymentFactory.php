<?php

namespace Database\Factories;

use App\Models\VendorPayment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Project;
use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorPayment>
 */
class VendorPaymentFactory extends Factory
{
    protected $model = VendorPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = date('Y');
        $month = date('m');
        $sequence = fake()->numberBetween(1, 9999);

        return [
            'payment_number' => sprintf('PAY-%s-%04d', date('Y'), $sequence),
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'project_id' => Project::factory(),
            'payment_type' => fake()->randomElement(['DP', 'Termin', 'Pelunasan', 'Full Payment']),
            'payment_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'invoice_number' => 'INV-' . strtoupper(fake()->bothify('??####??')),
            'po_number' => fake()->optional()->passthrough('PO-' . strtoupper(fake()->bothify('??####??'))),
            'amount' => fake()->randomFloat(2, 1000000, 500000000),
            'description' => fake()->sentence(10),
            'notes' => fake()->optional()->sentence(5),
            'status' => ApprovalStatus::PENDING,
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
            'pdf_path' => null,
        ];
    }

    /**
     * Indicate that the payment is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApprovalStatus::APPROVED,
                'approved_by' => User::factory(),
                'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    /**
     * Indicate that the payment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApprovalStatus::REJECTED,
                'approved_by' => User::factory(),
                'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'rejection_reason' => fake()->sentence(10),
            ];
        });
    }

    /**
     * Set payment type.
     */
    public function paymentType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => $type,
        ]);
    }
}

