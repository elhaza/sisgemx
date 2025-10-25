<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\SchoolGrade;
use App\Models\Subject;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolGrades = SchoolGrade::all();
        $teachers = User::where('role', UserRole::Teacher)->get();

        // Time slots for elementary school
        $timeSlots = [
            ['start' => '08:00:00', 'end' => '09:00:00'],
            ['start' => '09:00:00', 'end' => '10:00:00'],
            ['start' => '10:00:00', 'end' => '11:00:00'],
            ['start' => '11:30:00', 'end' => '12:30:00'],
            ['start' => '12:30:00', 'end' => '13:30:00'],
        ];

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($schoolGrades as $schoolGrade) {
            // Get subjects for this grade level
            $subjects = Subject::where('grade_level', $schoolGrade->level)->get();

            if ($subjects->isEmpty()) {
                continue;
            }

            $subjectIndex = 0;
            $teacherIndex = 0;

            // Create schedule for each day
            foreach ($daysOfWeek as $day) {
                foreach ($timeSlots as $slot) {
                    $subject = $subjects[$subjectIndex % $subjects->count()];
                    $teacher = $teachers[$teacherIndex % $teachers->count()];

                    Schedule::create([
                        'school_grade_id' => $schoolGrade->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                        'day_of_week' => $day,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'classroom' => $schoolGrade->level.$schoolGrade->section,
                    ]);

                    $subjectIndex++;
                    $teacherIndex++;
                }
            }
        }

        $this->command->info('Horarios creados para todos los grupos (5 bloques × 5 días)');
    }
}
