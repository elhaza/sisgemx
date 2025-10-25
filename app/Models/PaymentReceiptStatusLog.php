<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceiptStatusLog extends Model
{
    protected $fillable = [
        'payment_receipt_id',
        'changed_by_id',
        'previous_status',
        'new_status',
        'notes',
    ];

    public function paymentReceipt()
    {
        return $this->belongsTo(PaymentReceipt::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }
}
