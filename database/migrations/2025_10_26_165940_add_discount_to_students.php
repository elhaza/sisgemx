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
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('tutor_2_id')->comment('Descuento global del estudiante');
        });

        // Migrate discount data from student_tuitions to students
        // Take the discount from first tuition of each student per school year
        \DB::statement('
            UPDATE students s
            SET discount_percentage = (
                SELECT COALESCE(MAX(st.discount_percentage), 0)
                FROM student_tuitions st
                WHERE st.student_id = s.id
                AND st.school_year_id = s.school_year_id
                LIMIT 1
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};
