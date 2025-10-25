<?php

use App\Models\SchoolGrade;
use App\Models\User;
use App\UserRole;

it('only allows admin users to access filter options', function () {
    $user = User::factory()->create(['role' => UserRole::Teacher]);

    $response = $this->actingAs($user)->get('/api/messages/filter-options?role=teacher');

    $response->assertStatus(403);
    $response->assertJson(['error' => 'Unauthorized']);
});

it('returns filter options for admin user', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/api/messages/filter-options?role=teacher');

    $response->assertSuccessful();
    $response->assertJsonStructure(['filters' => [['type', 'label']]]);
});

it('returns correct filters for teacher role', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/api/messages/filter-options?role=teacher');

    $filters = $response->json('filters');
    $filterTypes = array_column($filters, 'type');

    expect($filterTypes)->toContain('all', 'by_level', 'by_subject', 'by_school_grade', 'individual');
});

it('returns correct filters for parent role', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/api/messages/filter-options?role=parent');

    $filters = $response->json('filters');
    $filterTypes = array_column($filters, 'type');

    expect($filterTypes)->toContain('all', 'by_school_grade', 'by_school_grade_group', 'by_student_name', 'individual');
});

it('returns filter data for teacher by level', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    SchoolGrade::factory()->create(['level' => 1, 'section' => 'A']);
    SchoolGrade::factory()->create(['level' => 2, 'section' => 'A']);

    $response = $this->actingAs($admin)
        ->get('/api/messages/filter-data?role=teacher&filter_type=by_level');

    $response->assertSuccessful();
    $response->assertJsonStructure(['items' => [['id', 'name']]]);
});

it('returns filter data for teacher by subject without duplicates', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $teacher1 = User::factory()->create(['role' => UserRole::Teacher]);
    $teacher2 = User::factory()->create(['role' => UserRole::Teacher]);

    // Create the same subject multiple times (e.g., for different teachers or years)
    \App\Models\Subject::factory()->create(['name' => 'Artes', 'teacher_id' => $teacher1->id]);
    \App\Models\Subject::factory()->create(['name' => 'Artes', 'teacher_id' => $teacher2->id]);
    \App\Models\Subject::factory()->create(['name' => 'Artes', 'teacher_id' => $teacher1->id]);
    \App\Models\Subject::factory()->create(['name' => 'Matemáticas', 'teacher_id' => $teacher1->id]);
    \App\Models\Subject::factory()->create(['name' => 'Matemáticas', 'teacher_id' => $teacher2->id]);

    $response = $this->actingAs($admin)
        ->get('/api/messages/filter-data?role=teacher&filter_type=by_subject');

    $response->assertSuccessful();
    $items = $response->json('items');

    // Check that each subject name appears only once
    $names = array_column($items, 'name');
    $uniqueNames = array_unique($names);

    expect(count($names))->toBe(count($uniqueNames));
    expect($names)->toContain('Artes', 'Matemáticas');
});

it('returns users when fetching with all filter', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    User::factory()->count(3)->create(['role' => UserRole::Teacher]);

    $response = $this->actingAs($admin)
        ->get('/api/messages/users?role=teacher&filter_type=all');

    $response->assertSuccessful();
    $users = $response->json();
    expect(count($users))->toBe(3);
});

it('excludes current admin from users list', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    User::factory()->count(2)->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)
        ->get('/api/messages/users?role=admin&filter_type=all');

    $users = $response->json();
    expect(count($users))->toBe(2);
    expect($users)->not->toContain('id', $admin->id);
});

it('filters users by search query for individual selection', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $garcia = User::factory()->create([
        'role' => UserRole::Teacher,
        'name' => 'Juan',
        'apellido_paterno' => 'García',
        'apellido_materno' => 'test',
    ]);
    User::factory()->create([
        'role' => UserRole::Teacher,
        'name' => 'Pedro',
        'apellido_paterno' => 'López',
        'apellido_materno' => 'test',
    ]);

    $response = $this->actingAs($admin)
        ->get('/api/messages/users?role=teacher&filter_type=individual&search='.urlencode('García'));

    $response->assertSuccessful();
    $users = $response->json();

    expect(count($users))->toBe(1);
    expect($users[0]['id'])->toBe($garcia->id);
    expect($users[0]['name'])->toContain('García'); // full_name includes apellido_paterno
});

it('returns parents by student search', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $student = User::factory()->create(['role' => UserRole::Student, 'parent_id' => $parent->id]);

    $response = $this->actingAs($admin)
        ->get('/api/messages/users?role=parent&filter_type=by_student_name&search='.urlencode($student->name));

    $users = $response->json();
    expect($users)->not->toBeEmpty();
});

it('only allows admin to access user endpoints', function () {
    $teacher = User::factory()->create(['role' => UserRole::Teacher]);

    $response = $this->actingAs($teacher)
        ->get('/api/messages/users?role=teacher&filter_type=all');

    $response->assertStatus(403);
});
