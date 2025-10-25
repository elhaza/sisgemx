<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolGrade>
 */
class SchoolGradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $level = 1;
        static $section = 'A';

        $currentLevel = $level;
        $currentSection = $section;

        if ($section === 'Z') {
            $level++;
            $section = 'A';
        } else {
            $section++;
        }

        return [
            'level' => $currentLevel,
            'name' => "Grado {$currentLevel}",
            'section' => $currentSection,
            'school_year_id' => SchoolYear::factory(),
        ];
    }
}
