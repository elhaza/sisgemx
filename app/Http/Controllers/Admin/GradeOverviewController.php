<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeOverviewController extends Controller
{
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::all();
        $schoolGrades = SchoolGrade::with('schoolYear')->get();
        $subjects = Subject::with('teacher')->get();

        $selectedSchoolYear = $request->input('school_year_id');
        $selectedSchoolGrade = $request->input('school_grade_id');
        $selectedSubject = $request->input('subject_id');

        $gradesQuery = Grade::with(['student.user', 'subject']);

        if ($selectedSchoolYear) {
            $gradesQuery->whereHas('student', function ($query) use ($selectedSchoolYear) {
                $query->where('school_year_id', $selectedSchoolYear);
            });
        }

        if ($selectedSchoolGrade) {
            $gradesQuery->whereHas('student', function ($query) use ($selectedSchoolGrade) {
                $query->where('school_grade_id', $selectedSchoolGrade);
            });
        }

        if ($selectedSubject) {
            $gradesQuery->where('subject_id', $selectedSubject);
        }

        $grades = $gradesQuery->latest()->paginate(20);

        // Calcular estadísticas
        $averageGrade = $gradesQuery->avg('grade') ?? 0;
        $totalStudents = Student::when($selectedSchoolYear, function ($query) use ($selectedSchoolYear) {
            $query->where('school_year_id', $selectedSchoolYear);
        })->when($selectedSchoolGrade, function ($query) use ($selectedSchoolGrade) {
            $query->where('school_grade_id', $selectedSchoolGrade);
        })->count();

        return view('admin.grades.index', compact(
            'grades',
            'schoolYears',
            'schoolGrades',
            'subjects',
            'selectedSchoolYear',
            'selectedSchoolGrade',
            'selectedSubject',
            'averageGrade',
            'totalStudents'
        ));
    }
}
