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
        'payment_id',
        'parent_id',
        'payment_date',
        'amount_paid',
        'payment_method',
        'receipt_file_path',
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

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
