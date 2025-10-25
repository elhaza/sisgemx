<?php

use App\Models\User;
use App\UserRole;

it('allows admin users to access the message create form', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/messages/create');

    $response->assertSuccessful()
        ->assertSee('Redactar Mensaje')
        ->assertSee('Enviar a (Rol)');
});

it('allows non-admin users to access the message create form', function () {
    $teacher = User::factory()->create(['role' => UserRole::Teacher]);

    $response = $this->actingAs($teacher)->get('/messages/create');

    $response->assertSuccessful()
        ->assertSee('Redactar Mensaje')
        ->assertSee('Para');
});

it('requires authentication to access message create form', function () {
    $response = $this->get('/messages/create');

    $response->assertRedirectToRoute('login');
});
