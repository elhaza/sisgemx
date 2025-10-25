<?php

namespace App\Http\Controllers\Admin;

use App\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['subject', 'subject.teacher', 'schoolGrade', 'schoolGrade.schoolYear'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $subjects = Subject::with('teacher')->get();
        $schoolGrades = SchoolGrade::with('schoolYear')->get();
        $daysOfWeek = DayOfWeek::cases();

        return view('admin.schedules.create', compact('activeSchoolYear', 'subjects', 'schoolGrades', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_grade_id' => 'required|exists:school_grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Horario creado exitosamente.');
    }

    public function edit(Schedule $schedule)
    {
        $subjects = Subject::with('teacher')->get();
        $schoolGrades = SchoolGrade::with('schoolYear')->get();
        $daysOfWeek = DayOfWeek::cases();

        return view('admin.schedules.edit', compact('schedule', 'subjects', 'schoolGrades', 'daysOfWeek'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'school_grade_id' => 'required|exists:school_grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Horario actualizado exitosamente.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Horario eliminado exitosamente.');
    }

    public function visual()
    {
        $schoolYears = SchoolYear::all();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $subjects = Subject::with('teacher')->where('school_year_id', $activeSchoolYear?->id)->get();
        $schoolGrades = SchoolGrade::with('schoolYear')->get();

        return view('admin.schedules.visual', compact('schoolYears', 'activeSchoolYear', 'subjects', 'schoolGrades'));
    }

    public function copyForm()
    {
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();

        return view('admin.schedules.copy', compact('schoolYears'));
    }

    public function copy(Request $request)
    {
        $validated = $request->validate([
            'source_school_year_id' => 'required|exists:school_years,id',
            'target_school_year_id' => 'required|exists:school_years,id|different:source_school_year_id',
            'create_missing_grades' => 'nullable|boolean',
            'create_missing_subjects' => 'nullable|boolean',
        ]);

        $sourceSchoolYear = SchoolYear::findOrFail($validated['source_school_year_id']);
        $targetSchoolYear = SchoolYear::findOrFail($validated['target_school_year_id']);

        // Validate that target school year is after source school year
        if ($targetSchoolYear->start_date <= $sourceSchoolYear->start_date) {
            return redirect()->back()->with('error', 'Solo se puede copiar de un ciclo escolar anterior hacia uno nuevo. El ciclo destino debe ser posterior al ciclo origen.');
        }

        // Check if target school year already has schedules
        $existingSchedulesCount = Schedule::whereHas('schoolGrade', function ($query) use ($targetSchoolYear) {
            $query->where('school_year_id', $targetSchoolYear->id);
        })->count();

        if ($existingSchedulesCount > 0) {
            return redirect()->back()->with('error', 'El ciclo escolar destino ya tiene horarios asignados.');
        }

        // Get all school grades from source school year
        $sourceSchoolGrades = SchoolGrade::where('school_year_id', $sourceSchoolYear->id)->get();

        $copiedCount = 0;
        $createdGradesCount = 0;
        $createdSubjectsCount = 0;
        $errors = [];

        foreach ($sourceSchoolGrades as $sourceGrade) {
            // Find matching school grade in target school year by level and section
            $targetGrade = SchoolGrade::where('school_year_id', $targetSchoolYear->id)
                ->where('level', $sourceGrade->level)
                ->where('section', $sourceGrade->section)
                ->first();

            if (! $targetGrade) {
                // Create grade if checkbox is enabled
                if ($request->has('create_missing_grades') && $request->create_missing_grades) {
                    $targetGrade = SchoolGrade::create([
                        'school_year_id' => $targetSchoolYear->id,
                        'level' => $sourceGrade->level,
                        'section' => $sourceGrade->section,
                        'name' => $sourceGrade->name,
                    ]);
                    $createdGradesCount++;
                } else {
                    $errors[] = "No se encontró grupo equivalente para {$sourceGrade->name} {$sourceGrade->section}";

                    continue;
                }
            }

            // Copy all schedules from source grade to target grade
            $schedules = Schedule::where('school_grade_id', $sourceGrade->id)->get();

            foreach ($schedules as $schedule) {
                // Check if subject exists for the new school year
                $targetSubject = Subject::where('school_year_id', $targetSchoolYear->id)
                    ->where('name', $schedule->subject->name)
                    ->where('teacher_id', $schedule->subject->teacher_id)
                    ->first();

                if (! $targetSubject) {
                    // Try to find the subject by name only
                    $targetSubject = Subject::where('school_year_id', $targetSchoolYear->id)
                        ->where('name', $schedule->subject->name)
                        ->first();
                }

                if (! $targetSubject) {
                    // Create subject if checkbox is enabled
                    if ($request->has('create_missing_subjects') && $request->create_missing_subjects) {
                        $targetSubject = Subject::create([
                            'school_year_id' => $targetSchoolYear->id,
                            'name' => $schedule->subject->name,
                            'teacher_id' => $schedule->subject->teacher_id,
                            'description' => $schedule->subject->description,
                        ]);
                        $createdSubjectsCount++;
                    } else {
                        continue;
                    }
                }

                Schedule::create([
                    'school_grade_id' => $targetGrade->id,
                    'subject_id' => $targetSubject->id,
                    'day_of_week' => $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'classroom' => $schedule->classroom,
                ]);

                $copiedCount++;
            }
        }

        if ($copiedCount > 0) {
            $message = "Se copiaron {$copiedCount} horarios exitosamente.";
            if ($createdGradesCount > 0) {
                $message .= " Se crearon {$createdGradesCount} grupos.";
            }
            if ($createdSubjectsCount > 0) {
                $message .= " Se crearon {$createdSubjectsCount} materias.";
            }
            if (! empty($errors)) {
                $message .= ' Advertencias: '.implode(', ', $errors);
            }

            return redirect()->route('admin.schedules.visual')->with('success', $message);
        }

        return redirect()->back()->with('error', 'No se pudieron copiar los horarios. Verifica que los grupos y materias existan en el ciclo destino o activa las opciones de auto-creación.');
    }

    public function getGroupSchedule(Request $request)
    {
        $query = Schedule::with(['subject.teacher', 'schoolGrade', 'schoolGrade.schoolYear']);

        if ($request->filled('school_grade_id')) {
            $query->where('school_grade_id', $request->school_grade_id);
        } elseif ($request->filled('school_year_id')) {
            $query->whereHas('schoolGrade', function ($q) use ($request) {
                $q->where('school_year_id', $request->school_year_id);
            });
        }

        $schedules = $query->get();

        return response()->json($schedules);
    }

    public function storeVisual(Request $request)
    {
        $validated = $request->validate([
            'school_grade_id' => 'required|exists:school_grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
        ]);

        // Check for teacher conflicts
        $subject = Subject::findOrFail($validated['subject_id']);
        $schoolGrade = SchoolGrade::findOrFail($validated['school_grade_id']);

        // Add teacher_id from subject
        $validated['teacher_id'] = $subject->teacher_id;

        $teacherConflict = $this->checkTeacherConflict(
            $subject->teacher_id,
            $schoolGrade->school_year_id,
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['school_grade_id']
        );

        if ($teacherConflict) {
            return response()->json([
                'success' => false,
                'message' => 'El maestro ya tiene una clase asignada en este horario.',
            ], 422);
        }

        // Check for schedule conflicts for the same group
        $groupConflict = $this->checkGroupConflict(
            $validated['school_grade_id'],
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time']
        );

        if ($groupConflict) {
            return response()->json([
                'success' => false,
                'message' => 'El grupo ya tiene una clase asignada en este horario.',
            ], 422);
        }

        $schedule = Schedule::create($validated);

        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['subject.teacher', 'schoolGrade', 'schoolGrade.schoolYear']),
        ]);
    }

    public function updateVisual(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
        ]);

        // Check for teacher conflicts
        $teacherConflict = $this->checkTeacherConflict(
            $schedule->subject->teacher_id,
            $schedule->schoolGrade->school_year_id,
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->school_grade_id,
            $schedule->id
        );

        if ($teacherConflict) {
            return response()->json([
                'success' => false,
                'message' => 'El maestro ya tiene una clase asignada en este horario.',
            ], 422);
        }

        // Check for schedule conflicts for the same group
        $groupConflict = $this->checkGroupConflict(
            $schedule->school_grade_id,
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->id
        );

        if ($groupConflict) {
            return response()->json([
                'success' => false,
                'message' => 'El grupo ya tiene una clase asignada en este horario.',
            ], 422);
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['subject.teacher', 'schoolGrade', 'schoolGrade.schoolYear']),
        ]);
    }

    public function destroyVisual(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Horario eliminado exitosamente.',
        ]);
    }

    private function checkTeacherConflict($teacherId, $schoolYearId, $dayOfWeek, $startTime, $endTime, $schoolGradeId, $excludeScheduleId = null)
    {
        $query = Schedule::whereHas('subject', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
            ->whereHas('schoolGrade', function ($q) use ($schoolYearId) {
                $q->where('school_year_id', $schoolYearId);
            })
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->where('school_grade_id', '!=', $schoolGradeId);

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query->exists();
    }

    private function checkGroupConflict($schoolGradeId, $dayOfWeek, $startTime, $endTime, $excludeScheduleId = null)
    {
        $query = Schedule::where('school_grade_id', $schoolGradeId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startTime, $endTime) {
                // Check if new schedule overlaps with existing schedules
                $q->where(function ($q2) use ($startTime) {
                    // New schedule starts during existing schedule
                    $q2->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })
                    ->orWhere(function ($q2) use ($endTime) {
                        // New schedule ends during existing schedule
                        $q2->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        // New schedule completely contains existing schedule
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    });
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query->exists();
    }
}
