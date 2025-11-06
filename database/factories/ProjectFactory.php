<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projectTypes = [
            'PLTP',
            'Geothermal Survey',
            'Maintenance',
            'Training',
            'Environmental Assessment',
            'Optimization',
        ];

        $projectType = fake()->randomElement($projectTypes);
        $projectName = $projectType . ' ' . fake()->words(2, true);

        return [
            'name' => $projectName,
            'code' => 'PRJ-' . fake()->year() . '-' . str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'client' => fake()->randomElement([
                'PT Pertamina Geothermal Energy',
                'PT PLN',
                'PT Indonesia Power',
                'PT Geo Dipa Energi',
                'Kementerian ESDM',
                'Internal',
            ]),
            'description' => fake()->paragraph(),
            'start_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('+1 year', '+2 years'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the project is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

