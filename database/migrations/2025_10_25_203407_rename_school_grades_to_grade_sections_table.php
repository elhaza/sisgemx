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

        // For SQLite, rename the level column to grade_level
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support simple RENAME COLUMN, so we need to recreate the table
            // First, check if the table has the break_time columns (they might be added by other migrations)
            $columns = DB::select('PRAGMA table_info(grade_sections)');
            $hasBreakTime = collect($columns)->contains(fn ($col) => $col->name === 'break_time_start');

            if ($hasBreakTime) {
                DB::statement('
                    CREATE TABLE grade_sections_new (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        grade_level INTEGER NOT NULL,
                        name VARCHAR(255),
                        section CHAR(1) DEFAULT "A",
                        school_year_id BIGINT UNSIGNED NOT NULL,
                        break_time_start TIME,
                        break_time_end TIME,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (school_year_id) REFERENCES school_years(id) ON DELETE CASCADE,
                        UNIQUE (grade_level, section, school_year_id)
                    )
                ');

                DB::statement('
                    INSERT INTO grade_sections_new (id, grade_level, name, section, school_year_id, break_time_start, break_time_end, created_at, updated_at)
                    SELECT id, level, CONCAT(level, section), section, school_year_id, break_time_start, break_time_end, created_at, updated_at
                    FROM grade_sections
                ');
            } else {
                DB::statement('
                    CREATE TABLE grade_sections_new (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        grade_level INTEGER NOT NULL,
                        name VARCHAR(255),
                        section CHAR(1) DEFAULT "A",
                        school_year_id BIGINT UNSIGNED NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (school_year_id) REFERENCES school_years(id) ON DELETE CASCADE,
                        UNIQUE (grade_level, section, school_year_id)
                    )
                ');

                DB::statement('
                    INSERT INTO grade_sections_new (id, grade_level, name, section, school_year_id, created_at, updated_at)
                    SELECT id, level, CONCAT(level, section), section, school_year_id, created_at, updated_at
                    FROM grade_sections
                ');
            }

            DB::statement('DROP TABLE grade_sections');

            DB::statement('ALTER TABLE grade_sections_new RENAME TO grade_sections');
        } else {
            // MySQL/PostgreSQL use standard ALTER TABLE syntax
            DB::statement('ALTER TABLE grade_sections CHANGE COLUMN level grade_level INT NOT NULL');
        }
    }

    public function down(): void
    {
        // Reverse: rename table back to school_grades
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                CREATE TABLE school_grades_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    level INTEGER NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    section CHAR(1) DEFAULT "A",
                    school_year_id BIGINT UNSIGNED NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (school_year_id) REFERENCES school_years(id) ON DELETE CASCADE
                )
            ');

            DB::statement('
                INSERT INTO school_grades_new (id, level, name, section, school_year_id, created_at, updated_at)
                SELECT id, grade_level, name, section, school_year_id, created_at, updated_at
                FROM grade_sections
            ');

            DB::statement('DROP TABLE grade_sections');

            DB::statement('ALTER TABLE school_grades_new RENAME TO school_grades');
        } else {
            // MySQL/PostgreSQL
            DB::statement('ALTER TABLE grade_sections CHANGE COLUMN grade_level level INT NOT NULL');
            Schema::rename('grade_sections', 'school_grades');
        }
    }
};
