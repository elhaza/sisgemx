<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'schoolYear'])->latest()->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::all();
        $schoolGrades = SchoolGrade::with('schoolYear')->orderBy('school_year_id')->orderBy('level')->orderBy('section')->get();
        $users = User::where('role', 'student')->whereDoesntHave('student')->get();

        return view('admin.students.create', compact('schoolYears', 'schoolGrades', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:school_grades,id',
            'enrollment_number' => 'required|string|unique:students',
            'curp' => 'nullable|string|size:18|unique:students',
            'gender' => 'nullable|string|in:male,female,unspecified',
            'birth_country' => 'nullable|string|max:255',
            'birth_state' => 'nullable|string|max:255',
            'birth_city' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_email' => 'nullable|email|max:255',
        ]);

        // Get grade info from school_grade
        $schoolGrade = SchoolGrade::find($validated['school_grade_id']);
        $validated['grade_level'] = $schoolGrade->name;
        $validated['group'] = $schoolGrade->section;

        Student::create($validated);

        return redirect()->route('admin.students.index')->with('success', 'Estudiante inscrito exitosamente.');
    }

    public function edit(Student $student)
    {
        $schoolYears = SchoolYear::all();
        $schoolGrades = SchoolGrade::with('schoolYear')->orderBy('school_year_id')->orderBy('level')->orderBy('section')->get();

        return view('admin.students.edit', compact('student', 'schoolYears', 'schoolGrades'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:school_grades,id',
            'enrollment_number' => 'required|string|unique:students,enrollment_number,'.$student->id,
            'curp' => 'nullable|string|size:18|unique:students,curp,'.$student->id,
            'gender' => 'nullable|string|in:male,female,unspecified',
            'birth_country' => 'nullable|string|max:255',
            'birth_state' => 'nullable|string|max:255',
            'birth_city' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_email' => 'nullable|email|max:255',
        ]);

        // Get grade info from school_grade
        $schoolGrade = SchoolGrade::find($validated['school_grade_id']);
        $validated['grade_level'] = $schoolGrade->name;
        $validated['group'] = $schoolGrade->section;

        $student->update($validated);

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado exitosamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Estudiante eliminado exitosamente.');
    }
}
