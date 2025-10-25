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
            $table->foreignId('monthly_tuition_id')->nullable()->after('school_year_id')->constrained()->nullOnDelete();
            $table->integer('year')->after('monthly_tuition_id');
            $table->integer('month')->after('year');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('monthly_amount');
            $table->decimal('final_amount', 10, 2)->after('discount_percentage');

            // Unique constraint: one tuition record per student, per school year, per month
            $table->unique(['student_id', 'school_year_id', 'year', 'month'], 'student_tuitions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->dropUnique('student_tuitions_unique');
            $table->dropForeign(['monthly_tuition_id']);
            $table->dropColumn(['monthly_tuition_id', 'year', 'month', 'discount_percentage', 'final_amount']);
        });
    }
};
