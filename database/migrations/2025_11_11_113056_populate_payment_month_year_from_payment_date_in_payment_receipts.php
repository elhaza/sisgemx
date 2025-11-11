<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update payment_month and payment_year from payment_date where they are NULL
        DB::update('
            UPDATE payment_receipts
            SET payment_month = MONTH(payment_date),
                payment_year = YEAR(payment_date)
            WHERE payment_month IS NULL
               OR payment_year IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - just leave the values as they are
    }
};
