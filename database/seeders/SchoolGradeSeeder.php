<?php

namespace Database\Seeders;

use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $grades = [
            ['level' => 1, 'name' => '1° Grado'],
            ['level' => 2, 'name' => '2° Grado'],
            ['level' => 3, 'name' => '3° Grado'],
            ['level' => 4, 'name' => '4° Grado'],
            ['level' => 5, 'name' => '5° Grado'],
            ['level' => 6, 'name' => '6° Grado'],
        ];

        $sections = ['A', 'B'];

        foreach ($grades as $grade) {
            foreach ($sections as $section) {
                SchoolGrade::create([
                    'level' => $grade['level'],
                    'name' => $grade['name'],
                    'section' => $section,
                    'school_year_id' => $activeSchoolYear->id,
                ]);
            }
        }

        $this->command->info('Grados creados: 1° a 6°, secciones A y B (12 grupos total)');
    }
}
