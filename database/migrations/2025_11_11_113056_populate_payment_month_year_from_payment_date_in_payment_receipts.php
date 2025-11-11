<?php

use App\Models\PaymentReceipt;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update payment_month and payment_year from payment_date where they are NULL
        PaymentReceipt::query()
            ->whereNull('payment_month')
            ->orWhereNull('payment_year')
            ->each(function (PaymentReceipt $receipt) {
                if ($receipt->payment_date) {
                    $receipt->update([
                        'payment_month' => $receipt->payment_date->month,
                        'payment_year' => $receipt->payment_date->year,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - just leave the values as they are
    }
};
