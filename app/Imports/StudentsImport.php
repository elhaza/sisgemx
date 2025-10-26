<?php

namespace App\Imports;

use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements FromCollection, WithHeadingRow
{
    protected SchoolYear $schoolYear;

    protected array $results = [
        'success' => 0,
        'errors' => [],
    ];

    public function __construct(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
    }

    public function collection($collection)
    {
        foreach ($collection as $row) {
            try {
                $this->processRow($row);
            } catch (\Exception $e) {
                $this->results['errors'][] = 'Fila: '.$e->getMessage();
            }
        }

        return $collection;
    }

    protected function processRow($row): void
    {
        // Validate required fields
        $this->validateRequiredFields($row);

        // Get or create school grade
        $level = (int) trim($row['grado']);
        $section = strtoupper(trim($row['seccion']));

        $schoolGrade = SchoolGrade::firstOrCreate(
            [
                'level' => $level,
                'section' => $section,
                'school_year_id' => $this->schoolYear->id,
            ],
            [
                'name' => "{$level}° Grado",
            ]
        );

        // Create or get student user
        $studentEmail = trim($row['correo_estudiante']);
        $studentUser = User::where('email', $studentEmail)->first();

        if (! $studentUser) {
            $studentUser = User::create([
                'name' => trim($row['nombre_estudiante']),
                'email' => $studentEmail,
                'apellido_paterno' => trim($row['apellido_paterno_estudiante']),
                'apellido_materno' => trim($row['apellido_materno_estudiante']),
                'password' => bcrypt($row['contrasena'] ?? 'sisgemx123'),
                'role' => UserRole::Student,
            ]);
        }

        // Create or get parent user
        $parentEmail = trim($row['correo_padres']);
        $parentUser = User::where('email', $parentEmail)->first();

        if (! $parentUser) {
            $parentUser = User::create([
                'name' => trim($row['nombre_padre']),
                'email' => $parentEmail,
                'apellido_paterno' => trim($row['apellido_paterno_padre']),
                'apellido_materno' => trim($row['apellido_materno_padre']),
                'password' => bcrypt('sisgemx123'),
                'role' => UserRole::Parent,
            ]);
        }

        // Create student record
        $enrollmentNumber = trim($row['matricula'] ?? '') ?: $this->generateEnrollmentNumber();

        $student = Student::create([
            'user_id' => $studentUser->id,
            'school_year_id' => $this->schoolYear->id,
            'school_grade_id' => $schoolGrade->id,
            'tutor_1_id' => $parentUser->id,
            'tutor_2_id' => null,
            'enrollment_number' => $enrollmentNumber,
            'curp' => trim($row['curp']),
            'date_of_birth' => Carbon::createFromFormat('d/m/Y', trim($row['fecha_nacimiento']))->format('Y-m-d'),
            'gender' => strtolower(substr(trim($row['sexo']), 0, 1)),
            'birth_country' => trim($row['pais_nacimiento']),
            'birth_state' => trim($row['estado_nacimiento']),
            'birth_city' => trim($row['ciudad_nacimiento']),
            'phone_number' => trim($row['telefono']),
            'address' => trim($row['domicilio']),
            'parent_email' => $parentEmail,
            'status' => 'active',
        ]);

        // Create tuition records for this student
        $this->createStudentTuitions($student);

        $this->results['success']++;
    }

    protected function validateRequiredFields($row): void
    {
        $required = [
            'nombre_estudiante',
            'apellido_paterno_padre',
            'apellido_materno_padre',
            'sexo',
            'curp',
            'pais_nacimiento',
            'estado_nacimiento',
            'ciudad_nacimiento',
            'telefono',
            'domicilio',
            'correo_padres',
            'grado',
            'seccion',
        ];

        foreach ($required as $field) {
            if (empty($row[$field])) {
                throw new \Exception("Campo requerido faltante: {$field}");
            }
        }
    }

    protected function generateEnrollmentNumber(): string
    {
        $lastStudent = Student::where('school_year_id', $this->schoolYear->id)
            ->orderBy('enrollment_number', 'desc')
            ->first();

        if (! $lastStudent) {
            return $this->schoolYear->start_date->format('Y').'001';
        }

        $lastNumber = (int) substr($lastStudent->enrollment_number, -3);

        return $this->schoolYear->start_date->format('Y').str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    protected function createStudentTuitions(Student $student): void
    {
        // Create a monthly tuition record for each month of the school year
        $startDate = $this->schoolYear->start_date;
        $endDate = $this->schoolYear->end_date;

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            StudentTuition::create([
                'student_id' => $student->id,
                'school_year_id' => $this->schoolYear->id,
                'year' => $currentDate->year,
                'month' => $currentDate->month,
                'monthly_amount' => 0,
                'discount_percentage' => 0,
            ]);

            $currentDate->addMonth();
        }
    }

    public function getResultMessage(): string
    {
        $message = "Importación completada. {$this->results['success']} estudiantes importados exitosamente.";

        if (! empty($this->results['errors'])) {
            $message .= "\n\nErrores encontrados:\n".implode("\n", array_slice($this->results['errors'], 0, 5));
            if (count($this->results['errors']) > 5) {
                $message .= "\n... y ".(count($this->results['errors']) - 5).' errores más.';
            }
        }

        return $message;
    }
}
