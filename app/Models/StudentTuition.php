<?php

namespace App\Models;

use App\Helpers\PaymentHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentTuition extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'monthly_tuition_id',
        'year',
        'month',
        'monthly_amount',
        'discount_percentage',
        'discount_amount',
        'discount_reason',
        'final_amount',
        'due_date',
        'late_fee_amount',
        'late_fee_paid',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'late_fee_amount' => 'decimal:2',
            'late_fee_paid' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function monthlyTuition(): BelongsTo
    {
        return $this->belongsTo(MonthlyTuition::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $months[$this->month] ?? '';
    }

    /**
     * Get the number of days late for this tuition
     */
    public function getDaysLateAttribute(): int
    {
        if (! $this->due_date) {
            return 0;
        }

        return PaymentHelper::calculateDaysLate($this->due_date->format('Y-m-d'));
    }

    /**
     * Get the late fee for this tuition (uses stored late_fee_amount)
     */
    public function getLateFeeAttribute(): float
    {
        return (float) ($this->late_fee_amount ?? 0);
    }

    /**
     * Get the calculated late fee based on current configuration
     */
    public function getCalculatedLateFeeAmountAttribute(): float
    {
        if (! $this->due_date || $this->days_late <= 0) {
            return 0;
        }

        // Check if fully paid (total amount paid >= final_amount)
        $totalPaid = $this->payments()->where('is_paid', true)->sum('amount');
        if ($totalPaid >= $this->final_amount) {
            return 0;
        }

        return PaymentHelper::calculateLateFee((float) $this->final_amount, $this->days_late);
    }

    /**
     * Get the total amount including late fees
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->final_amount + $this->late_fee_amount;
    }

    /**
     * Get the calculated total amount including calculated late fees
     */
    public function getCalculatedTotalAmountAttribute(): float
    {
        return $this->final_amount + $this->calculated_late_fee_amount;
    }

    /**
     * Check if this tuition is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->days_late > config('payment.grace_period_days', 0);
    }

    /**
     * Check if this tuition has been paid
     */
    public function isPaid(): bool
    {
        return $this->payments()->where('is_paid', true)->exists();
    }

    protected static function booted(): void
    {
        static::saving(function (StudentTuition $tuition) {
            // Calculate final amount based on discount percentage
            $discount = ($tuition->monthly_amount * $tuition->discount_percentage) / 100;
            $tuition->final_amount = $tuition->monthly_amount - $discount;

            // Set due date if not already set
            if (! $tuition->due_date && $tuition->year && $tuition->month) {
                $tuition->due_date = PaymentHelper::calculateDueDate($tuition->year, $tuition->month);
            }
        });
    }
}
