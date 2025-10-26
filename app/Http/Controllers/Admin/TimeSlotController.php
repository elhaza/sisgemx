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
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Calculate duration in minutes
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $validated['end_time']);
        $durationMinutes = $endTime->diffInMinutes($startTime);

        $validated['duration_minutes'] = $durationMinutes;

        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Franja horaria creada exitosamente.');
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
