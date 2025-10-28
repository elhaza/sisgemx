<?php

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\UserRole;

test('payment details endpoint requires authentication', function () {
    $schoolYear = SchoolYear::factory()->create(['is_active' => true]);
    $user = User::factory()->create();
    $student = Student::factory()->create([
        'school_year_id' => $schoolYear->id,
        'user_id' => $user->id,
    ]);

    $tuition = StudentTuition::create([
        'student_id' => $student->id,
        'school_year_id' => $schoolYear->id,
        'year' => now()->year,
        'month' => now()->month,
        'monthly_amount' => 1000,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'final_amount' => 1000,
        'due_date' => now()->addDay(),
        'late_fee_amount' => 0,
        'late_fee_paid' => 0,
    ]);

    $response = $this->getJson(
        route('admin.students.tuition-details', [$student, $tuition])
    );

    $response->assertUnauthorized();
});

test('payment details endpoint requires admin role', function () {
    $schoolYear = SchoolYear::factory()->create(['is_active' => true]);
    $user = User::factory()->create(['role' => UserRole::Teacher]);
    $student = Student::factory()->create([
        'school_year_id' => $schoolYear->id,
        'user_id' => User::factory()->create()->id,
    ]);

    $tuition = StudentTuition::create([
        'student_id' => $student->id,
        'school_year_id' => $schoolYear->id,
        'year' => now()->year,
        'month' => now()->month,
        'monthly_amount' => 1000,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'final_amount' => 1000,
        'due_date' => now()->addDay(),
        'late_fee_amount' => 0,
        'late_fee_paid' => 0,
    ]);

    $response = $this->actingAs($user)->getJson(
        route('admin.students.tuition-details', [$student, $tuition])
    );

    $response->assertForbidden();
});

test('grace period days not included in explanation when it is 0', function () {
    $schoolYear = SchoolYear::factory()->create(['is_active' => true]);
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = Student::factory()->create([
        'school_year_id' => $schoolYear->id,
        'user_id' => User::factory()->create()->id,
    ]);

    // Create an overdue tuition (30 days late)
    $tuition = StudentTuition::create([
        'student_id' => $student->id,
        'school_year_id' => $schoolYear->id,
        'year' => now()->year,
        'month' => now()->subMonths(1)->month,
        'monthly_amount' => 1000,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'final_amount' => 1000,
        'due_date' => now()->subDays(30),
        'late_fee_amount' => 1500,
        'late_fee_paid' => 0,
    ]);

    // With grace period = 0, the explanation should not have grace_period_days
    $response = $this->actingAs($admin)->getJson(
        route('admin.students.tuition-details', [$student, $tuition])
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.late_fee_details.explanation.days_late', 30)
        ->assertJsonMissing(['grace_period_days' => 0]);
});

test('late fee calculation is consistent between explanation and total', function () {
    $schoolYear = SchoolYear::factory()->create(['is_active' => true]);
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = Student::factory()->create([
        'school_year_id' => $schoolYear->id,
        'user_id' => User::factory()->create()->id,
    ]);

    // Create an overdue tuition with DAILY fees: 30 days late = 30 * $50 = $1500
    $tuition = StudentTuition::create([
        'student_id' => $student->id,
        'school_year_id' => $schoolYear->id,
        'year' => now()->year,
        'month' => now()->subMonths(1)->month,
        'monthly_amount' => 1000,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'final_amount' => 1000,
        'due_date' => now()->subDays(30),
        'late_fee_amount' => 1800,  // Old value that might be different
        'late_fee_paid' => 0,
    ]);

    $response = $this->actingAs($admin)->getJson(
        route('admin.students.tuition-details', [$student, $tuition])
    );

    // The calculated late fee should match the summary
    $response->assertSuccessful()
        ->assertJsonPath('data.late_fee_details.amount', 1500)
        ->assertJsonPath('data.payment_summary.late_fees', 1500)
        ->assertJsonPath('data.payment_summary.total_due', 2500);
});

test('student tuition calculated late fee attribute works correctly', function () {
    $schoolYear = SchoolYear::factory()->create(['is_active' => true]);
    $user = User::factory()->create();
    $student = Student::factory()->create([
        'school_year_id' => $schoolYear->id,
        'user_id' => $user->id,
    ]);

    // Create an overdue tuition: 30 days late = 30 * $50 = $1500
    $tuition = StudentTuition::create([
        'student_id' => $student->id,
        'school_year_id' => $schoolYear->id,
        'year' => now()->year,
        'month' => now()->subMonths(1)->month,
        'monthly_amount' => 1000,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'final_amount' => 1000,
        'due_date' => now()->subDays(30),
        'late_fee_amount' => 1800,  // Old value in database
        'late_fee_paid' => 0,
    ]);

    // The calculated_late_fee_amount should be dynamically calculated
    expect($tuition->calculated_late_fee_amount)->toBe(1500.0)
        ->and($tuition->calculated_total_amount)->toBe(2500.0);
});
