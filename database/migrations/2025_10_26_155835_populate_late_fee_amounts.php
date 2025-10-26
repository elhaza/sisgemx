<?php

use App\Helpers\PaymentHelper;
use App\Models\StudentTuition;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Calculate and populate late_fee_amount for all existing tuitions
        StudentTuition::all()->each(function (StudentTuition $tuition) {
            if (! $tuition->due_date) {
                return;
            }

            // Calculate days late
            $daysLate = PaymentHelper::calculateDaysLate($tuition->due_date->format('Y-m-d'));

            // Calculate late fee
            $lateFee = PaymentHelper::calculateLateFee($tuition->final_amount, $daysLate);

            // Update the tuition with the calculated late fee
            $tuition->update([
                'late_fee_amount' => $lateFee,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset late_fee_amount to 0
        StudentTuition::query()->update(['late_fee_amount' => 0]);
    }
};
