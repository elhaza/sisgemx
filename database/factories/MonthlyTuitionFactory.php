<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyTuition>
 */
class MonthlyTuitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_year_id' => \App\Models\SchoolYear::factory(),
            'year' => 2025,
            'month' => $this->faker->numberBetween(1, 12),
            'amount' => 3000.00,
        ];
    }
}
