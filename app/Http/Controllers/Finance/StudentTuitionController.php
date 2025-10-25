<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\StudentTuition;
use App\Models\TuitionConfig;
use Illuminate\Http\Request;

class StudentTuitionController extends Controller
{
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::all();
        $selectedSchoolYearId = $request->get('school_year_id', SchoolYear::where('is_active', true)->first()?->id);

        $query = StudentTuition::with(['student.user', 'schoolYear']);

        if ($selectedSchoolYearId) {
            $query->where('school_year_id', $selectedSchoolYearId);
        }

        $studentTuitions = $query->paginate(20);

        // Get default tuition amount for the selected school year
        $defaultTuition = null;
        if ($selectedSchoolYearId) {
            $defaultTuition = TuitionConfig::where('school_year_id', $selectedSchoolYearId)->first();
        }

        return view('finance.student-tuitions.index', compact('studentTuitions', 'schoolYears', 'selectedSchoolYearId', 'defaultTuition'));
    }

    public function edit(StudentTuition $studentTuition)
    {
        $studentTuition->load(['student.user', 'schoolYear']);

        return view('finance.student-tuitions.edit', compact('studentTuition'));
    }

    public function update(Request $request, StudentTuition $studentTuition)
    {
        $validated = $request->validate([
            'monthly_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $studentTuition->update($validated);

        return redirect()->route('finance.student-tuitions.index', ['school_year_id' => $studentTuition->school_year_id])
            ->with('success', 'Colegiatura del estudiante actualizada exitosamente.');
    }

    public function discountReport(Request $request)
    {
        $schoolYears = SchoolYear::all();
        $selectedSchoolYearId = $request->get('school_year_id', SchoolYear::where('is_active', true)->first()?->id);

        if (! $selectedSchoolYearId) {
            return view('finance.student-tuitions.discount-report', [
                'studentsWithDiscount' => collect(),
                'schoolYears' => $schoolYears,
                'selectedSchoolYearId' => null,
                'defaultTuition' => null,
            ]);
        }

        $defaultTuition = TuitionConfig::where('school_year_id', $selectedSchoolYearId)->first();

        if (! $defaultTuition) {
            return view('finance.student-tuitions.discount-report', [
                'studentsWithDiscount' => collect(),
                'schoolYears' => $schoolYears,
                'selectedSchoolYearId' => $selectedSchoolYearId,
                'defaultTuition' => null,
            ]);
        }

        $studentsWithDiscount = StudentTuition::with(['student.user', 'schoolYear'])
            ->where('school_year_id', $selectedSchoolYearId)
            ->where('monthly_amount', '<', $defaultTuition->amount)
            ->get()
            ->map(function ($tuition) use ($defaultTuition) {
                $tuition->default_amount = $defaultTuition->amount;
                $tuition->discount_amount = $defaultTuition->amount - $tuition->monthly_amount;
                $tuition->discount_percentage = ($tuition->discount_amount / $defaultTuition->amount) * 100;

                return $tuition;
            });

        return view('finance.student-tuitions.discount-report', compact('studentsWithDiscount', 'schoolYears', 'selectedSchoolYearId', 'defaultTuition'));
    }
}
