<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\MedicalJustification;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $unreadMessageCount = $teacher->unread_message_count;

        $mySubjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->with('gradeSection')
            ->withCount(['grades', 'assignments'])
            ->get();

        $totalStudents = Student::whereHas('schoolGrade', function ($query) use ($mySubjects) {
            $query->whereIn('grade_level', $mySubjects->pluck('grade_level')->unique());
        })
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->count();

        $pendingGrades = Assignment::whereIn('subject_id', $mySubjects->pluck('id'))
            ->where('due_date', '<', now())
            ->count();

        $activeAssignments = Assignment::whereIn('subject_id', $mySubjects->pluck('id'))
            ->where('due_date', '>=', now())
            ->count();

        $myAnnouncements = Announcement::where('teacher_id', $teacher->id)
            ->latest()
            ->take(5)
            ->get();

        $todaySchedule = Schedule::whereIn('subject_id', $mySubjects->pluck('id'))
            ->where('day_of_week', strtolower(now()->format('l')))
            ->with('subject')
            ->orderBy('start_time')
            ->get();

        $medicalJustifications = MedicalJustification::whereHas('student', function ($query) use ($mySubjects) {
            $query->whereHas('schoolGrade', function ($subQuery) use ($mySubjects) {
                $subQuery->whereIn('grade_level', $mySubjects->pluck('grade_level')->unique());
            });
        })->latest()->take(5)->get();

        return view('teacher.dashboard', compact(
            'unreadMessageCount',
            'mySubjects',
            'totalStudents',
            'pendingGrades',
            'activeAssignments',
            'myAnnouncements',
            'todaySchedule',
            'medicalJustifications'
        ));
    }
}
