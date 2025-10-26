<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
{
    public function index()
    {
        $teacherSubjects = TeacherSubject::with(['teacher', 'subject'])
            ->orderBy('teacher_id')
            ->orderBy('subject_id')
            ->paginate(20);

        return view('admin.teacher-subjects.index', compact('teacherSubjects'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.teacher-subjects.create', compact('teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'proficiency' => 'required|integer|between:1,10',
        ]);

        TeacherSubject::updateOrCreate(
            [
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => $validated['subject_id'],
            ],
            ['proficiency' => $validated['proficiency']]
        );

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Competencia de docente creada exitosamente.');
    }

    public function edit(TeacherSubject $teacherSubject)
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.teacher-subjects.edit', compact('teacherSubject', 'teachers', 'subjects'));
    }

    public function update(Request $request, TeacherSubject $teacherSubject)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'proficiency' => 'required|integer|between:1,10',
        ]);

        $teacherSubject->update($validated);

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Competencia de docente actualizada exitosamente.');
    }

    public function destroy(TeacherSubject $teacherSubject)
    {
        $teacherSubject->delete();

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Competencia de docente eliminada exitosamente.');
    }
}
