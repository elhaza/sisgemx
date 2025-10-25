<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\TuitionConfig;
use Illuminate\Http\Request;

class TuitionConfigController extends Controller
{
    public function index()
    {
        $tuitionConfigs = TuitionConfig::with('schoolYear')->latest()->paginate(15);

        return view('finance.tuition-configs.index', compact('tuitionConfigs'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::whereDoesntHave('tuitionConfig')->get();

        return view('finance.tuition-configs.create', compact('schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id|unique:tuition_configs',
            'amount' => 'required|numeric|min:0',
        ]);

        $tuitionConfig = TuitionConfig::create($validated);

        // Assign default tuition to all students in this school year
        $this->assignDefaultTuitionToStudents($tuitionConfig);

        return redirect()->route('finance.tuition-configs.index')
            ->with('success', 'Configuración de colegiatura creada exitosamente.');
    }

    public function edit(TuitionConfig $tuitionConfig)
    {
        return view('finance.tuition-configs.edit', compact('tuitionConfig'));
    }

    public function update(Request $request, TuitionConfig $tuitionConfig)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $tuitionConfig->update($validated);

        return redirect()->route('finance.tuition-configs.index')
            ->with('success', 'Configuración de colegiatura actualizada exitosamente.');
    }

    public function destroy(TuitionConfig $tuitionConfig)
    {
        $tuitionConfig->delete();

        return redirect()->route('finance.tuition-configs.index')
            ->with('success', 'Configuración de colegiatura eliminada exitosamente.');
    }

    protected function assignDefaultTuitionToStudents(TuitionConfig $tuitionConfig): void
    {
        $students = Student::where('school_year_id', $tuitionConfig->school_year_id)->get();

        foreach ($students as $student) {
            StudentTuition::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'school_year_id' => $tuitionConfig->school_year_id,
                ],
                [
                    'monthly_amount' => $tuitionConfig->amount,
                ]
            );
        }
    }
}
