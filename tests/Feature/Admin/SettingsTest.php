<?php

use App\Models\User;
use App\UserRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows admin to view settings edit page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get('/admin/settings');

    $response->assertSuccessful();
    $response->assertViewIs('admin.settings.edit');
});

it('allows admin to upload a logo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $file = UploadedFile::fake()->image('logo.png', 100, 100);

    $response = $this->actingAs($admin)->put('/admin/settings', [
        'logo' => $file,
    ]);

    $response->assertRedirect('/admin/settings');
    $response->assertSessionHas('success');

    Storage::disk('public')->assertExists('logos/'.$file->hashName());
});

it('validates logo upload', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->put('/admin/settings', [
        'logo' => 'not-a-file',
    ]);

    $response->assertSessionHasErrors('logo');
});
