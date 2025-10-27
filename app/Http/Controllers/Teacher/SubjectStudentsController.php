<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectStudentsController extends Controller
{
    public function show(Request $request, Subject $subject): \Illuminate\View\View
    {
        // Get the current logged-in teacher
        $teacher = $request->user();

        // Verify that the subject belongs to the teacher
        if ($subject->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized');
        }

        // Get students from the same grade section
        $students = $subject->gradeSection
            ->students()
            ->with(['user', 'schoolGrade'])
            ->orderBy('user_id')
            ->get();

        // Get grades for these students in this subject
        $grades = $subject->grades()
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Get assignments for this subject
        $assignments = $subject->assignments()
            ->where('due_date', '>=', now())
            ->latest()
            ->get();

        return view('teacher.subject-students', compact(
            'subject',
            'students',
            'grades',
            'assignments'
        ));
    }
}
