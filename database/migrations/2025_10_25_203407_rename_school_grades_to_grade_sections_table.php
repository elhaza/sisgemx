<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table from school_grades to grade_sections
        Schema::rename('school_grades', 'grade_sections');

        // Modify the table structure
        DB::statement('ALTER TABLE grade_sections CHANGE COLUMN level grade_level INT NOT NULL COMMENT "Ejemplo: 1, 2, 3, 4, 5, 6"');

        DB::statement('ALTER TABLE grade_sections CHANGE COLUMN section section CHAR(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT "A" COMMENT "Ejemplo: A, B, C"');

        DB::statement('ALTER TABLE grade_sections DROP COLUMN name');

        DB::statement('ALTER TABLE grade_sections ADD COLUMN name VARCHAR(255) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (CONCAT(grade_level, section)) STORED COMMENT "Ejemplo: 1A, 2B" AFTER section');
    }

    public function down(): void
    {
        // Reverse: rename table back to school_grades
        Schema::rename('grade_sections', 'school_grades');

        // Restore original structure
        DB::statement('ALTER TABLE school_grades DROP COLUMN name');

        DB::statement('ALTER TABLE school_grades CHANGE COLUMN grade_level level INT NOT NULL COMMENT "Ej: 1, 2, 3, etc."');

        DB::statement('ALTER TABLE school_grades CHANGE COLUMN section section CHAR(1) COLLATE utf8mb4_unicode_ci NULL DEFAULT "A" COMMENT "Ej: A, B, C"');

        DB::statement('ALTER TABLE school_grades ADD COLUMN name VARCHAR(255) COLLATE utf8mb4_unicode_ci AFTER level');
    }
};
