<?php

namespace Database\Seeders;

use App\Gender;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\StudentStatus;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $schoolGrades = SchoolGrade::where('school_year_id', $activeSchoolYear->id)
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        $parents = User::where('role', UserRole::Parent)->get();

        $firstNames = [
            'male' => ['Jose', 'Luis', 'Carlos', 'Miguel', 'Juan', 'Alejandro', 'Francisco', 'Diego', 'Javier', 'Fernando', 'Antonio', 'Raul', 'Roberto', 'Daniel'],
            'female' => ['Maria', 'Guadalupe', 'Rosa', 'Ana', 'Carmen', 'Isabel', 'Laura', 'Patricia', 'Claudia', 'Sofia', 'Gabriela', 'Andrea', 'Mariana', 'Valentina'],
        ];

        $lastNames1 = ['Garcia', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez', 'Hernandez', 'Perez', 'Sanchez', 'Ramirez', 'Torres', 'Flores', 'Rivera', 'Gomez', 'Diaz'];
        $lastNames2 = ['Cruz', 'Reyes', 'Morales', 'Jimenez', 'Ruiz', 'Mendoza', 'Vargas', 'Castro', 'Ortiz', 'Romero', 'Medina', 'Navarro', 'Gutierrez', 'Chavez'];

        $studentCount = 0;
        $parentIndex = 0;

        foreach ($schoolGrades as $schoolGrade) {
            // 14 students per group
            for ($i = 1; $i <= 14; $i++) {
                $studentCount++;

                // Alternate gender
                $gender = $i % 2 === 0 ? Gender::Female : Gender::Male;
                $genderKey = $gender === Gender::Male ? 'male' : 'female';

                // Pick names
                $firstName = $firstNames[$genderKey][($i - 1) % count($firstNames[$genderKey])];
                $lastName1 = $lastNames1[($studentCount - 1) % count($lastNames1)];
                $lastName2 = $lastNames2[($studentCount - 1) % count($lastNames2)];

                // Assign parent (rotate through parents, 2 students per tutor on average)
                $tutor1 = $parents[floor($parentIndex / 2) % $parents->count()];
                $tutor2 = $parents[(floor($parentIndex / 2) + 1) % $parents->count()];
                $parentIndex++;

                // Generate CURP-like code (simplified) - 18 characters exactly
                $year = 2010 + $schoolGrade->level; // Approximate birth year based on grade
                $yearShort = substr((string) $year, 2, 2); // Last 2 digits of year
                $genderCode = $gender === Gender::Male ? 'H' : 'M';
                $stateCode = 'EM'; // Estado de México
                // Format: AAAA YYMMDD H/M AA AAA - 18 chars total
                // AAAA (4) + YYMMDD (6) + H/M (1) + AA (2) + AAA (3) + N (2) = 18
                $curp = strtoupper(
                    substr($lastName1, 0, 2).
                    substr($lastName2, 0, 2).
                    substr($firstName, 0, 2).
                    $yearShort.'0101'.
                    $genderCode.
                    $stateCode.
                    chr(65 + ($studentCount % 26)). // Random consonant (1 char)
                    chr(65 + (($studentCount * 2) % 26)). // Random consonant (1 char)
                    ($studentCount % 10) // 1 digit number
                );

                // Create student user
                $studentUser = User::create([
                    'name' => $firstName,
                    'apellido_paterno' => $lastName1,
                    'apellido_materno' => $lastName2,
                    'email' => 'estudiante'.$studentCount.'@escuela.com',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Student,
                    'parent_id' => $tutor1->id,
                ]);

                // Create student record
                $enrollmentNumber = 'EST-'.($activeSchoolYear->name).'-'.str_pad($studentCount, 3, '0', STR_PAD_LEFT);

                Student::create([
                    'user_id' => $studentUser->id,
                    'school_year_id' => $activeSchoolYear->id,
                    'school_grade_id' => $schoolGrade->id,
                    'enrollment_number' => $enrollmentNumber,
                    'status' => StudentStatus::Active,
                    'curp' => $curp,
                    'date_of_birth' => $year.'-01-01',
                    'gender' => $gender,
                    'birth_country' => 'México',
                    'birth_state' => 'Estado de México',
                    'birth_city' => 'Toluca',
                    'phone_number' => '722'.str_pad($studentCount, 7, '0', STR_PAD_LEFT),
                    'address' => 'Calle Principal '.$studentCount.', Col. Centro, Toluca',
                    'parent_email' => $tutor1->email,
                    'tutor_1_id' => $tutor1->id,
                    'tutor_2_id' => $tutor2->id,
                    'requires_invoice' => $studentCount % 5 === 0, // 20% require invoice
                ]);
            }
        }

        $this->command->info('Estudiantes creados: 168 total (14 por grupo en 12 grupos)');
    }
}
