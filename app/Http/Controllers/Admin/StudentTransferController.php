<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSection;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentTransferController extends Controller
{
    public function index()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $schoolGrades = GradeSection::with('schoolYear')
            ->where('school_year_id', $activeSchoolYear?->id)
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();

        // Add student counts by gender for each grade
        $schoolGrades = $schoolGrades->map(function ($grade) {
            $grade->student_count = $this->getGradeStudentStats($grade->id);

            return $grade;
        });

        return view('admin.students.transfer', compact('schoolGrades', 'activeSchoolYear'));
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

    public function getDestinationGrades(Request $request)
    {
        $sourceGradeId = $request->query('source_grade_id');
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $sourceGradeId || ! $activeSchoolYear) {
            return response()->json(['grades' => []]);
        }

        $sourceGrade = GradeSection::find($sourceGradeId);

        if (! $sourceGrade) {
            return response()->json(['grades' => []]);
        }

        $destinationGrades = GradeSection::where('school_year_id', $activeSchoolYear->id)
            ->where('grade_level', $sourceGrade->grade_level)
            ->where('id', '!=', $sourceGradeId)
            ->orderBy('section')
            ->get();

        // Add student counts by gender for each grade
        $destinationGrades = $destinationGrades->map(function ($grade) {
            $grade->student_count = $this->getGradeStudentStats($grade->id);

            return $grade;
        });

        return response()->json(['grades' => $destinationGrades]);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'target_school_year_id' => 'required|exists:school_years,id',
            'target_school_grade_id' => 'required|exists:grade_sections,id',
        ]);

        $targetSchoolGrade = GradeSection::findOrFail($validated['target_school_grade_id']);

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

    protected function getGradeStudentStats(int $gradeId): array
    {
        $total = Student::where('school_grade_id', $gradeId)->count();
        $males = Student::where('school_grade_id', $gradeId)->where('gender', 'male')->count();
        $females = Student::where('school_grade_id', $gradeId)->where('gender', 'female')->count();

        return [
            'total' => $total,
            'males' => $males,
            'females' => $females,
        ];
    }
}
