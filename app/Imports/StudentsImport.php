<?php

namespace App\Imports;

use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected SchoolYear $schoolYear;

    protected array $results = [
        'success' => 0,
        'errors' => [],
    ];

    protected array $schoolGradeCache = [];

    protected array $userCache = [];

    protected int $nextEnrollmentNumber = 0;

    protected array $usersToCreate = [];

    protected array $studentsToCreate = [];

    protected array $tuitionsToCreate = [];

    public function __construct(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
        $this->initializeEnrollmentNumber();
    }

    public function collection(Collection $collection): void
    {
        // Pre-load existing users by email to reduce queries
        $existingEmails = User::pluck('id', 'email')->toArray();
        $this->userCache = $existingEmails;

        // First pass: Prepare all data
        $rowsData = [];
        foreach ($collection as $row) {
            try {
                $rowsData[] = $this->prepareRowData($row);
            } catch (\Exception $e) {
                $this->results['errors'][] = 'Fila: '.$e->getMessage();
            }
        }

        // Bulk insert all users at once
        if (! empty($this->usersToCreate)) {
            User::insert($this->usersToCreate);
            // Reload cache with newly created users
            $this->userCache = User::pluck('id', 'email')->toArray();
        }

        // Second pass: Create students with resolved user IDs
        foreach ($rowsData as $data) {
            try {
                $this->createStudent($data);
            } catch (\Exception $e) {
                $this->results['errors'][] = 'Fila: '.$e->getMessage();
            }
        }

        // Bulk insert all students at once
        if (! empty($this->studentsToCreate)) {
            Student::insert($this->studentsToCreate);
        }

        // Bulk insert all tuitions at once
        if (! empty($this->tuitionsToCreate)) {
            StudentTuition::insert($this->tuitionsToCreate);
        }
    }

    protected function prepareRowData($row): array
    {
        // Validate required fields
        $this->validateRequiredFields($row);

        // Get or create school grade (cached)
        $level = (int) trim($row['grado']);
        $section = strtoupper(trim($row['seccion']));
        $gradeKey = "{$level}_{$section}";

        if (! isset($this->schoolGradeCache[$gradeKey])) {
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
            $this->schoolGradeCache[$gradeKey] = $schoolGrade->id;
        }

        // Prepare student user
        $studentEmail = trim($row['correo_estudiante']);
        if (! isset($this->userCache[$studentEmail])) {
            $this->usersToCreate[] = [
                'name' => trim($row['nombre_estudiante']),
                'email' => $studentEmail,
                'apellido_paterno' => trim($row['apellido_paterno_estudiante']),
                'apellido_materno' => trim($row['apellido_materno_estudiante']),
                'password' => bcrypt($row['contrasena'] ?? 'sisgemx123'),
                'role' => UserRole::Student->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Prepare parent user
        $parentEmail = trim($row['correo_padres']);
        if (! isset($this->userCache[$parentEmail])) {
            $this->usersToCreate[] = [
                'name' => trim($row['nombre_padre']),
                'email' => $parentEmail,
                'apellido_paterno' => trim($row['apellido_paterno_padre']),
                'apellido_materno' => trim($row['apellido_materno_padre']),
                'password' => bcrypt('sisgemx123'),
                'role' => UserRole::Parent->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return [
            'row' => $row,
            'studentEmail' => $studentEmail,
            'parentEmail' => $parentEmail,
            'schoolGradeId' => $this->schoolGradeCache[$gradeKey],
            'enrollmentNumber' => trim($row['matricula'] ?? '') ?: $this->getNextEnrollmentNumber(),
        ];
    }

    protected function createStudent(array $data): void
    {
        $row = $data['row'];
        $studentUserId = $this->userCache[$data['studentEmail']];
        $parentUserId = $this->userCache[$data['parentEmail']];

        // Prepare student record for bulk insert
        $this->studentsToCreate[] = [
            'user_id' => $studentUserId,
            'school_year_id' => $this->schoolYear->id,
            'school_grade_id' => $data['schoolGradeId'],
            'tutor_1_id' => $parentUserId,
            'tutor_2_id' => null,
            'enrollment_number' => $data['enrollmentNumber'],
            'curp' => trim($row['curp']),
            'date_of_birth' => Carbon::createFromFormat('d/m/Y', trim($row['fecha_nacimiento']))->format('Y-m-d'),
            'gender' => $this->mapGender($row['sexo']),
            'birth_country' => trim($row['pais_nacimiento']),
            'birth_state' => trim($row['estado_nacimiento']),
            'birth_city' => trim($row['ciudad_nacimiento']),
            'phone_number' => trim($row['telefono']),
            'address' => trim($row['domicilio']),
            'parent_email' => $data['parentEmail'],
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Prepare tuition records
        $this->prepareTuitions($studentUserId, $data['enrollmentNumber']);
    }

    protected function prepareTuitions(int $studentId, string $enrollmentNumber): void
    {
        $startDate = $this->schoolYear->start_date;
        $endDate = $this->schoolYear->end_date;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $this->tuitionsToCreate[] = [
                'student_id' => $studentId,
                'school_year_id' => $this->schoolYear->id,
                'year' => $currentDate->year,
                'month' => $currentDate->month,
                'monthly_amount' => 0,
                'discount_percentage' => 0,
                'final_amount' => 0,
                'due_date' => $currentDate->clone()->addDay(10)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $currentDate->addMonth();
        }
    }

    protected function initializeEnrollmentNumber(): void
    {
        $lastStudent = Student::where('school_year_id', $this->schoolYear->id)
            ->orderBy('enrollment_number', 'desc')
            ->first();

        if (! $lastStudent) {
            $this->nextEnrollmentNumber = (int) ($this->schoolYear->start_date->format('Y').'001');
        } else {
            $lastNumber = (int) substr($lastStudent->enrollment_number, -3);
            $this->nextEnrollmentNumber = (int) ($this->schoolYear->start_date->format('Y').str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT));
        }
    }

    protected function getNextEnrollmentNumber(): string
    {
        return (string) $this->nextEnrollmentNumber++;
    }

    protected function mapGender(string $sexo): string
    {
        $sexo = strtolower(trim($sexo));

        return match (true) {
            in_array($sexo, ['m', 'masculino', 'male', 'hombre']) => 'male',
            in_array($sexo, ['f', 'femenino', 'female', 'mujer']) => 'female',
            default => 'unspecified',
        };
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
