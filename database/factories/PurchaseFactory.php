<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Project;
use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 100);
        $unitPrice = fake()->randomFloat(2, 100000, 50000000);
        // Calculate total price
        $totalPrice = round($quantity * $unitPrice, 2);

        $date = date('Ymd');
        $sequence = fake()->numberBetween(1, 9999);

        return [
            'purchase_number' => sprintf('PUR-%s-%04d', $date, $sequence),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'type' => fake()->randomElement(['Barang', 'Jasa', 'Aset']),
            'category' => fake()->randomElement([
                'IT & Teknologi',
                'Alat Tulis Kantor',
                'Peralatan',
                'Perlengkapan',
                'Jasa Profesional',
                'Lainnya',
            ]),
            'item_name' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'quantity' => $quantity,
            'unit' => fake()->randomElement(['Unit', 'Pcs', 'Set', 'Paket', 'Box', 'Bulan', 'Tahun']),
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'notes' => fake()->optional()->sentence(5),
            'status' => ApprovalStatus::PENDING,
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
            'pdf_path' => null,
        ];
    }

    /**
     * Indicate that the purchase is approved.
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
     * Indicate that the purchase is rejected.
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
     * Set purchase type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Set purchase category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}

