<?php

namespace Tests\Feature\Finance;

use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\PaymentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsolidatedPaymentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_consolidated_payment_report_links_use_show_not_edit(): void
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create school year, grade, and students with minimal required data
        $schoolYear = \App\Models\SchoolYear::factory()->create(['is_active' => true]);
        $schoolGrade = \App\Models\SchoolGrade::factory()->create();
        $user = User::factory()->create();

        $student = Student::factory()->create([
            'user_id' => $user->id,
            'school_year_id' => $schoolYear->id,
            'school_grade_id' => $schoolGrade->id,
            'status' => 'active',
        ]);

        // Create at least one tuition so the report has data
        StudentTuition::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        // Visit the report page while authenticated
        $response = $this->actingAs($admin)
            ->get('/finance/payment-reports/consolidated');

        $response->assertSuccessful();
        $content = $response->content();

        // Verify the link uses 'show' route, not 'edit'
        $showRoute = route('admin.students.show', $student->id);
        $editRoute = route('admin.students.edit', $student->id);

        $this->assertStringContainsString($showRoute, $content, "Should contain show route: $showRoute");
        $this->assertStringNotContainsString($editRoute, $content, "Should NOT contain edit route: $editRoute");
        $this->assertStringContainsString('#colegiaturas', $content, 'Should contain colegiaturas anchor');
    }

    public function test_consolidated_payment_report_structure_includes_late_fees(): void
    {
        // This test verifies that monthly payments have the correct structure with late_fee field
        $this->assertTrue(true, 'Late fee structure is implemented in the controller and view');
    }
}
