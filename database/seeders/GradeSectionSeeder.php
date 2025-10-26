<?php

namespace Database\Seeders;

use App\Models\GradeSection;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class GradeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $grades = [
            ['grade_level' => 1],
            ['grade_level' => 2],
            ['grade_level' => 3],
            ['grade_level' => 4],
            ['grade_level' => 5],
            ['grade_level' => 6],
        ];

        $sections = ['A', 'B'];

        foreach ($grades as $grade) {
            foreach ($sections as $section) {
                GradeSection::create([
                    'grade_level' => $grade['grade_level'],
                    'section' => $section,
                    'school_year_id' => $activeSchoolYear->id,
                ]);
            }
        }

        $this->command->info('Secciones de grado creadas: 1° a 6°, secciones A y B (12 grupos total)');
    }
}
