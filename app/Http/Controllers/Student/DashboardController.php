<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        $unreadMessageCount = $user->unread_message_count;

        if (! $student) {
            abort(404, 'Estudiante no encontrado');
        }

        $totalGrades = Grade::where('student_id', $student->id)->count();

        $pendingAssignments = Assignment::where('due_date', '>=', now())
            ->whereHas('subject', function ($query) use ($student) {
                $query->where('grade_level', $student->grade_level);
            })->count();

        $overdueAssignments = Assignment::where('due_date', '<', now())
            ->whereHas('subject', function ($query) use ($student) {
                $query->where('grade_level', $student->grade_level);
            })->count();

        $recentAnnouncements = Announcement::whereHas('teacher.subjects', function ($query) use ($student) {
            $query->where('grade_level', $student->grade_level);
        })->latest()->take(5)->get();

        $schedules = Schedule::where('school_grade_id', $student->school_grade_id)
            ->with(['subject', 'subject.teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Get current day and time for highlighting current class
        $now = now();
        $currentDayOfWeek = $now->format('l');
        $dayMapping = [
            'Monday' => 'monday',
            'Tuesday' => 'tuesday',
            'Wednesday' => 'wednesday',
            'Thursday' => 'thursday',
            'Friday' => 'friday',
            'Saturday' => 'saturday',
            'Sunday' => 'sunday',
        ];
        $currentDay = $dayMapping[$currentDayOfWeek] ?? null;
        $currentTime = $now->format('H:i');

        return view('student.dashboard', compact(
            'unreadMessageCount',
            'student',
            'totalGrades',
            'pendingAssignments',
            'overdueAssignments',
            'recentAnnouncements',
            'schedules',
            'currentDay',
            'currentTime'
        ));
    }

    public function schedule()
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(404, 'Estudiante no encontrado');
        }

        $schedules = Schedule::where('school_grade_id', $student->school_grade_id)
            ->with(['subject.teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('student.schedule', compact('student', 'schedules'));
    }

    public function grades()
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(404, 'Estudiante no encontrado');
        }

        $grades = Grade::where('student_id', $student->id)
            ->with(['subject'])
            ->latest()
            ->paginate(15);

        return view('student.grades', compact('student', 'grades'));
    }

    public function assignments()
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(404, 'Estudiante no encontrado');
        }

        $assignments = Assignment::whereHas('subject.schedules', function ($query) use ($student) {
            $query->where('school_grade_id', $student->school_grade_id);
        })
            ->with(['subject'])
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('student.assignments', compact('student', 'assignments'));
    }
}
