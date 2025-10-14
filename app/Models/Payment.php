<?php

namespace App\Models;

use App\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payment_type',
        'description',
        'amount',
        'month',
        'year',
        'due_date',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_type' => PaymentType::class,
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'is_paid' => 'boolean',
            'paid_at' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
