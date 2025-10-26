<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['teacher', 'schoolYear']);

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by subject name
        if ($request->filled('subject_name')) {
            $query->where('name', 'like', '%'.$request->subject_name.'%');
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        $subjects = $query->latest()->paginate(15);

        $teachers = User::where('role', 'teacher')->get();
        $schoolYears = SchoolYear::all();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $gradeLevels = [1, 2, 3, 4, 5, 6];
        $subjectList = Subject::selectRaw('MIN(id) as id, name')
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        // Calculate teacher hours
        $teacherHours = [];
        foreach ($teachers as $teacher) {
            $totalHours = Subject::where('teacher_id', $teacher->id)
                ->whereNotNull('default_hours_per_week')
                ->sum('default_hours_per_week');

            $teacherHours[$teacher->id] = [
                'name' => $teacher->full_name,
                'hours' => $totalHours,
                'max_hours' => $teacher->max_hours_per_week ?? 40,
            ];
        }

        return view('admin.subjects.index', compact('subjects', 'teachers', 'schoolYears', 'activeSchoolYear', 'gradeLevels', 'subjectList', 'teacherHours'));
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
            'default_hours_per_week' => 'nullable|numeric|min:0.5|max:40',
        ]);

        // Validar que no exista duplicado (mismo maestro, misma materia, mismo grade_level)
        $existing = Subject::where('teacher_id', $validated['teacher_id'])
            ->where('name', $validated['name'])
            ->where('grade_level', $validated['grade_level'])
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este maestro ya tiene esta materia asignada para este grado.',
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Este maestro ya tiene esta materia asignada para este grado.']);
        }

        // Validar horas máximas diarias del maestro
        $teacher = User::find($validated['teacher_id']);
        $currentWeeklyHours = Subject::where('teacher_id', $teacher->id)
            ->whereNotNull('default_hours_per_week')
            ->sum('default_hours_per_week');

        $newWeeklyHours = $currentWeeklyHours + ($validated['default_hours_per_week'] ?? 0);
        $dailyAverage = $newWeeklyHours / 5; // Asumiendo 5 días de clase por semana

        // Verificar si se supera el máximo de horas diarias
        if ($dailyAverage > $teacher->max_hours_per_day && !$request->input('force')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'warning' => true,
                    'message' => 'El maestro supera el máximo de horas diarias permitidas.',
                    'teacher_name' => $teacher->full_name,
                    'current_daily_average' => round($dailyAverage, 2),
                    'max_hours_per_day' => $teacher->max_hours_per_day,
                    'total_weekly_hours' => round($newWeeklyHours, 2),
                ], 422);
            }
        }

        $subject = Subject::create($validated);

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Materia creada exitosamente.',
                'id' => $subject->id,
                'name' => $subject->name,
            ]);
        }

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

    public function storeCatalogSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        $subject = Subject::create([
            'name' => $validated['name'],
            'description' => null,
            'teacher_id' => null,
            'grade_level' => null,
            'school_year_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Materia creada exitosamente.',
            'subject' => [
                'id' => $subject->id,
                'name' => $subject->name,
            ],
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'max_hours_per_day' => 'nullable|numeric|min:1|max:12',
            'max_hours_per_week' => 'nullable|numeric|min:1|max:60',
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'max_hours_per_day' => $validated['max_hours_per_day'] ?? 8,
            'max_hours_per_week' => $validated['max_hours_per_week'] ?? 40,
            'role' => 'teacher',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Docente creado exitosamente.',
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->full_name,
            ],
        ]);
    }
}
