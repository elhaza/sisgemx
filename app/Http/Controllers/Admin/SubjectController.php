<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['teacher', 'schoolYear'])
            ->latest()
            ->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        $schoolYears = SchoolYear::all();
        $gradeLevels = [1, 2, 3, 4, 5, 6];

        return view('admin.subjects.create', compact('teachers', 'schoolYears', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'grade_level' => 'required|integer|between:1,6',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Materia creada exitosamente.');
    }

    public function edit(Subject $subject)
    {
        $teachers = User::where('role', 'teacher')->get();
        $schoolYears = SchoolYear::all();
        $gradeLevels = [1, 2, 3, 4, 5, 6];

        return view('admin.subjects.edit', compact('subject', 'teachers', 'schoolYears', 'gradeLevels'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'grade_level' => 'required|integer|between:1,6',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Materia actualizada exitosamente.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Materia eliminada exitosamente.');
    }
}
