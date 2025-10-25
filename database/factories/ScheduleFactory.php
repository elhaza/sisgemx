<?php

namespace Database\Factories;

use App\DayOfWeek;
use App\Models\SchoolGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_grade_id' => SchoolGrade::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement(DayOfWeek::cases()),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
            'classroom' => $this->faker->word(),
        ];
    }
}
