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
        // Add new columns to student_tuitions
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->decimal('late_fee_amount', 10, 2)->default(0)->after('due_date')->comment('Editable late fee amount');
            $table->decimal('late_fee_paid', 10, 2)->default(0)->after('late_fee_amount')->comment('Amount of late fees actually paid');
            // Remove old columns
            $table->dropColumn(['is_paid', 'paid_at']);
        });

        // Add student_tuition_id to payments to link them
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('student_tuition_id')->nullable()->after('student_id')->constrained('student_tuitions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignIdFor('student_tuitions');
            $table->dropColumn('student_tuition_id');
        });

        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->dropColumn(['late_fee_amount', 'late_fee_paid']);
            $table->boolean('is_paid')->default(false)->after('due_date');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
        });
    }
};
