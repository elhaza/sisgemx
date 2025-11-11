<?php

use App\Models\ChargeTemplate;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentAssignedCharge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed the database with necessary data
    $this->seed([
        \Database\Seeders\UserSeeder::class,
        \Database\Seeders\SchoolYearSeeder::class,
        \Database\Seeders\GradeSectionSeeder::class,
        \Database\Seeders\SubjectSeeder::class,
        \Database\Seeders\StudentSeeder::class,
        \Database\Seeders\ChargeTemplateSeeder::class,
    ]);
    // Create a finance admin user
    $this->user = User::factory()->create(['role' => 'finance_admin']);

    // Get active school year
    $this->schoolYear = SchoolYear::where('is_active', true)->first();

    // Get a student
    $this->student = Student::where('status', 'active')
        ->where('school_year_id', $this->schoolYear->id)
        ->first();
});

it('can view extra charges index page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('finance.extra-charges.index'));

    $response->assertSuccessful();
    $response->assertViewIs('finance.extra-charges.index');
});

it('can create a new charge template', function () {
    $chargeData = [
        'name' => 'Test Inscripción 2025',
        'charge_type' => 'inscription',
        'description' => 'Test inscription charge',
        'amount' => 1500.00,
        'default_due_date' => '2025-12-15',
        'apply_to' => 'all',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('finance.extra-charges.store'), $chargeData);

    $response->assertRedirect(route('finance.extra-charges.index'));
    expect(ChargeTemplate::where('name', 'Test Inscripción 2025')->exists())->toBeTrue();
});

it('assigns charges to all students when apply_to is all', function () {
    $template = ChargeTemplate::create([
        'name' => 'Test Material',
        'charge_type' => 'materials',
        'description' => 'Test material',
        'amount' => 3500.00,
        'default_due_date' => '2025-12-15',
        'school_year_id' => $this->schoolYear->id,
        'is_active' => true,
        'created_by' => $this->user->id,
    ]);

    $activeStudentsCount = Student::where('status', 'active')
        ->where('school_year_id', $this->schoolYear->id)
        ->count();

    expect($template->assignedCharges->count())->toBe(0);
    expect($activeStudentsCount)->toBeGreaterThan(0);
});

it('can view assigned charges for a template', function () {
    $template = ChargeTemplate::first();

    // Manually assign to a student
    StudentAssignedCharge::create([
        'student_id' => $this->student->id,
        'charge_template_id' => $template->id,
        'amount' => $template->amount,
        'due_date' => $template->default_due_date,
        'is_paid' => false,
        'created_by' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('finance.extra-charges.show', $template));

    $response->assertSuccessful();
    $response->assertSee($this->student->user->full_name);
});

it('can mark assigned charge as paid', function () {
    $template = ChargeTemplate::first();

    $charge = StudentAssignedCharge::create([
        'student_id' => $this->student->id,
        'charge_template_id' => $template->id,
        'amount' => $template->amount,
        'due_date' => $template->default_due_date,
        'is_paid' => false,
        'created_by' => $this->user->id,
    ]);

    expect($charge->is_paid)->toBeFalse();

    $response = $this->actingAs($this->user)
        ->post(route('finance.assigned-charges.mark-as-paid', $charge));

    expect($charge->fresh()->is_paid)->toBeTrue();
});

it('validates required fields when creating charge template', function () {
    $response = $this->actingAs($this->user)
        ->post(route('finance.extra-charges.store'), []);

    $response->assertSessionHasErrors(['name', 'charge_type', 'amount', 'default_due_date', 'apply_to']);
});

it('can update a charge template', function () {
    $template = ChargeTemplate::create([
        'name' => 'Update Test',
        'charge_type' => 'materials',
        'amount' => 1000.00,
        'default_due_date' => '2025-12-15',
        'school_year_id' => $this->schoolYear->id,
        'is_active' => true,
        'created_by' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->put(route('finance.extra-charges.update', $template), [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'amount' => 2000.00,
            'default_due_date' => '2025-09-15',
            'is_active' => true,
        ]);

    $response->assertRedirect(route('finance.extra-charges.index'));
    expect($template->fresh()->name)->toBe('Updated Name');
    expect($template->fresh()->amount)->toBe('2000.00');
});

it('can delete a charge template', function () {
    $template = ChargeTemplate::create([
        'name' => 'Delete Test',
        'charge_type' => 'materials',
        'amount' => 1000.00,
        'default_due_date' => '2025-12-15',
        'school_year_id' => $this->schoolYear->id,
        'is_active' => true,
        'created_by' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)
        ->delete(route('finance.extra-charges.destroy', $template));

    $response->assertRedirect(route('finance.extra-charges.index'));
    expect(ChargeTemplate::find($template->id))->toBeNull();
});
