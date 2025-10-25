<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'teacher_id' => User::factory(),
            'grade_level' => $this->faker->numberBetween(1, 6),
            'school_year_id' => SchoolYear::factory(),
        ];
    }
}
