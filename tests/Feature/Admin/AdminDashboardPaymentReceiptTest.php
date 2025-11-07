<?php

use App\Models\PaymentReceipt;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\ReceiptStatus;
use App\StudentStatus;
use App\UserRole;

beforeEach(function () {
    // Create admin user
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);

    // Create active school year
    $this->schoolYear = SchoolYear::factory()->create([
        'name' => '2025-2026',
        'start_date' => '2025-08-01',
        'end_date' => '2026-07-31',
        'is_active' => true,
    ]);
});

it('shows dashboard with pending payment receipts amount', function () {
    // Create parent and student
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Create a pending payment receipt for this month
    PaymentReceipt::factory()->create([
        'student_id' => $student->id,
        'parent_id' => $parent->id,
        'registered_by_id' => $parent->id,
        'payment_date' => now(),
        'amount_paid' => 2500.00,
        'payment_year' => now()->year,
        'payment_month' => now()->month,
        'status' => ReceiptStatus::Pending,
    ]);

    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewHas('financialStats');

    $stats = $response->viewData('financialStats');
    expect($stats['pending_monthly_payments'])->toEqual(2500.00);
});

it('shows zero pending receipts when none exist', function () {
    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewHas('financialStats');

    $stats = $response->viewData('financialStats');
    expect($stats['pending_monthly_payments'])->toEqual(0);
});

it('does not count validated receipts in pending amount', function () {
    // Create parent and student
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Create a validated receipt
    PaymentReceipt::factory()->create([
        'student_id' => $student->id,
        'parent_id' => $parent->id,
        'registered_by_id' => $parent->id,
        'payment_date' => now(),
        'amount_paid' => 2500.00,
        'payment_year' => now()->year,
        'payment_month' => now()->month,
        'status' => ReceiptStatus::Validated,
        'validated_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewHas('financialStats');

    $stats = $response->viewData('financialStats');
    expect($stats['pending_monthly_payments'])->toEqual(0);
});

it('calculates pending receipts for current month only', function () {
    // Create parent and student
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Create a pending receipt for current month
    PaymentReceipt::factory()->create([
        'student_id' => $student->id,
        'parent_id' => $parent->id,
        'registered_by_id' => $parent->id,
        'payment_date' => now(),
        'amount_paid' => 2500.00,
        'payment_year' => now()->year,
        'payment_month' => now()->month,
        'status' => ReceiptStatus::Pending,
    ]);

    // Create a pending receipt for previous month
    PaymentReceipt::factory()->create([
        'student_id' => $student->id,
        'parent_id' => $parent->id,
        'registered_by_id' => $parent->id,
        'payment_date' => now()->subMonth(),
        'amount_paid' => 2500.00,
        'payment_year' => now()->subMonth()->year,
        'payment_month' => now()->subMonth()->month,
        'status' => ReceiptStatus::Pending,
    ]);

    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewHas('financialStats');

    $stats = $response->viewData('financialStats');
    // Only the current month's pending receipt should be counted
    expect($stats['pending_monthly_payments'])->toEqual(2500.00);
});

it('sums multiple pending receipts for current month', function () {
    // Create parent and student
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $studentUser = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);
    $student = Student::factory()->create([
        'user_id' => $studentUser->id,
        'school_year_id' => $this->schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    // Create 3 pending receipts for current month
    for ($i = 0; $i < 3; $i++) {
        PaymentReceipt::factory()->create([
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'registered_by_id' => $parent->id,
            'payment_date' => now(),
            'amount_paid' => 2500.00,
            'payment_year' => now()->year,
            'payment_month' => now()->month,
            'status' => ReceiptStatus::Pending,
        ]);
    }

    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewHas('financialStats');

    $stats = $response->viewData('financialStats');
    expect($stats['pending_monthly_payments'])->toEqual(7500.00);
});
