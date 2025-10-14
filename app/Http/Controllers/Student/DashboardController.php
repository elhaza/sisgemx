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
        $student = auth()->user()->student;

        if (! $student) {
            abort(404, 'Estudiante no encontrado');
        }

        $totalGrades = Grade::where('student_id', $student->id)->count();

        $pendingAssignments = Assignment::where('due_date', '>=', now())
            ->whereHas('subject.students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })->count();

        $overdueAssignments = Assignment::where('due_date', '<', now())
            ->whereHas('subject.students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })->count();

        $recentAnnouncements = Announcement::whereHas('teacher.subjects.students', function ($query) use ($student) {
            $query->where('students.id', $student->id);
        })->latest()->take(5)->get();

        $schedules = Schedule::where('grade_level', $student->grade_level)
            ->where('group', $student->group)
            ->with('subject')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('student.dashboard', compact(
            'student',
            'totalGrades',
            'pendingAssignments',
            'overdueAssignments',
            'recentAnnouncements',
            'schedules'
        ));
    }
}
