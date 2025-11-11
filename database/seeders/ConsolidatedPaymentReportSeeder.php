<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsolidatedPaymentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPaymentRecords();
        $this->createAssignedCharges();
    }

    /**
     * Create payment records for student tuitions
     */
    private function createPaymentRecords(): void
    {
        $tuitions = \App\Models\StudentTuition::query()
            ->with('student')
            ->get();

        $paymentCount = 0;

        foreach ($tuitions as $tuition) {
            // 70% of tuitions have been paid
            if (rand(1, 100) <= 70) {
                \App\Models\Payment::create([
                    'student_id' => $tuition->student_id,
                    'student_tuition_id' => $tuition->id,
                    'payment_type' => \App\PaymentType::Tuition->value,
                    'description' => "Matrícula {$tuition->month_name}",
                    'amount' => (float) $tuition->final_amount,
                    'month' => $tuition->month,
                    'year' => $tuition->year,
                    'due_date' => $tuition->due_date,
                    'is_paid' => true,
                    'paid_at' => now()->subDays(rand(0, 30)),
                ]);
                $paymentCount++;
            }
        }

        $this->command->info("Created {$paymentCount} payment records");
    }

    /**
     * Create assigned charges for students
     */
    private function createAssignedCharges(): void
    {
        $activeSchoolYear = \App\Models\SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return;
        }

        $chargeTemplates = \App\Models\ChargeTemplate::where('school_year_id', $activeSchoolYear->id)
            ->get();

        if ($chargeTemplates->isEmpty()) {
            $this->command->warn('No charge templates found, skipping assigned charges');

            return;
        }

        $students = \App\Models\Student::where('school_year_id', $activeSchoolYear->id)
            ->where('status', 'active')
            ->get();

        $chargeCount = 0;

        foreach ($students as $student) {
            foreach ($chargeTemplates as $template) {
                // 60% of students have each charge assigned, 80% of those are paid
                if (rand(1, 100) <= 60) {
                    $isPaid = rand(1, 100) <= 80;
                    \App\Models\StudentAssignedCharge::create([
                        'student_id' => $student->id,
                        'charge_template_id' => $template->id,
                        'amount' => (float) $template->amount,
                        'due_date' => $template->default_due_date,
                        'is_paid' => $isPaid,
                        'created_by' => \App\Models\User::where('role', 'admin')->first()?->id ?? 1,
                    ]);
                    $chargeCount++;
                }
            }
        }

        $this->command->info("Created {$chargeCount} assigned charge records");
    }
}
