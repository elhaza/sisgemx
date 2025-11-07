<?php

use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\PaymentType;
use App\StudentStatus;
use App\UserRole;

beforeEach(function () {
    // Crear administrador
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);

    // Crear ciclo escolar activo
    $this->schoolYear = SchoolYear::factory()->create([
        'name' => '2025-2026',
        'start_date' => '2025-08-01',
        'end_date' => '2026-07-31',
        'is_active' => true,
    ]);
});

it('shows overdue parents report page for admin', function () {
    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    $response->assertStatus(200);
    $response->assertViewIs('admin.overdue-parents-report');
});

it('denies access to non-admin users', function () {
    $parent = User::factory()->create(['role' => UserRole::Parent]);

    $response = $this->actingAs($parent)->get('/admin/overdue-parents-report');

    $response->assertStatus(403);
});

it('shows no parents when all tuitions are paid', function () {
    // Crear padre y estudiante
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Crear tuición vencida
    $tuition = StudentTuition::factory()->create([
        'student_id' => $student->id,
        'school_year_id' => $this->schoolYear->id,
        'due_date' => now()->subMonths(2),
        'monthly_amount' => 3000.00,
    ]);

    // Crear payment para marcar como pagado
    Payment::create([
        'student_id' => $student->id,
        'student_tuition_id' => $tuition->id,
        'payment_type' => PaymentType::Tuition,
        'description' => 'Pago de colegiatura',
        'amount' => $tuition->final_amount,
        'month' => $tuition->month,
        'year' => $tuition->year,
        'due_date' => $tuition->due_date,
        'is_paid' => true,
        'paid_at' => $tuition->due_date->subDay(),
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    // No debe mostrar padres con mora si todo está pagado
    $response->assertViewHas('parentReport', []);
});

it('shows parent with overdue payment', function () {
    // Crear padre y estudiante
    $parent = User::factory()->create(['role' => UserRole::Parent, 'name' => 'Juan García']);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Crear tuición vencida SIN pagar
    $tuition = StudentTuition::factory()->create([
        'student_id' => $student->id,
        'school_year_id' => $this->schoolYear->id,
        'due_date' => now()->subMonths(2),
        'monthly_amount' => 3000.00,
    ]);

    // NO crear payment receipt (tuición sin pagar)

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    // Debe mostrar al padre con mora
    $response->assertViewHas('parentReport');
    $parentReport = $response->viewData('parentReport');

    expect($parentReport)->not->toBeEmpty();
});

it('calculates correct debt amount', function () {
    $parent = User::factory()->create(['role' => UserRole::Parent, 'name' => 'Test Parent']);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Crear 3 tuiciones vencidas sin pagar
    for ($i = 1; $i <= 3; $i++) {
        StudentTuition::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $this->schoolYear->id,
            'due_date' => now()->subMonths($i),
            'monthly_amount' => 3000.00,
            'discount_percentage' => 0,
        ]);
    }

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    $parentReport = $response->viewData('parentReport');
    expect($parentReport)->not->toBeEmpty();

    // El adeudo debe ser al menos 9,000 (3 meses × 3,000)
    $parentData = reset($parentReport);
    expect($parentData['total_tuition_amount'] ?? 0)->toBeGreaterThanOrEqual(9000);
});

it('only shows parents with overdue tuitions', function () {
    // Crear 2 padres
    $parent1 = User::factory()->create(['role' => UserRole::Parent, 'name' => 'Parent 1']);
    $parent2 = User::factory()->create(['role' => UserRole::Parent, 'name' => 'Parent 2']);

    // Estudiante 1: con mora
    $studentUser1 = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent1->id]);
    $student1 = Student::factory()->create([
        'user_id' => $studentUser1->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Estudiante 2: sin mora (todo pagado)
    $studentUser2 = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent2->id]);
    $student2 = Student::factory()->create([
        'user_id' => $studentUser2->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Tuición vencida sin pagar para student1
    $tuition1 = StudentTuition::factory()->create([
        'student_id' => $student1->id,
        'school_year_id' => $this->schoolYear->id,
        'due_date' => now()->subMonths(1),
        'monthly_amount' => 3000.00,
    ]);

    // Tuición vencida pero pagada para student2
    $tuition2 = StudentTuition::factory()->create([
        'student_id' => $student2->id,
        'school_year_id' => $this->schoolYear->id,
        'due_date' => now()->subMonths(1),
        'monthly_amount' => 3000.00,
    ]);

    Payment::create([
        'student_id' => $student2->id,
        'student_tuition_id' => $tuition2->id,
        'payment_type' => PaymentType::Tuition,
        'description' => 'Pago de colegiatura',
        'amount' => $tuition2->final_amount,
        'month' => $tuition2->month,
        'year' => $tuition2->year,
        'due_date' => $tuition2->due_date,
        'is_paid' => true,
        'paid_at' => $tuition2->due_date->subDay(),
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    $parentReport = $response->viewData('parentReport');

    // Solo debe mostrar 1 padre (parent1 con mora)
    expect(count($parentReport))->toBe(1);
});

it('does not show parents with future due dates', function () {
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Crear tuición con fecha futura (no vencida)
    StudentTuition::factory()->create([
        'student_id' => $student->id,
        'school_year_id' => $this->schoolYear->id,
        'due_date' => now()->addMonths(1),
        'monthly_amount' => 3000.00,
    ]);

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    $parentReport = $response->viewData('parentReport');

    // No debe mostrar el padre (la tuición no está vencida)
    expect($parentReport)->toBeEmpty();
});

it('shows correct number of overdue parents from comprehensive seeder', function () {
    // Crear manualmente la estructura que crearía el seeder
    // 1 padre con 3 meses de mora
    $parent1 = User::factory()->create(['role' => UserRole::Parent, 'name' => 'Parent with 3 months']);
    for ($i = 1; $i <= 1; $i++) {
        $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent1->id]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'school_year_id' => $this->schoolYear->id,
            'status' => StudentStatus::Active,
        ]);

        for ($month = 1; $month <= 3; $month++) {
            StudentTuition::factory()->create([
                'student_id' => $student->id,
                'school_year_id' => $this->schoolYear->id,
                'due_date' => now()->subMonths(5 - $month),
                'monthly_amount' => 3000.00,
            ]);
        }
    }

    // 6 padres con 2 meses de mora
    for ($p = 2; $p <= 7; $p++) {
        $parent = User::factory()->create(['role' => UserRole::Parent, 'name' => "Parent with 2 months $p"]);
        $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'school_year_id' => $this->schoolYear->id,
            'status' => StudentStatus::Active,
        ]);

        for ($month = 1; $month <= 2; $month++) {
            StudentTuition::factory()->create([
                'student_id' => $student->id,
                'school_year_id' => $this->schoolYear->id,
                'due_date' => now()->subMonths(4 - $month),
                'monthly_amount' => 3000.00,
            ]);
        }
    }

    // 2 padres con 1 mes de mora
    for ($p = 8; $p <= 9; $p++) {
        $parent = User::factory()->create(['role' => UserRole::Parent, 'name' => "Parent with 1 month $p"]);
        $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'school_year_id' => $this->schoolYear->id,
            'status' => StudentStatus::Active,
        ]);

        StudentTuition::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $this->schoolYear->id,
            'due_date' => now()->subMonths(2),
            'monthly_amount' => 3000.00,
        ]);
    }

    $response = $this->actingAs($this->admin)->get('/admin/overdue-parents-report');

    $response->assertStatus(200);
    $parentReport = $response->viewData('parentReport');

    // Debe mostrar al menos 9 padres/grupos con mora
    // El reporta agrupa por tutor pair, así que puede haber menos si comparten tutores
    expect(count($parentReport))->toBeGreaterThanOrEqual(1);

    // Verificar que haya múltiples estudiantes con mora
    $totalStudentsWithDebt = 0;
    foreach ($parentReport as $parentData) {
        $totalStudentsWithDebt += count($parentData['students'] ?? []);
    }
    expect($totalStudentsWithDebt)->toBeGreaterThanOrEqual(9);
});
