<?php

namespace Database\Seeders;

use App\DayOfWeek;
use App\Gender;
use App\Models\GradeSection;
use App\Models\MonthlyTuition;
use App\Models\PickupPerson;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\Subject;
use App\Models\User;
use App\Relationship;
use App\StudentStatus;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSchoolSeeder extends Seeder
{
    /**
     * Nombres y apellidos realistas en español
     */
    private array $maleFirstNames = [
        'Lucas', 'Mateo', 'Santiago', 'Alejandro', 'Andrés',
        'Carlos', 'Diego', 'Felipe', 'Fernando', 'Francisco',
        'Gabriel', 'Guillermo', 'Héctor', 'Ignacio', 'Javier',
        'Jesús', 'Juan', 'Julio', 'Luis', 'Manuel',
        'Marcos', 'Mario', 'Miguel', 'Nicolás', 'Óscar',
        'Pablo', 'Pedro', 'Rafael', 'Ramón', 'Ricardo',
        'Roberto', 'Rodrigo', 'Rubén', 'Salvador', 'Samuel',
        'Sergio', 'Vicente', 'Víctor', 'Waldo', 'Xavier',
    ];

    private array $femaleFirstNames = [
        'Andrea', 'Ángela', 'Antonia', 'Aurora', 'Bárbara',
        'Beatriz', 'Belén', 'Blanca', 'Brenda', 'Camila',
        'Carlota', 'Carmen', 'Carolina', 'Catalina', 'Cecilia',
        'Claudia', 'Clemencia', 'Concepción', 'Consuelo', 'Cristina',
        'Delia', 'Dolores', 'Dominga', 'Donatila', 'Dorothea',
        'Edith', 'Elisa', 'Elizabeth', 'Eloísa', 'Elsa',
        'Elvira', 'Emilia', 'Emma', 'Enriqueta', 'Enrique',
        'Ester', 'Esther', 'Estela', 'Estelia', 'Estefanía',
    ];

    private array $patternalLastNames = [
        'García', 'Martínez', 'González', 'Rodríguez', 'Hernández',
        'López', 'Pérez', 'Sánchez', 'Ramírez', 'Torres',
        'Flores', 'Rivera', 'Gómez', 'Díaz', 'Cruz',
        'Reyes', 'Morales', 'Jiménez', 'Ruiz', 'Mendoza',
        'Vargas', 'Castro', 'Ortiz', 'Romero', 'Medina',
    ];

    private array $maternalLastNames = [
        'Navarro', 'Gutiérrez', 'Chávez', 'Olvera', 'Fuentes',
        'Salazar', 'Contreras', 'Barrera', 'Estrada', 'Miranda',
        'Aguila', 'Álvarez', 'Arellano', 'Arriaga', 'Arroyo',
        'Avila', 'Ayala', 'Azcona', 'Balderas', 'Barajas',
        'Bárcenas', 'Barona', 'Barrón', 'Basave', 'Basulto',
    ];

    private array $pickupRelationships = [
        Relationship::Grandparent,
        Relationship::Uncle,
        Relationship::Aunt,
        Relationship::Family,
        Relationship::Friend,
        Relationship::Other,
    ];

    private array $schoolGrades = [
        ['grade' => 1, 'level' => 1],
        ['grade' => 2, 'level' => 2],
        ['grade' => 3, 'level' => 3],
        ['grade' => 4, 'level' => 4],
        ['grade' => 5, 'level' => 5],
        ['grade' => 6, 'level' => 6],
    ];

    private array $subjects = [
        'Español',
        'Matemáticas',
        'Ciencias Naturales',
        'Historia y Geografía',
        'Formación Cívica',
        'Educación Física',
        'Inglés',
        'Música',
        'Artes Plásticas',
    ];

    public function run(): void
    {
        // Usar transacción para mantener integridad
        DB::transaction(function () {
            $this->command->info('');
            $this->command->info('╔════════════════════════════════════════════════════════╗');
            $this->command->info('║     Iniciando Seeder Integral de Escuela 2025-2026     ║');
            $this->command->info('╚════════════════════════════════════════════════════════╝');

            // 1. Crear administrador
            $this->command->info('');
            $this->command->info('→ Creando administrador...');
            $this->createAdmin();

            // 2. Respaldar settings actuales
            $this->command->info('→ Respaldando configuración actual...');
            $this->backupCurrentSettings();

            // 3. Crear ciclo escolar 2025-2026
            $this->command->info('→ Creando ciclo escolar 2025-2026...');
            $schoolYear = $this->createSchoolYear();

            // 3. Crear estructura académica (6 grupos, 1 salón cada uno)
            $this->command->info('→ Creando 6 grupos escolares (1º a 6º)...');
            $gradeSections = $this->createGradeSections($schoolYear);

            // 4. Crear maestros
            $this->command->info('→ Creando 6 maestros titulares + 3 maestros especializados...');
            $headTeachers = $this->createHeadTeachers();
            $specializedTeachers = $this->createSpecializedTeachers();
            $allTeachers = array_merge($headTeachers, $specializedTeachers);

            // 5. Crear padres/tutores
            $this->command->info('→ Creando 84 padres/tutores con coherencia familiar...');
            $parents = $this->createParents();

            // 6. Crear alumnos
            $this->command->info('→ Creando 84 alumnos (14 por grupo, 7 niños y 7 niñas)...');
            $students = $this->createStudents($schoolYear, $gradeSections, $parents);

            // 7. Crear personas autorizadas para recoger
            $this->command->info('→ Creando personas autorizadas para recoger estudiantes...');
            $this->createPickupPeople($students);

            // 8. Crear ciclo de cuotas mensuales
            $this->command->info('→ Creando configuración de cuotas mensuales (3,000 pesos)...');
            $monthlyTuitions = $this->createMonthlyTuitions($schoolYear);

            // 9. Crear transacciones de tuiciones con pagos parciales/atrasados
            $this->command->info('→ Generando transacciones de tuiciones y pagos...');
            $this->createTuitionsAndPayments($students, $schoolYear, $monthlyTuitions);

            // 10. Crear asignaturas y asignaciones
            $this->command->info('→ Creando asignaturas y asignaciones por grupo...');
            $this->createSubjectsAndAssignments($schoolYear, $gradeSections, $headTeachers, $specializedTeachers);

            // 11. Crear horarios
            $this->command->info('→ Generando horarios completos (7:30 a.m. - 2:00 p.m.)...');
            $this->createSchedules($gradeSections);

            // Mostrar resumen
            $this->showSummary($schoolYear, $gradeSections, $headTeachers, $specializedTeachers, $parents, $students);
        });
    }

    /**
     * Crea un administrador de la escuela
     */
    private function createAdmin(): void
    {
        $existing = User::where('email', 'admin@escuela.com')->first();

        if ($existing) {
            $this->command->info('   ✓ Administrador ya existe');

            return;
        }

        User::create([
            'name' => 'Administrador',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'Escuela',
            'email' => 'admin@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        $this->command->info('   ✓ Administrador creado: admin@escuela.com');
    }

    /**
     * Respalda la configuración actual de settings
     */
    private function backupCurrentSettings(): void
    {
        $settings = DB::table('settings')->get();
        if ($settings->count() > 0) {
            $this->command->info('   ✓ Configuración actual preservada');
        }
    }

    /**
     * Crea el ciclo escolar 2025-2026
     */
    private function createSchoolYear(): SchoolYear
    {
        // Verificar si ya existe
        $existing = SchoolYear::where('name', '2025-2026')->first();
        if ($existing) {
            return $existing;
        }

        return SchoolYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-08-01',
            'end_date' => '2026-07-31',
            'is_active' => false, // No activar aún, es futuro
        ]);
    }

    /**
     * Crea 6 grupos escolares (1º a 6º), uno por grado
     */
    private function createGradeSections(SchoolYear $schoolYear): array
    {
        $gradeSections = [];

        foreach ($this->schoolGrades as $gradeData) {
            // Solo crear si no existe
            $existing = GradeSection::where('school_year_id', $schoolYear->id)
                ->where('grade_level', $gradeData['grade'])
                ->where('section', 'Única')
                ->first();

            if ($existing) {
                $gradeSections[] = $existing;

                continue;
            }

            $gradeSection = GradeSection::create([
                'school_year_id' => $schoolYear->id,
                'grade_level' => $gradeData['grade'],
                'section' => 'Única', // Una sección por grado
                'break_time_start' => '10:30',
                'break_time_end' => '11:00',
            ]);

            $gradeSections[] = $gradeSection;
        }

        return $gradeSections;
    }

    /**
     * Crea 6 maestros titulares (uno por grado)
     */
    private function createHeadTeachers(): array
    {
        $headTeachers = [];
        $gradeNames = ['Primero', 'Segundo', 'Tercero', 'Cuarto', 'Quinto', 'Sexto'];

        foreach ($gradeNames as $index => $gradeName) {
            $firstName = $this->maleFirstNames[$index];
            $lastName1 = $this->patternalLastNames[$index];
            $lastName2 = $this->maternalLastNames[$index];

            $email = strtolower($firstName[0].$lastName1.'.'.$lastName2.'@escuela.com');

            $existing = User::where('email', $email)->first();
            if ($existing) {
                $headTeachers[] = $existing;

                continue;
            }

            $teacher = User::create([
                'name' => $firstName,
                'apellido_paterno' => $lastName1,
                'apellido_materno' => $lastName2,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => UserRole::Teacher,
            ]);

            $headTeachers[] = $teacher;
        }

        return $headTeachers;
    }

    /**
     * Crea 3 maestros especializados
     */
    private function createSpecializedTeachers(): array
    {
        $specializations = [
            ['name' => 'Educación', 'lastName1' => 'Físico', 'lastName2' => 'Martínez', 'email' => 'profesor.educacion@escuela.com'],
            ['name' => 'Inglés', 'lastName1' => 'Languages', 'lastName2' => 'García', 'email' => 'profesor.ingles@escuela.com'],
            ['name' => 'Música', 'lastName1' => 'Melodía', 'lastName2' => 'Pérez', 'email' => 'profesor.musica@escuela.com'],
        ];

        $teachers = [];

        foreach ($specializations as $spec) {
            $existing = User::where('email', $spec['email'])->first();
            if ($existing) {
                $teachers[] = $existing;

                continue;
            }

            $teacher = User::create([
                'name' => $spec['name'],
                'apellido_paterno' => $spec['lastName1'],
                'apellido_materno' => $spec['lastName2'],
                'email' => $spec['email'],
                'password' => Hash::make('password'),
                'role' => UserRole::Teacher,
            ]);

            $teachers[] = $teacher;
        }

        return $teachers;
    }

    /**
     * Crea 84 padres/tutores con nombres coherentes
     */
    private function createParents(): array
    {
        $parents = [];

        // Generar 84 padres
        for ($i = 0; $i < 84; $i++) {
            $firstName = $this->maleFirstNames[$i % count($this->maleFirstNames)];
            $lastName1 = $this->patternalLastNames[$i % count($this->patternalLastNames)];
            $lastName2 = $this->maternalLastNames[$i % count($this->maternalLastNames)];

            // Email único
            $email = strtolower(
                str_replace('á', 'a', str_replace('é', 'e', str_replace('í', 'i', str_replace('ó', 'o', str_replace('ú', 'u',
                    $firstName[0].$lastName1)))))
                .'.padre'.($i + 1).'@correo.com'
            );

            // Evitar duplicados
            $email = preg_replace('/[^a-z0-9@._-]/', '', $email);

            $existing = User::where('email', $email)->first();
            if ($existing) {
                $parents[] = $existing;

                continue;
            }

            $parent = User::create([
                'name' => $firstName,
                'apellido_paterno' => $lastName1,
                'apellido_materno' => $lastName2,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => UserRole::Parent,
            ]);

            $parents[] = $parent;
        }

        return $parents;
    }

    /**
     * Crea 84 alumnos con coherencia familiar
     */
    private function createStudents(SchoolYear $schoolYear, array $gradeSections, array $parents): array
    {
        $students = [];
        $studentCount = 0;
        $parentIndex = 0;

        foreach ($gradeSections as $gradeSection) {
            // 14 alumnos por grupo: 7 niños y 7 niñas
            for ($i = 0; $i < 14; $i++) {
                $studentCount++;

                // Alternar género: niño, niña, niño, niña...
                $gender = ($i % 2 === 0) ? Gender::Male : Gender::Female;
                $genderKey = $gender === Gender::Male ? 'male' : 'female';

                // Seleccionar nombre basado en género
                $firstName = $genderKey === 'male'
                    ? $this->maleFirstNames[$i % count($this->maleFirstNames)]
                    : $this->femaleFirstNames[$i % count($this->femaleFirstNames)];

                // Usar apellidos del padre para coherencia familiar
                $tutor1 = $parents[$parentIndex % count($parents)];
                $parentIndex++;

                $lastName1 = $tutor1->apellido_paterno;
                $lastName2 = $tutor1->apellido_materno;

                // Generar CURP realista
                $year = 2015 + $gradeSection->grade_level; // Edad aproximada según grado
                $curp = $this->generateCURP($firstName, $lastName1, $lastName2, $year, $gender);

                // Crear usuario del estudiante con email único
                $studentEmail = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'],
                    ['', 'a', 'e', 'i', 'o', 'u'],
                    $firstName.'.'.($studentCount).'.2025@estudiantes.escuela.com'));

                // Verificar si el email ya existe
                $existing = User::where('email', $studentEmail)->first();
                if ($existing) {
                    $studentUser = $existing;
                } else {
                    $studentUser = User::create([
                        'name' => $firstName,
                        'apellido_paterno' => $lastName1,
                        'apellido_materno' => $lastName2,
                        'email' => $studentEmail,
                        'password' => Hash::make('password'),
                        'role' => UserRole::Student,
                        'parent_id' => $tutor1->id,
                    ]);
                }

                // Número de matrícula único con timestamp para evitar duplicados
                $enrollmentNumber = '2025-'.$gradeSection->grade_level.'-'.str_pad($studentCount, 4, '0', STR_PAD_LEFT);

                // Verificar si el estudiante ya existe
                $existingStudent = Student::where('user_id', $studentUser->id)
                    ->where('school_year_id', $schoolYear->id)
                    ->first();

                if ($existingStudent) {
                    $student = $existingStudent;
                } else {
                    // Crear registro de estudiante
                    $student = Student::create([
                        'user_id' => $studentUser->id,
                        'school_year_id' => $schoolYear->id,
                        'school_grade_id' => $gradeSection->id,
                        'enrollment_number' => $enrollmentNumber,
                        'status' => StudentStatus::Active,
                        'curp' => $curp,
                        'date_of_birth' => ($year).'-'.str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT).'-'.str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                        'gender' => $gender,
                        'birth_country' => 'México',
                        'birth_state' => 'Estado de México',
                        'birth_city' => 'Toluca',
                        'phone_number' => '722'.str_pad($studentCount, 7, '0', STR_PAD_LEFT),
                        'address' => 'Calle '.$lastName1.' '.($studentCount).', Toluca',
                        'parent_email' => $tutor1->email,
                        'tutor_1_id' => $tutor1->id,
                        'requires_invoice' => rand(1, 10) <= 2, // 20% requieren factura
                    ]);
                }

                $students[] = $student;
            }
        }

        return $students;
    }

    /**
     * Crea personas autorizadas para recoger estudiantes
     */
    private function createPickupPeople(array $students): void
    {
        foreach ($students as $index => $student) {
            // 70% de estudiantes tendrán una persona autorizada
            if (rand(1, 100) > 70) {
                continue;
            }

            $firstName = $this->maleFirstNames[$index % count($this->maleFirstNames)];
            $lastName1 = $this->patternalLastNames[$index % count($this->patternalLastNames)];

            $relationship = $this->pickupRelationships[rand(0, count($this->pickupRelationships) - 1)];

            PickupPerson::create([
                'student_id' => $student->id,
                'name' => $firstName.' '.$lastName1,
                'relationship' => $relationship->value,
            ]);
        }
    }

    /**
     * Crea configuración de cuotas mensuales (3,000 pesos)
     */
    private function createMonthlyTuitions(SchoolYear $schoolYear): array
    {
        $monthlyTuitions = [];
        $months = ['Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'];

        // Crear una cuota por mes del ciclo escolar
        for ($month = 1; $month <= 12; $month++) {
            $existing = MonthlyTuition::where('school_year_id', $schoolYear->id)
                ->where('month', $month)
                ->first();

            if ($existing) {
                $monthlyTuitions[] = $existing;

                continue;
            }

            $tuition = MonthlyTuition::create([
                'school_year_id' => $schoolYear->id,
                'year' => 2025,
                'month' => $month,
                'amount' => 3000.00, // 3,000 pesos mensuales
            ]);

            $monthlyTuitions[] = $tuition;
        }

        return $monthlyTuitions;
    }

    /**
     * Crea transacciones de tuiciones y pagos parciales/atrasados
     */
    private function createTuitionsAndPayments(array $students, SchoolYear $schoolYear, array $monthlyTuitions): void
    {
        foreach ($students as $student) {
            // Crear tuición para cada mes
            foreach ($monthlyTuitions as $monthlyTuition) {
                // Verificar si ya existe la tuición
                $existing = StudentTuition::where('student_id', $student->id)
                    ->where('school_year_id', $schoolYear->id)
                    ->where('monthly_tuition_id', $monthlyTuition->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $hasLateFee = rand(1, 100) <= 30; // 30% con atraso
                $isPaid = rand(1, 100) <= 85; // 85% pagados

                StudentTuition::create([
                    'student_id' => $student->id,
                    'school_year_id' => $schoolYear->id,
                    'monthly_tuition_id' => $monthlyTuition->id,
                    'year' => $monthlyTuition->year,
                    'month' => $monthlyTuition->month,
                    'monthly_amount' => 3000.00,
                    'discount_percentage' => rand(0, 100) <= 20 ? rand(5, 15) : 0, // 20% con descuento
                    'due_date' => '2025-'.str_pad($monthlyTuition->month, 2, '0', STR_PAD_LEFT).'-10',
                    'late_fee_amount' => $hasLateFee ? 300.00 : 0,
                    'late_fee_paid' => $isPaid && $hasLateFee ? true : false,
                    'notes' => $hasLateFee && ! $isPaid ? 'Pago pendiente con interés moratorio' : null,
                ]);
            }
        }
    }

    /**
     * Crea asignaturas y asignaciones
     */
    private function createSubjectsAndAssignments(
        SchoolYear $schoolYear,
        array $gradeSections,
        array $headTeachers,
        array $specializedTeachers
    ): void {
        foreach ($gradeSections as $index => $gradeSection) {
            // Asignar maestro titular
            $headTeacher = $headTeachers[$index];

            // Asignaturas principales con maestro titular
            foreach (['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia y Geografía', 'Formación Cívica'] as $subjectName) {
                Subject::firstOrCreate(
                    [
                        'name' => $subjectName,
                        'school_year_id' => $schoolYear->id,
                        'grade_section_id' => $gradeSection->id,
                    ],
                    [
                        'teacher_id' => $headTeacher->id,
                        'grade_level' => $gradeSection->grade_level,
                        'default_hours_per_week' => 5,
                    ]
                );
            }

            // Asignaturas especializadas
            $specializations = [
                ['name' => 'Educación Física', 'teacher' => $specializedTeachers[0]],
                ['name' => 'Inglés', 'teacher' => $specializedTeachers[1]],
                ['name' => 'Música', 'teacher' => $specializedTeachers[2]],
            ];

            foreach ($specializations as $spec) {
                Subject::firstOrCreate(
                    [
                        'name' => $spec['name'],
                        'school_year_id' => $schoolYear->id,
                        'grade_section_id' => $gradeSection->id,
                    ],
                    [
                        'teacher_id' => $spec['teacher']->id,
                        'grade_level' => $gradeSection->grade_level,
                        'default_hours_per_week' => 2,
                    ]
                );
            }

            // Artes Plásticas con maestro titular
            Subject::firstOrCreate(
                [
                    'name' => 'Artes Plásticas',
                    'school_year_id' => $schoolYear->id,
                    'grade_section_id' => $gradeSection->id,
                ],
                [
                    'teacher_id' => $headTeacher->id,
                    'grade_level' => $gradeSection->grade_level,
                    'default_hours_per_week' => 2,
                ]
            );
        }
    }

    /**
     * Crea horarios completos
     * 7:30 a.m. - 2:00 p.m.
     * Receso: 10:30 - 11:00 a.m.
     */
    private function createSchedules(array $gradeSections): void
    {
        $timeBlocks = [
            ['start' => '07:30', 'end' => '08:30'],
            ['start' => '08:30', 'end' => '09:30'],
            ['start' => '09:30', 'end' => '10:30'],
            // Receso 10:30 - 11:00
            ['start' => '11:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '01:00'],
            ['start' => '01:00', 'end' => '02:00'],
        ];

        $subjectPriorities = [
            ['Español', 'Matemáticas', 'Ciencias Naturales', 'Historia y Geografía', 'Educación Física'],
            ['Matemáticas', 'Español', 'Historia y Geografía', 'Formación Cívica', 'Inglés'],
            ['Ciencias Naturales', 'Español', 'Matemáticas', 'Música', 'Artes Plásticas'],
            ['Español', 'Matemáticas', 'Inglés', 'Ciencias Naturales', 'Educación Física'],
            ['Matemáticas', 'Historia y Geografía', 'Español', 'Música', 'Formación Cívica'],
            ['Español', 'Ciencias Naturales', 'Educación Física', 'Artes Plásticas', 'Matemáticas'],
        ];

        $days = [
            DayOfWeek::Monday,
            DayOfWeek::Tuesday,
            DayOfWeek::Wednesday,
            DayOfWeek::Thursday,
            DayOfWeek::Friday,
        ];

        foreach ($gradeSections as $gradeIndex => $gradeSection) {
            $subjectIndex = 0;
            $prioritySubjects = $subjectPriorities[$gradeIndex];

            foreach ($days as $dayOfWeek) {
                foreach ($timeBlocks as $block) {
                    $subject = Subject::where('school_year_id', $gradeSection->schoolYear->id)
                        ->where('grade_section_id', $gradeSection->id)
                        ->where('name', $prioritySubjects[$subjectIndex % count($prioritySubjects)])
                        ->first();

                    if (! $subject) {
                        continue;
                    }

                    // Evitar duplicados
                    $existing = DB::table('schedules')->where([
                        'school_grade_id' => $gradeSection->id,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => $block['start'],
                    ])->first();

                    if (! $existing) {
                        DB::table('schedules')->insert([
                            'school_grade_id' => $gradeSection->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $subject->teacher_id,
                            'day_of_week' => $dayOfWeek,
                            'start_time' => $block['start'],
                            'end_time' => $block['end'],
                            'classroom' => $gradeSection->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $subjectIndex++;
                }
            }
        }
    }

    /**
     * Genera un CURP realista (18 caracteres)
     */
    private function generateCURP(string $firstName, string $lastName1, string $lastName2, int $year, $gender): string
    {
        // Normalizar: remover acentos y convertir a mayúsculas
        $normalize = function ($str) {
            $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);

            return strtoupper(preg_replace('/[^A-Z0-9]/', '', $str));
        };

        $ln1 = $normalize($lastName1);
        $ln2 = $normalize($lastName2);
        $fn = $normalize($firstName);

        // Primeras 4 letras: 2 del primer apellido + 2 del segundo
        $part1 = substr($ln1, 0, 2).substr($ln2, 0, 2);

        // Siguiente letra: primera del nombre
        $part2 = substr($fn, 0, 1);

        // Fecha: YYMMDD
        $yearShort = substr((string) $year, 2, 2);
        $date = $yearShort.'0101';

        // Género
        $genderCode = $gender === Gender::Male ? 'H' : 'M';

        // Estado
        $stateCode = 'EM';

        // 2 dígitos aleatorios
        $digits = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

        // Total: 4+1+6+1+2+2+2 = 18 caracteres
        return $part1.$part2.$date.$genderCode.$stateCode.$digits;
    }

    /**
     * Obtiene una consonante de una palabra
     */
    private function getConsonant(string $word, int $position): string
    {
        $cleaned = strtoupper(str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'A', 'E', 'I', 'O', 'U'], '', $word));

        return $cleaned[$position] ?? 'X';
    }

    /**
     * Muestra resumen de lo creado
     */
    private function showSummary(
        SchoolYear $schoolYear,
        array $gradeSections,
        array $headTeachers,
        array $specializedTeachers,
        array $parents,
        array $students
    ): void {
        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════╗');
        $this->command->info('║              RESUMEN DEL SEEDER EJECUTADO              ║');
        $this->command->info('╚════════════════════════════════════════════════════════╝');

        $this->command->info('');
        $this->command->info('📚 CICLO ESCOLAR');
        $this->command->info('   • Ciclo: '.$schoolYear->name);
        $this->command->info('   • Fecha inicio: '.$schoolYear->start_date);
        $this->command->info('   • Fecha fin: '.$schoolYear->end_date);

        $this->command->info('');
        $this->command->info('🏫 ESTRUCTURA ACADÉMICA');
        $this->command->info('   • Grupos escolares: '.count($gradeSections).' (1º a 6º grado)');
        $this->command->info('   • Salones: '.count($gradeSections).' (uno por grupo)');

        $this->command->info('');
        $this->command->info('👨‍🏫 MAESTROS');
        $this->command->info('   • Maestros titulares: '.count($headTeachers));
        $this->command->info('   • Maestros especializados: '.count($specializedTeachers));
        $this->command->info('     - Educación Física');
        $this->command->info('     - Inglés');
        $this->command->info('     - Música');
        $this->command->info('   • Total maestros: '.(count($headTeachers) + count($specializedTeachers)));

        $this->command->info('');
        $this->command->info('👨‍👩‍👧‍👦 ALUMNOS Y FAMILIAS');
        $this->command->info('   • Total alumnos: '.count($students).' (14 por grupo)');
        $this->command->info('   • Distribución por género: 7 niños y 7 niñas por grupo');
        $this->command->info('   • Padres/Tutores: '.count($parents));
        $this->command->info('   • Personas autorizadas para recoger: '.(int) (count($students) * 0.7));

        $this->command->info('');
        $this->command->info('💰 PAGOS Y CUOTAS');
        $this->command->info('   • Cuota mensual: $3,000 MXN');
        $this->command->info('   • Meses: 12 (Agosto 2025 - Julio 2026)');
        $this->command->info('   • Total transacciones de tuición: '.(count($students) * 12));
        $this->command->info('   • Estudiantes con pagos atrasados: ~30%');
        $this->command->info('   • Estudiantes con descuento: ~20%');

        $this->command->info('');
        $this->command->info('📅 HORARIOS');
        $this->command->info('   • Inicio de clases: 07:30 a.m.');
        $this->command->info('   • Fin de clases: 02:00 p.m.');
        $this->command->info('   • Receso: 10:30 - 11:00 a.m. (30 minutos)');
        $this->command->info('   • Bloques horarios: 6 bloques de 1 hora');

        $this->command->info('');
        $this->command->info('✅ DATOS PRESERVADOS');
        $this->command->info('   • Anuncios existentes: conservados');
        $this->command->info('   • Configuración de settings: preservada');

        $this->command->info('');
        $this->command->info('🔐 CREDENCIALES DE PRUEBA (Password: password)');
        $this->command->info('   • Administrador: admin@escuela.com');
        $this->command->info('   • Maestros: '.collect($headTeachers)->first()->email.' y otros');
        $this->command->info('   • Padres: '.collect($parents)->first()->email.' y otros');
        $this->command->info('   • Alumnos: '.collect($students)->first()->user->email.' y otros');

        $this->command->info('');
        $this->command->info('╚════════════════════════════════════════════════════════╝');
        $this->command->info('✨ ¡Seeder ejecutado exitosamente!');
        $this->command->info('');
    }
}
