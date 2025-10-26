<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Monto de descuento aplicado');
            $table->text('discount_reason')->nullable()->comment('Razón del descuento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_reason']);
        });
    }
};
