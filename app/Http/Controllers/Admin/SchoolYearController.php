<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSection;
use App\Models\MonthlyTuition;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\Subject;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::latest()->paginate(15);

        return view('admin.school-years.index', compact('schoolYears'));
    }

    public function create()
    {
        return view('admin.school-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'monthly_tuitions' => 'required|array',
            'monthly_tuitions.*.year' => 'required|integer',
            'monthly_tuitions.*.month' => 'required|integer|min:1|max:12',
            'monthly_tuitions.*.amount' => 'required|numeric|min:0',
            'copy_groups' => 'boolean',
            'copy_subjects' => 'boolean',
            'copy_schedules' => 'boolean',
            'copy_students' => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        $schoolYear = SchoolYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        // Get previous school year
        $previousSchoolYear = SchoolYear::where('id', '!=', $schoolYear->id)
            ->orderBy('start_date', 'desc')
            ->first();

        // Copy groups if requested
        if ($request->boolean('copy_groups') && $previousSchoolYear) {
            $this->copyGroups($previousSchoolYear, $schoolYear);
        }

        // Copy subjects if requested
        if ($request->boolean('copy_subjects') && $previousSchoolYear) {
            $this->copySubjects($previousSchoolYear, $schoolYear);
        }

        // Copy schedules if requested
        if ($request->boolean('copy_schedules') && $previousSchoolYear) {
            $this->copySchedules($previousSchoolYear, $schoolYear);
        }

        // Create monthly tuitions
        foreach ($validated['monthly_tuitions'] as $monthlyData) {
            MonthlyTuition::create([
                'school_year_id' => $schoolYear->id,
                'year' => $monthlyData['year'],
                'month' => $monthlyData['month'],
                'amount' => $monthlyData['amount'],
            ]);
        }

        // If copy_students is checked, redirect to student assignment page
        if ($request->boolean('copy_students') && $previousSchoolYear) {
            session()->flash('success', 'Ciclo escolar creado exitosamente. Ahora asigne los estudiantes.');

            return redirect()->route('admin.school-years.assign-students', $schoolYear);
        }

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar creado exitosamente.');
    }

    protected function copyGroups(SchoolYear $source, SchoolYear $target): void
    {
        $sourceGrades = GradeSection::where('school_year_id', $source->id)->get();

        foreach ($sourceGrades as $grade) {
            GradeSection::create([
                'school_year_id' => $target->id,
                'grade_level' => $grade->grade_level,
                'section' => $grade->section,
            ]);
        }
    }

    protected function copySubjects(SchoolYear $source, SchoolYear $target): void
    {
        $sourceSubjects = Subject::where('school_year_id', $source->id)->get();

        foreach ($sourceSubjects as $subject) {
            Subject::create([
                'school_year_id' => $target->id,
                'name' => $subject->name,
                'description' => $subject->description,
                'teacher_id' => $subject->teacher_id,
                'grade_level' => $subject->grade_level,
            ]);
        }
    }

    protected function copySchedules(SchoolYear $source, SchoolYear $target): void
    {
        $sourceSchedules = Schedule::whereHas('schoolGrade', function ($query) use ($source) {
            $query->where('school_year_id', $source->id);
        })->get();

        foreach ($sourceSchedules as $schedule) {
            // Find corresponding school grade in target year
            $targetGrade = GradeSection::where('school_year_id', $target->id)
                ->where('grade_level', $schedule->schoolGrade->grade_level)
                ->where('section', $schedule->schoolGrade->section)
                ->first();

            // Find corresponding subject in target year
            $targetSubject = Subject::where('school_year_id', $target->id)
                ->where('name', $schedule->subject->name)
                ->where('teacher_id', $schedule->subject->teacher_id)
                ->first();

            if ($targetGrade && $targetSubject) {
                Schedule::create([
                    'school_grade_id' => $targetGrade->id,
                    'subject_id' => $targetSubject->id,
                    'day_of_week' => $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ]);
            }
        }
    }

    public function assignStudents(SchoolYear $schoolYear)
    {
        // Get previous school year
        $previousSchoolYear = SchoolYear::where('id', '!=', $schoolYear->id)
            ->orderBy('start_date', 'desc')
            ->first();

        if (! $previousSchoolYear) {
            return redirect()->route('admin.school-years.index')
                ->with('error', 'No hay ciclo escolar anterior para copiar estudiantes.');
        }

        // Get active students from previous school year
        $students = Student::where('students.school_year_id', $previousSchoolYear->id)
            ->where('students.status', 'active')
            ->with(['user', 'schoolGrade'])
            ->join('grade_sections', 'students.school_grade_id', '=', 'grade_sections.id')
            ->orderBy('grade_sections.grade_level')
            ->orderBy('grade_sections.section')
            ->select('students.*')
            ->get();

        // Get target grades for the new school year
        $targetGrades = GradeSection::where('school_year_id', $schoolYear->id)
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();

        // Get maximum level available in target grades
        $maxLevelAvailable = $targetGrades->max('grade_level') ?? 0;

        // Separate students into those who can advance and those who completed
        $studentsToAdvance = collect();
        $studentsCompleted = collect();

        foreach ($students as $student) {
            $currentLevel = $student->schoolGrade->grade_level ?? 0;
            $nextLevel = $currentLevel + 1;

            if ($nextLevel > $maxLevelAvailable) {
                // Student is at max level and has no next level
                $studentsCompleted->push($student);
            } else {
                $studentsToAdvance->push($student);
            }
        }

        // Distribute students evenly among groups of same level
        $studentAssignments = $this->distributeStudentsEvenly($studentsToAdvance, $targetGrades);

        return view('admin.school-years.assign-students', compact(
            'schoolYear',
            'previousSchoolYear',
            'studentsToAdvance',
            'studentsCompleted',
            'targetGrades',
            'studentAssignments'
        ));
    }

    protected function distributeStudentsEvenly($students, $targetGrades)
    {
        $assignments = [];
        $levelGroups = [];

        // Group target grades by level
        foreach ($targetGrades as $grade) {
            $levelGroups[$grade->grade_level][] = $grade;
        }

        // Group students by their current level
        $studentsByLevel = [];
        foreach ($students as $student) {
            $currentLevel = $student->schoolGrade->grade_level ?? 0;
            $studentsByLevel[$currentLevel][] = $student;
        }

        // Assign students evenly to next level groups
        foreach ($studentsByLevel as $currentLevel => $levelStudents) {
            $nextLevel = $currentLevel + 1;

            if (! isset($levelGroups[$nextLevel])) {
                continue;
            }

            $nextLevelGrades = $levelGroups[$nextLevel];
            $studentsPerGroup = ceil(count($levelStudents) / count($nextLevelGrades));
            $gradeIndex = 0;
            $countInCurrentGrade = 0;

            foreach ($levelStudents as $student) {
                if ($countInCurrentGrade >= $studentsPerGroup && $gradeIndex < count($nextLevelGrades) - 1) {
                    $gradeIndex++;
                    $countInCurrentGrade = 0;
                }

                $assignments[$student->id] = $nextLevelGrades[$gradeIndex]->id;
                $countInCurrentGrade++;
            }
        }

        return $assignments;
    }

    public function storeStudentAssignments(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'students' => 'array',
            'students.*' => 'exists:students,id',
            'assignments' => 'array',
            'assignments.*' => 'exists:school_grades,id',
            'graduated_students' => 'array',
            'graduated_students.*' => 'exists:students,id',
        ]);

        $assignedCount = 0;
        $graduatedCount = 0;

        // Handle students to advance
        if (! empty($validated['students'])) {
            $monthlyTuitions = MonthlyTuition::where('school_year_id', $schoolYear->id)->get();

            foreach ($validated['students'] as $studentId) {
                $gradeId = $validated['assignments'][$studentId];
                $grade = GradeSection::find($gradeId);

                // Update student school year and grade
                $student = Student::find($studentId);
                $student->update([
                    'school_year_id' => $schoolYear->id,
                    'school_grade_id' => $gradeId,
                    'status' => 'active',
                ]);

                // Create student tuitions for all months (without discount)
                foreach ($monthlyTuitions as $monthlyTuition) {
                    StudentTuition::create([
                        'student_id' => $studentId,
                        'school_year_id' => $schoolYear->id,
                        'monthly_tuition_id' => $monthlyTuition->id,
                        'year' => $monthlyTuition->year,
                        'month' => $monthlyTuition->month,
                        'monthly_amount' => $monthlyTuition->amount,
                        'discount_percentage' => 0,
                        'final_amount' => $monthlyTuition->amount,
                    ]);
                }

                $assignedCount++;
            }
        }

        // Handle students to graduate
        if (! empty($validated['graduated_students'])) {
            foreach ($validated['graduated_students'] as $studentId) {
                $student = Student::find($studentId);
                $student->update([
                    'status' => 'graduated',
                ]);

                $graduatedCount++;
            }
        }

        $message = [];
        if ($assignedCount > 0) {
            $message[] = "$assignedCount estudiante(s) asignado(s) al nuevo ciclo escolar";
        }
        if ($graduatedCount > 0) {
            $message[] = "$graduatedCount estudiante(s) marcado(s) como graduado(s)";
        }

        return redirect()->route('admin.school-years.index')
            ->with('success', implode(' y ', $message).'.');
    }

    public function edit(SchoolYear $schoolYear)
    {
        // Generate all months between start_date and end_date
        $start = $schoolYear->start_date;
        $end = $schoolYear->end_date;

        $months = [];
        $current = $start->copy()->startOfMonth();

        while ($current <= $end) {
            $months[] = [
                'year' => $current->year,
                'month' => $current->month,
            ];
            $current->addMonth();
        }

        // Load existing monthly tuitions
        $existingTuitions = MonthlyTuition::where('school_year_id', $schoolYear->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.$item->month;
            });

        // Merge with generated months
        $monthlyTuitions = collect($months)->map(function ($monthData) use ($existingTuitions) {
            $key = $monthData['year'].'-'.$monthData['month'];
            $existing = $existingTuitions[$key] ?? null;

            return [
                'id' => $existing?->id,
                'year' => $monthData['year'],
                'month' => $monthData['month'],
                'amount' => $existing?->amount ?? 0,
            ];
        });

        return view('admin.school-years.edit', compact('schoolYear', 'monthlyTuitions'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'monthly_tuitions' => 'required|array',
            'monthly_tuitions.*.id' => 'nullable|exists:monthly_tuitions,id',
            'monthly_tuitions.*.year' => 'required|integer',
            'monthly_tuitions.*.month' => 'required|integer|min:1|max:12',
            'monthly_tuitions.*.amount' => 'required|numeric|min:0',
        ]);

        // Ensure is_active is set even when checkbox is unchecked
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active'] && ! $schoolYear->is_active) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        // Update school year basic info
        $schoolYear->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'],
        ]);

        // Update monthly tuitions
        foreach ($validated['monthly_tuitions'] as $monthlyData) {
            if (! empty($monthlyData['id'])) {
                // Update existing monthly tuition
                MonthlyTuition::findOrFail($monthlyData['id'])->update([
                    'amount' => $monthlyData['amount'],
                ]);
            } else {
                // Create new monthly tuition if it doesn't exist
                MonthlyTuition::firstOrCreate(
                    [
                        'school_year_id' => $schoolYear->id,
                        'year' => $monthlyData['year'],
                        'month' => $monthlyData['month'],
                    ],
                    ['amount' => $monthlyData['amount']]
                );
            }
        }

        // Create/update student tuitions for all students in this school year
        $this->createStudentTuitions($schoolYear);

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar actualizado exitosamente. Las colegiaturas de los estudiantes han sido creadas/actualizadas.');
    }

    public function destroy(Request $request, SchoolYear $schoolYear)
    {
        // Validate the special token
        $request->validate([
            'confirmation_token' => 'required|string',
        ]);

        $expectedToken = env('TOKEN_SPECIAL_COMMANDS', 'DELETE2025');

        if ($request->confirmation_token !== $expectedToken) {
            return redirect()->route('admin.school-years.index')->with('error', 'Token de confirmación incorrecto. No se puede eliminar el ciclo escolar.');
        }

        if ($schoolYear->is_active) {
            return redirect()->route('admin.school-years.index')->with('error', 'No puedes eliminar el ciclo escolar activo.');
        }

        $schoolYear->delete();

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar eliminado exitosamente.');
    }

    public function getSchoolGrades(SchoolYear $schoolYear)
    {
        $schoolGrades = $schoolYear->gradeSections()
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get(['id', 'grade_level', 'section', 'school_year_id']);

        return response()->json($schoolGrades);
    }

    /**
     * Create student tuitions for all active students in a school year
     */
    protected function createStudentTuitions(SchoolYear $schoolYear): void
    {
        // Get all active students in this school year
        $students = Student::where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->get();

        // Get all monthly tuitions for this school year
        $monthlyTuitions = MonthlyTuition::where('school_year_id', $schoolYear->id)
            ->get();

        // Create StudentTuition for each student and month if it doesn't exist
        foreach ($students as $student) {
            foreach ($monthlyTuitions as $monthlyTuition) {
                StudentTuition::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'school_year_id' => $schoolYear->id,
                        'year' => $monthlyTuition->year,
                        'month' => $monthlyTuition->month,
                    ],
                    [
                        'monthly_tuition_id' => $monthlyTuition->id,
                        'monthly_amount' => $monthlyTuition->amount,
                        'discount_percentage' => 0,
                        'final_amount' => $monthlyTuition->amount,
                        'due_date' => \App\Helpers\PaymentHelper::calculateDueDate($monthlyTuition->year, $monthlyTuition->month),
                    ]
                );
            }
        }
    }
}
