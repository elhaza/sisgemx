<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = TimeSlot::orderBy('day_of_week')->orderBy('start_time')->paginate(20);

        $dayNames = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.time-slots.index', compact('timeSlots', 'dayNames'));
    }

    public function create()
    {
        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.time-slots.create', compact('daysOfWeek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'days_of_week.required' => 'Debes seleccionar al menos un día de la semana.',
            'days_of_week.min' => 'Debes seleccionar al menos un día de la semana.',
        ]);

        // Calculate duration in minutes
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $validated['end_time']);
        $durationMinutes = $endTime->diffInMinutes($startTime);

        // Create a TimeSlot for each selected day
        foreach ($validated['days_of_week'] as $day) {
            TimeSlot::create([
                'day_of_week' => $day,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_minutes' => $durationMinutes,
            ]);
        }

        $dayCount = count($validated['days_of_week']);
        $message = $dayCount === 1 ? 'Franja horaria creada exitosamente.' : "Se crearon $dayCount franjas horarias exitosamente.";

        return redirect()->route('admin.time-slots.index')
            ->with('success', $message);
    }

    public function edit(TimeSlot $timeSlot)
    {
        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.time-slots.edit', compact('timeSlot', 'daysOfWeek'));
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Calculate duration in minutes
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $validated['end_time']);
        $durationMinutes = $endTime->diffInMinutes($startTime);

        $validated['duration_minutes'] = $durationMinutes;

        $timeSlot->update($validated);

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Franja horaria actualizada exitosamente.');
    }

    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Franja horaria eliminada exitosamente.');
    }
}
