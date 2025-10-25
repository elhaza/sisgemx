<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Calculate late fee based on days late and configuration
     */
    public static function calculateLateFee(float $amount, int $daysLate): float
    {
        $graceDays = config('payment.grace_period_days');
        $type = strtoupper(config('payment.late_fee_type'));
        $rate = config('payment.late_fee_rate');
        $fixed = config('payment.late_fee_amount');

        if ($daysLate <= $graceDays) {
            return 0.0;
        }

        $effectiveDays = $daysLate - $graceDays;

        switch ($type) {
            case 'ONCE':
                return $fixed;

            case 'DAILY':
                return $fixed > 0
                    ? $fixed * $effectiveDays
                    : $amount * $rate * $effectiveDays;

            case 'MONTHLY':
            default:
                $monthsLate = ceil($effectiveDays / 30);

                return $fixed > 0
                    ? $fixed * $monthsLate
                    : $amount * $rate * $monthsLate;
        }
    }

    /**
     * Calculate the due date for a tuition based on year and month
     */
    public static function calculateDueDate(int $year, int $month): string
    {
        $dueDay = config('payment.tuition_due_day', 10);

        return sprintf('%04d-%02d-%02d', $year, $month, $dueDay);
    }

    /**
     * Calculate days late for a given due date
     */
    public static function calculateDaysLate(string $dueDate): int
    {
        $due = new \DateTime($dueDate);
        $today = new \DateTime('today');

        if ($today <= $due) {
            return 0;
        }

        $diff = $today->diff($due);

        return $diff->days;
    }
}
