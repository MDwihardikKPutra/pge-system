<?php

namespace Database\Factories;

use App\Models\SPD;
use App\Models\User;
use App\Models\Project;
use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SPD>
 */
class SpdFactory extends Factory
{
    protected $model = SPD::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use Carbon for reliable date handling
        $departureDate = \Carbon\Carbon::parse(fake()->dateTimeBetween('now', '+1 month'));
        // Ensure return date is after departure date by adding days
        $returnDate = $departureDate->copy()->addDays(fake()->numberBetween(1, 7));

        $transportCost = fake()->randomFloat(2, 500000, 10000000);
        $accommodationCost = fake()->randomFloat(2, 500000, 5000000);
        $mealCost = fake()->randomFloat(2, 200000, 2000000);
        $otherCost = fake()->randomFloat(2, 0, 1000000);
        $totalCost = $transportCost + $accommodationCost + $mealCost + $otherCost;

        $date = date('Ymd');
        $sequence = fake()->numberBetween(1, 999);

        $costs = [
            [
                'name' => 'Transport',
                'description' => fake()->sentence(5),
                'amount' => $transportCost,
            ],
            [
                'name' => 'Hotel',
                'description' => fake()->sentence(5),
                'amount' => $accommodationCost,
            ],
            [
                'name' => 'Makan',
                'description' => fake()->sentence(5),
                'amount' => $mealCost,
            ],
        ];

        if ($otherCost > 0) {
            $costs[] = [
                'name' => 'Lainnya',
                'description' => fake()->sentence(5),
                'amount' => $otherCost,
            ];
        }

        return [
            'spd_number' => sprintf('SPD-%s-%03d', $date, $sequence),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'destination' => fake()->city(),
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'purpose' => fake()->sentence(10),
            'transport_cost' => $transportCost,
            'accommodation_cost' => $accommodationCost,
            'meal_cost' => $mealCost,
            'other_cost' => $otherCost,
            'other_cost_description' => $otherCost > 0 ? fake()->sentence(5) : null,
            'total_cost' => $totalCost,
            'costs' => $costs,
            'notes' => fake()->optional()->sentence(5),
            'status' => ApprovalStatus::PENDING,
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
            'pdf_path' => null,
        ];
    }

    /**
     * Indicate that the SPD is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            // Ensure dates are valid if not set
            if (!isset($attributes['departure_date'])) {
                $departureDate = fake()->dateTimeBetween('now', '+1 month');
                $departureTimestamp = $departureDate->getTimestamp();
                $returnDate = fake()->dateTimeBetween('@' . $departureTimestamp, '+7 days');
                $attributes['departure_date'] = $departureDate;
                $attributes['return_date'] = $returnDate;
            }
            
            return array_merge($attributes, [
                'status' => ApprovalStatus::APPROVED,
                'approved_by' => User::factory(),
                'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            ]);
        });
    }

    /**
     * Indicate that the SPD is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            // Ensure dates are valid if not set
            if (!isset($attributes['departure_date'])) {
                $departureDate = fake()->dateTimeBetween('now', '+1 month');
                $departureTimestamp = $departureDate->getTimestamp();
                $returnDate = fake()->dateTimeBetween('@' . $departureTimestamp, '+7 days');
                $attributes['departure_date'] = $departureDate;
                $attributes['return_date'] = $returnDate;
            }
            
            return array_merge($attributes, [
                'status' => ApprovalStatus::REJECTED,
                'approved_by' => User::factory(),
                'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'rejection_reason' => fake()->sentence(10),
            ]);
        });
    }

    /**
     * Create SPD with custom costs array.
     */
    public function withCosts(array $costs): static
    {
        $totalCost = array_sum(array_column($costs, 'amount'));

        return $this->state(function (array $attributes) use ($costs, $totalCost) {
            return [
                'costs' => $costs,
                'transport_cost' => $this->getCostByName($costs, 'Transport') ?? 0,
                'accommodation_cost' => $this->getCostByName($costs, 'Hotel') ?? 0,
                'meal_cost' => $this->getCostByName($costs, 'Makan') ?? 0,
                'other_cost' => $this->getCostByName($costs, 'Lainnya') ?? 0,
                'total_cost' => $totalCost,
            ];
        });
    }

    /**
     * Get cost amount by name from costs array.
     */
    private function getCostByName(array $costs, string $name): ?float
    {
        foreach ($costs as $cost) {
            if (($cost['name'] ?? '') === $name) {
                return $cost['amount'] ?? 0;
            }
        }
        return null;
    }
}

