<?php

namespace Database\Seeders;

use App\Models\PaymentReceipt;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\PaymentMethod;
use App\ReceiptStatus;
use App\UserRole;
use Illuminate\Database\Seeder;

class PaymentReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $students = Student::where('school_year_id', $activeSchoolYear->id)->get();
        $financeAdmin = User::where('role', UserRole::FinanceAdmin)->first();

        // Payment methods distribution
        $paymentMethods = [
            PaymentMethod::Transfer,
            PaymentMethod::Cash,
            PaymentMethod::Card,
        ];

        $receiptCount = 0;
        $validatedCount = 0;
        $pendingCount = 0;

        foreach ($students as $student) {
            // Get tuitions for this student (sorted by date)
            $tuitions = StudentTuition::where('student_id', $student->id)
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Determine how many months to pay (majority have paid August-September, some through October)
            $monthsToPay = rand(2, 3); // Most students have paid 2-3 months

            foreach ($tuitions->take($monthsToPay) as $index => $tuition) {
                // Payment date: some on time, some late
                $dueDate = $tuition->due_date;
                $paymentDate = $dueDate->copy();

                if ($index === 0) {
                    // First payment: 80% on time, 20% late
                    if (rand(1, 100) > 80) {
                        $paymentDate = $paymentDate->addDays(rand(5, 20));
                    }
                } else {
                    // Later payments: 70% on time, 30% late
                    if (rand(1, 100) > 70) {
                        $paymentDate = $paymentDate->addDays(rand(3, 15));
                    }
                }

                // Status: 85% validated, 10% pending, 5% rejected
                $statusRandom = rand(1, 100);
                if ($statusRandom <= 85) {
                    $status = ReceiptStatus::Validated;
                    $validatedBy = $financeAdmin->id;
                    $validatedAt = $paymentDate->copy()->addDays(rand(1, 3));
                    $validatedCount++;
                } elseif ($statusRandom <= 95) {
                    $status = ReceiptStatus::Pending;
                    $validatedBy = null;
                    $validatedAt = null;
                    $pendingCount++;
                } else {
                    $status = ReceiptStatus::Rejected;
                    $validatedBy = $financeAdmin->id;
                    $validatedAt = $paymentDate->copy()->addDays(rand(1, 3));
                    $rejectionReason = 'Comprobante ilegible, favor de volver a subir';
                }

                // Calculate amount with late fee if applicable
                $amount = $tuition->final_amount;
                if ($paymentDate->gt($dueDate)) {
                    $daysLate = $dueDate->diffInDays($paymentDate);
                    $lateFee = \App\Helpers\PaymentHelper::calculateLateFee($amount, $daysLate);
                    $amount += $lateFee;
                }

                PaymentReceipt::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->tutor_1_id,
                    'registered_by_id' => $student->tutor_1_id,
                    'payment_date' => $paymentDate,
                    'amount_paid' => $amount,
                    'payment_year' => $tuition->year,
                    'payment_month' => $tuition->month,
                    'reference' => 'REF-'.str_pad($receiptCount + 1, 8, '0', STR_PAD_LEFT),
                    'account_holder_name' => $student->tutor1->full_name,
                    'issuing_bank' => ['BBVA', 'Santander', 'Banorte', 'HSBC'][rand(0, 3)],
                    'payment_method' => $paymentMethods[rand(0, count($paymentMethods) - 1)],
                    'receipt_image' => 'receipts/demo-receipt-'.($receiptCount + 1).'.jpg',
                    'status' => $status,
                    'validated_by' => $validatedBy ?? null,
                    'validated_at' => $validatedAt ?? null,
                    'rejection_reason' => $rejectionReason ?? null,
                ]);

                $receiptCount++;
            }
        }

        $this->command->info("Comprobantes de pago creados: {$receiptCount} total");
        $this->command->info("Validados: {$validatedCount}, Pendientes: {$pendingCount}, Rechazados: ".($receiptCount - $validatedCount - $pendingCount));
        $this->command->info('Incluye pagos puntuales y con retrasos para demostración');
    }
}
