<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentTransferController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $schoolGrades = SchoolGrade::with('schoolYear')->orderBy('school_year_id')->orderBy('level')->orderBy('section')->get();

        return view('admin.students.transfer', compact('schoolYears', 'schoolGrades'));
    }

    public function getStudents(Request $request)
    {
        $query = Student::with(['user', 'schoolYear', 'schoolGrade']);

        if ($request->filled('school_grade_id')) {
            $query->where('school_grade_id', $request->school_grade_id);
        } elseif ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        $students = $query->orderBy('enrollment_number')->get();

        return response()->json($students);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'target_school_year_id' => 'required|exists:school_years,id',
            'target_school_grade_id' => 'required|exists:school_grades,id',
        ]);

        $targetSchoolGrade = SchoolGrade::findOrFail($validated['target_school_grade_id']);

        // Verify the target school grade belongs to the target school year
        if ($targetSchoolGrade->school_year_id != $validated['target_school_year_id']) {
            return response()->json([
                'success' => false,
                'message' => 'El grado seleccionado no pertenece al ciclo escolar destino.',
            ], 422);
        }

        $transferredCount = 0;
        $errors = [];

        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::find($studentId);

            if (! $student) {
                continue;
            }

            // Update student's school year and school grade
            $student->update([
                'school_year_id' => $validated['target_school_year_id'],
                'school_grade_id' => $validated['target_school_grade_id'],
                'grade_level' => $targetSchoolGrade->name,
                'group' => $targetSchoolGrade->section,
            ]);

            $transferredCount++;
        }

        if ($transferredCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "Se transfirieron {$transferredCount} estudiantes exitosamente.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudieron transferir los estudiantes.',
        ], 422);
    }
}
