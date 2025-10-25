<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $teachers = User::where('role', UserRole::Teacher)->get();

        // Subjects per grade level
        $subjectsByGrade = [
            1 => ['Español', 'Matemáticas', 'Conocimiento del Medio', 'Artes', 'Educación Física'],
            2 => ['Español', 'Matemáticas', 'Conocimiento del Medio', 'Artes', 'Educación Física'],
            3 => ['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia', 'Artes', 'Educación Física'],
            4 => ['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia', 'Geografía', 'Artes', 'Educación Física'],
            5 => ['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia', 'Geografía', 'Formación Cívica y Ética', 'Artes', 'Educación Física'],
            6 => ['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia', 'Geografía', 'Formación Cívica y Ética', 'Artes', 'Educación Física'],
        ];

        $teacherIndex = 0;

        foreach ($subjectsByGrade as $gradeLevel => $subjects) {
            foreach ($subjects as $subjectName) {
                // Assign teacher (rotate through available teachers)
                $teacher = $teachers[$teacherIndex % $teachers->count()];
                $teacherIndex++;

                Subject::create([
                    'name' => $subjectName,
                    'description' => $subjectName.' para '.$gradeLevel.'° grado',
                    'teacher_id' => $teacher->id,
                    'grade_level' => $gradeLevel,
                    'school_year_id' => $activeSchoolYear->id,
                ]);
            }
        }

        $this->command->info('Materias creadas por grado con maestros asignados');
    }
}
