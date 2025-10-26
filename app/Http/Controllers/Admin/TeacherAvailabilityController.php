<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAvailability;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherAvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = TeacherAvailability::with('teacher')
            ->orderBy('teacher_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);

        $dayNames = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.teacher-availabilities.index', compact('availabilities', 'dayNames'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.teacher-availabilities.create', compact('teachers', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'note' => 'nullable|string|max:255',
        ]);

        TeacherAvailability::create($validated);

        return redirect()->route('admin.teacher-availabilities.index')
            ->with('success', 'Disponibilidad de docente creada exitosamente.');
    }

    public function edit(TeacherAvailability $teacherAvailability)
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.teacher-availabilities.edit', compact('teacherAvailability', 'teachers', 'daysOfWeek'));
    }

    public function update(Request $request, TeacherAvailability $teacherAvailability)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'note' => 'nullable|string|max:255',
        ]);

        $teacherAvailability->update($validated);

        return redirect()->route('admin.teacher-availabilities.index')
            ->with('success', 'Disponibilidad de docente actualizada exitosamente.');
    }

    public function destroy(TeacherAvailability $teacherAvailability)
    {
        $teacherAvailability->delete();

        return redirect()->route('admin.teacher-availabilities.index')
            ->with('success', 'Disponibilidad de docente eliminada exitosamente.');
    }
}
