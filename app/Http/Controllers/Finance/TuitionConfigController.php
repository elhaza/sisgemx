<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\MonthlyTuition;
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
            'monthly_tuitions' => 'required|array',
            'monthly_tuitions.*.year' => 'required|integer',
            'monthly_tuitions.*.month' => 'required|integer|min:1|max:12',
            'monthly_tuitions.*.amount' => 'required|numeric|min:0',
        ]);

        // Create TuitionConfig with average amount for reference
        $averageAmount = collect($validated['monthly_tuitions'])->avg('amount');
        $tuitionConfig = TuitionConfig::create([
            'school_year_id' => $validated['school_year_id'],
            'amount' => $averageAmount,
        ]);

        // Create monthly tuitions if they don't exist
        foreach ($validated['monthly_tuitions'] as $monthlyData) {
            MonthlyTuition::firstOrCreate(
                [
                    'school_year_id' => $validated['school_year_id'],
                    'year' => $monthlyData['year'],
                    'month' => $monthlyData['month'],
                ],
                ['amount' => $monthlyData['amount']]
            );
        }

        // Assign default tuition to all students in this school year
        $this->assignDefaultTuitionToStudents($tuitionConfig);

        return redirect()->route('finance.tuition-configs.index')
            ->with('success', 'Configuración de colegiatura creada exitosamente.');
    }

    public function edit(TuitionConfig $tuitionConfig)
    {
        // Load monthly tuitions from the school year
        $monthlyTuitions = MonthlyTuition::where('school_year_id', $tuitionConfig->school_year_id)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('finance.tuition-configs.edit', compact('tuitionConfig', 'monthlyTuitions'));
    }

    public function update(Request $request, TuitionConfig $tuitionConfig)
    {
        $validated = $request->validate([
            'monthly_tuitions' => 'required|array',
            'monthly_tuitions.*.id' => 'nullable|exists:monthly_tuitions,id',
            'monthly_tuitions.*.year' => 'required|integer',
            'monthly_tuitions.*.month' => 'required|integer|min:1|max:12',
            'monthly_tuitions.*.amount' => 'required|numeric|min:0',
        ]);

        // Update monthly tuitions
        foreach ($validated['monthly_tuitions'] as $monthlyData) {
            if (! empty($monthlyData['id'])) {
                // Update existing
                MonthlyTuition::findOrFail($monthlyData['id'])->update([
                    'amount' => $monthlyData['amount'],
                ]);
            } else {
                // Create new if it doesn't exist
                MonthlyTuition::firstOrCreate(
                    [
                        'school_year_id' => $tuitionConfig->school_year_id,
                        'year' => $monthlyData['year'],
                        'month' => $monthlyData['month'],
                    ],
                    ['amount' => $monthlyData['amount']]
                );
            }
        }

        // Update TuitionConfig with average amount
        $averageAmount = collect($validated['monthly_tuitions'])->avg('amount');
        $tuitionConfig->update(['amount' => $averageAmount]);

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
