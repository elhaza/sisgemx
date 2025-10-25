<?php

namespace App\Models;

use App\PaymentMethod;
use App\ReceiptStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentReceiptFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'parent_id',
        'registered_by_id',
        'payment_date',
        'amount_paid',
        'reference',
        'account_holder_name',
        'issuing_bank',
        'payment_method',
        'receipt_image',
        'status',
        'validated_by',
        'validated_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount_paid' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'status' => ReceiptStatus::class,
            'validated_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(PaymentReceiptStatusLog::class);
    }
}
