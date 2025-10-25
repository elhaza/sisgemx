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
        Schema::table('schedules', function (Blueprint $table) {
            // Remove old columns
            $table->dropForeign(['school_year_id']);
            $table->dropColumn(['school_year_id', 'grade_level', 'group']);

            // Add new foreign key to school_grades
            $table->foreignId('school_grade_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Remove school_grade_id
            $table->dropForeign(['school_grade_id']);
            $table->dropColumn('school_grade_id');

            // Restore old columns
            $table->foreignId('school_year_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('grade_level', 50)->after('subject_id');
            $table->string('group', 10)->after('grade_level');
        });
    }
};
