<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\GradeSection;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\SubjectRequirement;
use App\Models\TeacherAvailability;
use App\Models\TeacherSubject;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Collection;

class ScheduleGenerationService
{
    private array $schedule = [];

    private array $teacherHours = [];

    private array $groupHours = [];

    private array $conflicts = [];

    private array $assignments = [];

    private int $assignmentsMade = 0;

    public function generate(SchoolYear $schoolYear, ?GradeSection $gradeSection = null): array
    {
        $this->schedule = [];
        $this->teacherHours = [];
        $this->groupHours = [];
        $this->conflicts = [];
        $this->assignments = [];
        $this->assignmentsMade = 0;

        // Obtener datos
        $gradeSections = $gradeSection ? collect([$gradeSection]) : $schoolYear->gradeSections;
        $subjects = Subject::where('school_year_id', $schoolYear->id)->get();
        $timeSlots = TimeSlot::orderBy('day_of_week')->orderBy('start_time')->get();
        $teachers = User::where('role', 'teacher')->get();
        $classrooms = Classroom::all();

        if ($gradeSections->isEmpty() || $subjects->isEmpty() || $timeSlots->isEmpty()) {
            $this->conflicts[] = 'Datos insuficientes: faltan grupos, materias o franjas horarias.';

            return $this->buildResponse($gradeSections);
        }

        // Inicializar tracking de horas
        foreach ($teachers as $teacher) {
            $this->teacherHours[$teacher->id] = [
                'name' => $teacher->full_name,
                'daily' => [],
                'weekly' => 0,
                'max_daily' => $teacher->max_hours_per_day ?? 8,
                'max_weekly' => $teacher->max_hours_per_week ?? 40,
            ];
        }

        foreach ($gradeSections as $section) {
            $this->groupHours[$section->id] = [];
        }

        // Obtener requisitos de materias y priorizar
        $requirements = $this->getRequirements($gradeSections, $subjects);
        $sortedRequirements = $this->prioritizeRequirements($requirements, $teachers);

        // Intentar asignar cada materia
        foreach ($sortedRequirements as $req) {
            $this->assignSubjectToGroup($req, $teachers, $timeSlots, $classrooms);
        }

        return $this->buildResponse($gradeSections);
    }

    private function getRequirements(Collection $gradeSections, Collection $subjects): array
    {
        $requirements = [];

        foreach ($gradeSections as $section) {
            foreach ($subjects->filter(fn ($s) => $s->grade_level === $section->grade_level) as $subject) {
                $requirement = SubjectRequirement::where('subject_id', $subject->id)
                    ->where('grade_section_id', $section->id)
                    ->first();

                $hoursPerWeek = $requirement?->hours_per_week ?? $subject->default_hours_per_week ?? 3;
                $minConsecutive = $requirement?->min_consecutive_minutes;

                $requirements[] = [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'grade_section_id' => $section->id,
                    'section_name' => $section->name,
                    'hours_per_week' => $hoursPerWeek,
                    'min_consecutive_minutes' => $minConsecutive,
                    'teacher_id' => $subject->teacher_id,
                    'teacher_name' => $subject->teacher->full_name ?? 'Sin asignar',
                ];
            }
        }

        return $requirements;
    }

    private function prioritizeRequirements(array $requirements, Collection $teachers): array
    {
        usort($requirements, function ($a, $b) {
            // Prioridad 1: más horas semanales
            if ($a['hours_per_week'] !== $b['hours_per_week']) {
                return $b['hours_per_week'] <=> $a['hours_per_week'];
            }

            // Prioridad 2: menos docentes disponibles
            $teachersA = TeacherSubject::where('subject_id', $a['subject_id'])->count();
            $teachersB = TeacherSubject::where('subject_id', $b['subject_id'])->count();

            return $teachersA <=> $teachersB;
        });

        return $requirements;
    }

    private function assignSubjectToGroup(array $requirement, Collection $teachers, Collection $timeSlots, Collection $classrooms): void
    {
        $hoursNeeded = $requirement['hours_per_week'];
        $sessionsNeeded = [];

        // Calcular duración de sesiones
        foreach ($timeSlots as $slot) {
            if ($hoursNeeded <= 0) {
                break;
            }

            $slotMinutes = $slot->duration_minutes;
            $slotHours = $slotMinutes / 60;

            if ($slotHours <= $hoursNeeded) {
                $sessionsNeeded[] = $slot->id;
                $hoursNeeded -= $slotHours;
            }
        }

        // Buscar docente disponible
        $assignedTeacher = $this->findAvailableTeacher(
            $requirement['subject_id'],
            $sessionsNeeded,
            $teachers,
            $timeSlots
        );

        if (! $assignedTeacher) {
            $this->conflicts[] = "{$requirement['subject_name']} ({$requirement['section_name']}): No hay docente disponible.";

            return;
        }

        // Asignar sesiones
        foreach ($sessionsNeeded as $slotId) {
            $slot = $timeSlots->find($slotId);
            $classroom = $classrooms->first();

            $assignment = [
                'subject_id' => $requirement['subject_id'],
                'subject_name' => $requirement['subject_name'],
                'grade_section_id' => $requirement['grade_section_id'],
                'section_name' => $requirement['section_name'],
                'teacher_id' => $assignedTeacher->id,
                'teacher_name' => $assignedTeacher->full_name,
                'day_of_week' => $slot->day_of_week,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'duration_minutes' => $slot->duration_minutes,
                'classroom_id' => $classroom->id ?? null,
                'classroom_code' => $classroom->code ?? 'N/A',
            ];

            $this->assignments[] = $assignment;
            $this->assignmentsMade++;

            // Actualizar tracking de horas
            $slotHours = $slot->duration_minutes / 60;
            $this->teacherHours[$assignedTeacher->id]['weekly'] += $slotHours;

            if (! isset($this->teacherHours[$assignedTeacher->id]['daily'][$slot->day_of_week])) {
                $this->teacherHours[$assignedTeacher->id]['daily'][$slot->day_of_week] = 0;
            }
            $this->teacherHours[$assignedTeacher->id]['daily'][$slot->day_of_week] += $slotHours;
        }
    }

    private function findAvailableTeacher(int $subjectId, array $timeSlotIds, Collection $teachers, Collection $timeSlots): ?User
    {
        // Obtener docentes que pueden enseñar esta materia
        $eligibleTeachers = TeacherSubject::where('subject_id', $subjectId)
            ->with('teacher')
            ->get()
            ->pluck('teacher')
            ->filter(fn ($t) => $t !== null);

        foreach ($eligibleTeachers as $teacher) {
            $canAssign = true;

            foreach ($timeSlotIds as $slotId) {
                $slot = $timeSlots->find($slotId);

                // Verificar disponibilidad
                $available = TeacherAvailability::where('teacher_id', $teacher->id)
                    ->where('day_of_week', $slot->day_of_week)
                    ->where('start_time', '<=', $slot->start_time)
                    ->where('end_time', '>=', $slot->end_time)
                    ->exists();

                if (! $available) {
                    $canAssign = false;

                    break;
                }

                // Verificar límite diario
                $slotHours = $slot->duration_minutes / 60;
                $dailyHours = $this->teacherHours[$teacher->id]['daily'][$slot->day_of_week] ?? 0;

                if ($dailyHours + $slotHours > $this->teacherHours[$teacher->id]['max_daily']) {
                    $canAssign = false;

                    break;
                }

                // Verificar límite semanal
                $weeklyHours = $this->teacherHours[$teacher->id]['weekly'];
                if ($weeklyHours + $slotHours > $this->teacherHours[$teacher->id]['max_weekly']) {
                    $canAssign = false;

                    break;
                }

                // Verificar que no hay conflicto de horario
                $conflict = collect($this->assignments)->filter(function ($assignment) use ($teacher, $slot) {
                    return $assignment['teacher_id'] === $teacher->id
                        && $assignment['day_of_week'] === $slot->day_of_week
                        && $this->timesOverlap($assignment['start_time'], $assignment['end_time'], $slot->start_time, $slot->end_time);
                })->isNotEmpty();

                if ($conflict) {
                    $canAssign = false;

                    break;
                }
            }

            if ($canAssign) {
                return $teacher;
            }
        }

        return null;
    }

    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $s1 = strtotime($start1);
        $e1 = strtotime($end1);
        $s2 = strtotime($start2);
        $e2 = strtotime($end2);

        return $s1 < $e2 && $s2 < $e1;
    }

    private function buildResponse(Collection $gradeSections): array
    {
        $preview = $this->buildPreview($gradeSections);

        $missingAssignments = $this->calculateMissingAssignments($gradeSections);

        return [
            'preview' => $preview,
            'conflicts' => $this->conflicts,
            'summary' => [
                'assignments_made' => $this->assignmentsMade,
                'assignments_missing' => $missingAssignments,
                'teacher_loads' => array_values($this->teacherHours),
                'suggestions' => $this->generateSuggestions(),
            ],
            'assignments' => $this->assignments,
            'explanation' => 'Algoritmo Greedy con priorización por horas semanales y disponibilidad de docentes.',
        ];
    }

    private function buildPreview(Collection $gradeSections): array
    {
        $preview = [];

        foreach ($gradeSections as $section) {
            $sectionAssignments = collect($this->assignments)
                ->filter(fn ($a) => $a['grade_section_id'] === $section->id)
                ->groupBy('day_of_week');

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            $dayNames = [
                'monday' => 'Lunes',
                'tuesday' => 'Martes',
                'wednesday' => 'Miércoles',
                'thursday' => 'Jueves',
                'friday' => 'Viernes',
            ];

            $preview[] = [
                'section_id' => $section->id,
                'section_name' => $section->name,
                'schedule' => array_map(function ($day) use ($sectionAssignments, $dayNames) {
                    return [
                        'day' => $dayNames[$day],
                        'day_code' => $day,
                        'assignments' => collect($sectionAssignments->get($day, []))->values()->toArray(),
                    ];
                }, $days),
            ];
        }

        return $preview;
    }

    private function calculateMissingAssignments(Collection $gradeSections): int
    {
        $total = 0;

        foreach ($gradeSections as $section) {
            $sectionSubjects = Subject::where('grade_level', $section->grade_level)->count();
            $sectionAssignments = collect($this->assignments)
                ->filter(fn ($a) => $a['grade_section_id'] === $section->id)
                ->groupBy('subject_id')
                ->count();

            $total += max(0, $sectionSubjects - $sectionAssignments);
        }

        return $total;
    }

    private function generateSuggestions(): array
    {
        $suggestions = [];

        foreach ($this->teacherHours as $teacherHours) {
            $utilization = ($teacherHours['weekly'] / $teacherHours['max_weekly']) * 100;

            if ($utilization < 50) {
                $suggestions[] = "El docente {$teacherHours['name']} tiene baja carga ({$teacherHours['weekly']}h de {$teacherHours['max_weekly']}h). Considere aumentar asignaciones.";
            } elseif ($utilization > 90) {
                $suggestions[] = "El docente {$teacherHours['name']} está al máximo de su capacidad ({$teacherHours['weekly']}h de {$teacherHours['max_weekly']}h).";
            }
        }

        return $suggestions;
    }
}
