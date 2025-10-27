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
        // Get all subjects grouped by name and grade_level
        $subjects = DB::table('subjects')
            ->select('name', 'grade_level', 'teacher_id', 'school_year_id')
            ->distinct()
            ->get();

        foreach ($subjects as $subject) {
            // Find sections for this grade level
            $sections = DB::table('grade_sections')
                ->where('grade_level', $subject->grade_level)
                ->where('school_year_id', $subject->school_year_id)
                ->orderBy('section')
                ->get();

            foreach ($sections as $section) {
                // Check if this subject already exists for this section
                $exists = DB::table('subjects')
                    ->where('name', $subject->name)
                    ->where('grade_level', $subject->grade_level)
                    ->where('teacher_id', $subject->teacher_id)
                    ->where('grade_section_id', $section->id)
                    ->exists();

                if (! $exists) {
                    // Update or create the subject for this section
                    DB::table('subjects')
                        ->where('name', $subject->name)
                        ->where('grade_level', $subject->grade_level)
                        ->where('teacher_id', $subject->teacher_id)
                        ->where('school_year_id', $subject->school_year_id)
                        ->where('grade_section_id', null)
                        ->limit(1)
                        ->update(['grade_section_id' => $section->id]);

                    // If there are more sections, duplicate the subject
                    if ($section->id !== $sections->first()->id) {
                        $subjectData = DB::table('subjects')
                            ->where('name', $subject->name)
                            ->where('grade_level', $subject->grade_level)
                            ->where('teacher_id', $subject->teacher_id)
                            ->where('school_year_id', $subject->school_year_id)
                            ->where('grade_section_id', $sections->first()->id)
                            ->first();

                        if ($subjectData) {
                            DB::table('subjects')->insert([
                                'name' => $subjectData->name,
                                'description' => $subjectData->description,
                                'teacher_id' => $subjectData->teacher_id,
                                'grade_level' => $subjectData->grade_level,
                                'school_year_id' => $subjectData->school_year_id,
                                'grade_section_id' => $section->id,
                                'default_hours_per_week' => $subjectData->default_hours_per_week,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all grade_section_id to NULL
        DB::table('subjects')->update(['grade_section_id' => null]);

        // Delete duplicate subjects (keep only one per name/grade_level/teacher)
        DB::statement('
            DELETE FROM subjects
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM subjects s1
                GROUP BY name, grade_level, teacher_id, school_year_id
            )
        ');
    }
};
