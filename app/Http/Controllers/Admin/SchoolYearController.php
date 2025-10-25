<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
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
        ]);

        if ($request->boolean('is_active')) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        SchoolYear::create($validated);

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar creado exitosamente.');
    }

    public function edit(SchoolYear $schoolYear)
    {
        return view('admin.school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active') && ! $schoolYear->is_active) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        $schoolYear->update($validated);

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar actualizado exitosamente.');
    }

    public function destroy(SchoolYear $schoolYear)
    {
        if ($schoolYear->is_active) {
            return redirect()->route('admin.school-years.index')->with('error', 'No puedes eliminar el ciclo escolar activo.');
        }

        $schoolYear->delete();

        return redirect()->route('admin.school-years.index')->with('success', 'Ciclo escolar eliminado exitosamente.');
    }
}
