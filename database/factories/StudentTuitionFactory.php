<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentTuition>
 */
class StudentTuitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'school_year_id' => \App\Models\SchoolYear::factory(),
            'monthly_tuition_id' => \App\Models\MonthlyTuition::factory(),
            'year' => $this->faker->year(),
            'month' => $this->faker->numberBetween(1, 12),
            'monthly_amount' => 3000.00,
            'discount_percentage' => 0,
            'due_date' => now()->subMonths(1),
        ];
    }
}
