<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Discount;
use App\Models\Grade;
use App\Models\MedicalJustification;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\PaymentReceiptStatusLog;
use App\Models\Schedule;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\Subject;
use App\Models\TuitionConfig;
use App\Models\User;
use App\PaymentMethod;
use App\PaymentType;
use App\ReceiptStatus;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear ciclo escolar activo
        $schoolYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-07-31',
            'is_active' => true,
        ]);

        // Crear grados escolares
        $grade6A = SchoolGrade::create([
            'level' => 6,
            'name' => 'Sexto',
            'section' => 'A',
            'school_year_id' => $schoolYear->id,
        ]);

        $grade6B = SchoolGrade::create([
            'level' => 6,
            'name' => 'Sexto',
            'section' => 'B',
            'school_year_id' => $schoolYear->id,
        ]);

        // Crear usuarios con diferentes roles
        $admin = User::create([
            'name' => 'Administrador General',
            'email' => 'admin@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        $financeAdmin = User::create([
            'name' => 'Juan Pérez - Finanzas',
            'email' => 'finanzas@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::FinanceAdmin,
        ]);

        // Crear maestros
        $teacher1 = User::create([
            'name' => 'María García - Matemáticas',
            'email' => 'maria.garcia@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Teacher,
        ]);

        $teacher2 = User::create([
            'name' => 'Carlos López - Español',
            'email' => 'carlos.lopez@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Teacher,
        ]);

        // Crear padres de familia
        $parent1 = User::create([
            'name' => 'Roberto Martínez',
            'email' => 'roberto.martinez@correo.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Parent,
        ]);

        $parent2 = User::create([
            'name' => 'Ana Rodríguez',
            'email' => 'ana.rodriguez@correo.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Parent,
        ]);

        // Crear estudiantes vinculados a padres
        $studentUser1 = User::create([
            'name' => 'Pedro Martínez',
            'email' => 'pedro.martinez@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
            'parent_id' => $parent1->id,
        ]);

        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'school_year_id' => $schoolYear->id,
            'school_grade_id' => $grade6A->id,
            'enrollment_number' => 'EST-2024-001',
            'grade_level' => '6to',
            'group' => 'A',
        ]);

        $studentUser2 = User::create([
            'name' => 'Laura Rodríguez',
            'email' => 'laura.rodriguez@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
            'parent_id' => $parent2->id,
        ]);

        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'school_year_id' => $schoolYear->id,
            'school_grade_id' => $grade6A->id,
            'enrollment_number' => 'EST-2024-002',
            'grade_level' => '6to',
            'group' => 'A',
        ]);

        // Crear materias
        $mathSubject = Subject::create([
            'name' => 'Matemáticas',
            'description' => 'Curso de matemáticas para 6to grado',
            'teacher_id' => $teacher1->id,
            'grade_level' => '6to',
            'school_year_id' => $schoolYear->id,
        ]);

        $spanishSubject = Subject::create([
            'name' => 'Español',
            'description' => 'Curso de español para 6to grado',
            'teacher_id' => $teacher2->id,
            'grade_level' => '6to',
            'school_year_id' => $schoolYear->id,
        ]);

        // Crear horarios
        Schedule::create([
            'subject_id' => $mathSubject->id,
            'grade_level' => '6to',
            'group' => 'A',
            'day_of_week' => 'monday',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'classroom' => 'Aula 101',
        ]);

        Schedule::create([
            'subject_id' => $spanishSubject->id,
            'grade_level' => '6to',
            'group' => 'A',
            'day_of_week' => 'tuesday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'classroom' => 'Aula 102',
        ]);

        // Configurar monto general de colegiatura por ciclo escolar
        $tuitionConfig = TuitionConfig::create([
            'school_year_id' => $schoolYear->id,
            'amount' => 3000.00,
        ]);

        // Asignar colegiatura por defecto a los estudiantes
        StudentTuition::create([
            'student_id' => $student1->id,
            'school_year_id' => $schoolYear->id,
            'monthly_amount' => 3000.00,
            'notes' => 'Monto estándar',
        ]);

        StudentTuition::create([
            'student_id' => $student2->id,
            'school_year_id' => $schoolYear->id,
            'monthly_amount' => 2700.00,
            'notes' => 'Descuento del 10% aplicado por beca académica',
        ]);

        // Crear pagos para estudiante 1
        $payment1 = Payment::create([
            'student_id' => $student1->id,
            'payment_type' => PaymentType::Tuition,
            'amount' => 3000.00,
            'month' => 9,
            'year' => 2024,
            'due_date' => '2024-09-05',
            'is_paid' => false,
        ]);

        $payment2 = Payment::create([
            'student_id' => $student1->id,
            'payment_type' => PaymentType::Books,
            'description' => 'Paquete de libros ciclo 2024-2025',
            'amount' => 1500.00,
            'year' => 2024,
            'due_date' => '2024-08-20',
            'is_paid' => true,
            'paid_at' => '2024-08-18',
        ]);

        // Crear comprobante de pago pendiente
        $receipt = PaymentReceipt::create([
            'student_id' => $student1->id,
            'parent_id' => $parent1->id,
            'registered_by_id' => $parent1->id,
            'payment_date' => '2024-09-03',
            'amount_paid' => 3000.00,
            'reference' => 'TRANSF-20240903-1234',
            'account_holder_name' => 'Roberto Martínez',
            'issuing_bank' => 'Banco Nacional',
            'payment_method' => PaymentMethod::Transfer,
            'receipt_image' => 'payment-receipts/comprobante-001.jpg',
            'status' => ReceiptStatus::Pending,
        ]);

        // Registrar el log de creación del comprobante
        PaymentReceiptStatusLog::create([
            'payment_receipt_id' => $receipt->id,
            'changed_by_id' => $parent1->id,
            'previous_status' => null,
            'new_status' => ReceiptStatus::Pending,
            'notes' => 'Comprobante creado por el padre de familia',
        ]);

        // Crear descuento especial
        Discount::create([
            'student_id' => $student2->id,
            'school_year_id' => $schoolYear->id,
            'discount_percentage' => 10.00,
            'reason' => 'Beca por rendimiento académico',
            'created_by' => $financeAdmin->id,
        ]);

        // Crear calificaciones
        Grade::create([
            'student_id' => $student1->id,
            'subject_id' => $mathSubject->id,
            'period' => 'Primer Bimestre',
            'grade' => 95.50,
            'comments' => 'Excelente participación y desempeño',
            'teacher_id' => $teacher1->id,
        ]);

        Grade::create([
            'student_id' => $student1->id,
            'subject_id' => $spanishSubject->id,
            'period' => 'Primer Bimestre',
            'grade' => 88.00,
            'teacher_id' => $teacher2->id,
        ]);

        // Crear tareas
        Assignment::create([
            'subject_id' => $mathSubject->id,
            'teacher_id' => $teacher1->id,
            'title' => 'Ejercicios de fracciones',
            'description' => 'Resolver los ejercicios 1-20 del libro página 45',
            'due_date' => now()->addDays(5),
            'max_points' => 100,
        ]);

        Assignment::create([
            'subject_id' => $spanishSubject->id,
            'teacher_id' => $teacher2->id,
            'title' => 'Ensayo sobre la lectura',
            'description' => 'Escribir un ensayo de 2 páginas sobre el libro leído',
            'due_date' => now()->addDays(7),
            'max_points' => 100,
        ]);

        // Crear anuncios
        Announcement::create([
            'teacher_id' => $teacher1->id,
            'title' => 'Examen de matemáticas próxima semana',
            'content' => 'Les informo que el examen del primer bimestre se realizará el próximo viernes. Repasen los temas vistos en clase.',
            'target_audience' => ['students', 'parents'],
        ]);

        Announcement::create([
            'teacher_id' => $teacher2->id,
            'title' => 'Actividad de lectura',
            'content' => 'Recordatorio: traer el libro de lectura para la actividad del martes.',
            'target_audience' => ['students'],
        ]);

        // Crear justificante médico
        MedicalJustification::create([
            'student_id' => $student2->id,
            'parent_id' => $parent2->id,
            'absence_date' => now()->subDays(2),
            'reason' => 'Consulta médica por gripe',
            'document_file_path' => 'medical/justificante-001.pdf',
        ]);

        $this->command->info('Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('Usuarios de prueba:');
        $this->command->info('Admin: admin@escuela.com / password');
        $this->command->info('Finanzas: finanzas@escuela.com / password');
        $this->command->info('Maestro 1: maria.garcia@escuela.com / password');
        $this->command->info('Maestro 2: carlos.lopez@escuela.com / password');
        $this->command->info('Padre 1: roberto.martinez@correo.com / password');
        $this->command->info('Padre 2: ana.rodriguez@correo.com / password');
        $this->command->info('Estudiante 1: pedro.martinez@escuela.com / password');
        $this->command->info('Estudiante 2: laura.rodriguez@escuela.com / password');
    }
}
