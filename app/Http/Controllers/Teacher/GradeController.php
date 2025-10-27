<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreGradeRequest;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;

class GradeController extends Controller
{
    /**
     * Display a listing of grades.
     */
    public function index()
    {
        $teacher = auth()->user();
        $mySubjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->pluck('id');

        $grades = Grade::whereIn('subject_id', $mySubjects)
            ->with('student', 'subject')
            ->latest('created_at')
            ->paginate(15);

        return view('teacher.grades.index', compact('grades'));
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create()
    {
        $teacher = auth()->user();
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->with('gradeSection')
            ->get()
            ->sortBy(function ($subject) {
                return $subject->gradeSection?->name ?? $subject->grade_level;
            });

        return view('teacher.grades.create', compact('subjects'));
    }

    /**
     * Get students and grades for bulk grading
     */
    public function bulkGradeView($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('id', $subjectId)
            ->where('teacher_id', $teacher->id)
            ->with('gradeSection')
            ->first();

        if (! $subject) {
            return redirect()->back()->with('error', 'Materia no encontrada');
        }

        $studentsQuery = Student::where('school_year_id', function ($query) {
            $query->select('id')
                ->from('school_years')
                ->where('is_active', true)
                ->limit(1);
        });

        if ($subject->grade_section_id) {
            $studentsQuery = $studentsQuery->where('school_grade_id', $subject->grade_section_id);
        } else {
            $studentsQuery = $studentsQuery->whereHas('schoolGrade', function ($query) use ($subject) {
                $query->where('grade_level', $subject->grade_level);
            });
        }

        $students = $studentsQuery->with(['user', 'schoolGrade'])
            ->orderBy('user_id')
            ->get()
            ->map(function ($student) use ($teacher, $subject) {
                $grade = Grade::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('teacher_id', $teacher->id)
                    ->latest('created_at')
                    ->first();

                return [
                    'student' => $student,
                    'grade' => $grade,
                ];
            });

        return view('teacher.grades.bulk-create', compact('subject', 'students'));
    }

    /**
     * Store bulk grades
     */
    public function storeBulkGrades(Request $request)
    {
        $teacher = auth()->user();

        $subject = Subject::where('id', $request->subject_id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (! $subject) {
            return redirect()->back()->with('error', 'Materia no encontrada');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'period' => 'required|string|max:50',
            'grades' => 'array',
            'grades.*.student_id' => 'exists:students,id',
            'grades.*.grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.comments' => 'nullable|string|max:500',
        ]);

        $period = $validated['period'];
        $gradesData = $validated['grades'] ?? [];
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($gradesData as $gradeData) {
            if (empty($gradeData['grade'])) {
                continue;
            }

            $grade = Grade::where('student_id', $gradeData['student_id'])
                ->where('subject_id', $subject->id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($grade) {
                $grade->update([
                    'period' => $period,
                    'grade' => $gradeData['grade'],
                    'comments' => $gradeData['comments'] ?? null,
                ]);
                $updatedCount++;
            } else {
                Grade::create([
                    'student_id' => $gradeData['student_id'],
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'period' => $period,
                    'grade' => $gradeData['grade'],
                    'comments' => $gradeData['comments'] ?? null,
                ]);
                $createdCount++;
            }
        }

        $message = "Calificaciones guardadas: {$createdCount} nuevas, {$updatedCount} actualizadas";

        return redirect()->route('teacher.grades.index')->with('success', $message);
    }

    /**
     * Get students for a specific subject via AJAX
     */
    public function getStudentsBySubject($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('id', $subjectId)
            ->where('teacher_id', $teacher->id)
            ->with('gradeSection')
            ->first();

        if (! $subject) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        $studentsQuery = Student::where('school_year_id', function ($query) {
            $query->select('id')
                ->from('school_years')
                ->where('is_active', true)
                ->limit(1);
        });

        if ($subject->grade_section_id) {
            $studentsQuery = $studentsQuery->where('school_grade_id', $subject->grade_section_id);
        } else {
            $studentsQuery = $studentsQuery->whereHas('schoolGrade', function ($query) use ($subject) {
                $query->where('grade_level', $subject->grade_level);
            });
        }

        $students = $studentsQuery->with('user', 'schoolGrade')
            ->orderBy('user_id')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->user->full_name,
                    'grade_level' => $student->schoolGrade->grade_level,
                ];
            });

        return response()->json($students);
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(StoreGradeRequest $request)
    {
        $teacher = auth()->user();
        $validated = $request->validated();
        $validated['teacher_id'] = $teacher->id;

        Grade::create($validated);

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Calificación registrada exitosamente');
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade)
    {
        $this->authorize('view', $grade);

        return view('teacher.grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        $this->authorize('update', $grade);

        $teacher = auth()->user();
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->get();

        $students = Student::whereHas('schoolGrade', function ($query) use ($subjects) {
            $query->whereIn('grade_level', $subjects->pluck('grade_level')->unique());
        })
            ->with('user', 'schoolGrade')
            ->orderBy('user_id')
            ->get();

        return view('teacher.grades.edit', compact('grade', 'subjects', 'students'));
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(StoreGradeRequest $request, Grade $grade)
    {
        $this->authorize('update', $grade);

        $validated = $request->validated();
        $grade->update($validated);

        return redirect()->route('teacher.grades.show', $grade)
            ->with('success', 'Calificación actualizada exitosamente');
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(Grade $grade)
    {
        $this->authorize('delete', $grade);

        $grade->delete();

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Calificación eliminada exitosamente');
    }
}
