<?php

namespace App\Http\Controllers\Admin;

use App\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SchoolGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['subject', 'subject.teacher'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $subjects = Subject::with('teacher')->get();
        $schoolGrades = SchoolGrade::with('schoolYear')->get();
        $daysOfWeek = DayOfWeek::cases();

        return view('admin.schedules.create', compact('subjects', 'schoolGrades', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|string|max:50',
            'group' => 'required|string|max:10',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'required|string|max:50',
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
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|string|max:50',
            'group' => 'required|string|max:10',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'required|string|max:50',
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
}
