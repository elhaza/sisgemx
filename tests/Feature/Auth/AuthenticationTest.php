<?php

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\StudentStatus;
use App\UserRole;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('student cannot login if not active', function () {
    $schoolYear = SchoolYear::factory()->create();
    $user = User::factory()->create(['role' => UserRole::Student]);
    Student::factory()->create([
        'user_id' => $user->id,
        'school_year_id' => $schoolYear->id,
        'status' => StudentStatus::Graduated,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('student can login if active', function () {
    $schoolYear = SchoolYear::factory()->create();
    $user = User::factory()->create(['role' => UserRole::Student]);
    Student::factory()->create([
        'user_id' => $user->id,
        'school_year_id' => $schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('parent cannot login if they do not have active children', function () {
    $schoolYear = SchoolYear::factory()->create();
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $child = User::factory()->create(['parent_id' => $parent->id, 'role' => UserRole::Student]);
    Student::factory()->create([
        'user_id' => $child->id,
        'school_year_id' => $schoolYear->id,
        'status' => StudentStatus::Graduated,
    ]);

    $this->post('/login', [
        'email' => $parent->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('parent can login if they have at least one active child', function () {
    $schoolYear = SchoolYear::factory()->create();
    $parent = User::factory()->create(['role' => UserRole::Parent]);
    $child = User::factory()->create(['parent_id' => $parent->id, 'role' => UserRole::Student]);
    Student::factory()->create([
        'user_id' => $child->id,
        'school_year_id' => $schoolYear->id,
        'status' => StudentStatus::Active,
    ]);

    $response = $this->post('/login', [
        'email' => $parent->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
