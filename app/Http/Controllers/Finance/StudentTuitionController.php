<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\MonthlyTuition;
use App\Models\SchoolYear;
use App\Models\StudentTuition;
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

        return view('finance.student-tuitions.index', compact('studentTuitions', 'schoolYears', 'selectedSchoolYearId'));
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
            ]);
        }

        // Get all student tuitions for the selected school year
        $allStudentTuitions = StudentTuition::with(['student.user', 'schoolYear'])
            ->where('school_year_id', $selectedSchoolYearId)
            ->get();

        // Get all monthly tuitions for reference
        $monthlyTuitions = MonthlyTuition::where('school_year_id', $selectedSchoolYearId)
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.$item->month;
            });

        // Filter students with discounts by comparing against their specific month's tuition
        $studentsWithDiscount = $allStudentTuitions
            ->filter(function ($tuition) use ($monthlyTuitions) {
                $key = $tuition->year.'-'.$tuition->month;

                return isset($monthlyTuitions[$key]) && $tuition->monthly_amount < $monthlyTuitions[$key]->amount;
            })
            ->map(function ($tuition) use ($monthlyTuitions) {
                $key = $tuition->year.'-'.$tuition->month;
                $monthlyTuition = $monthlyTuitions[$key] ?? null;

                if ($monthlyTuition) {
                    $tuition->default_amount = $monthlyTuition->amount;
                    $tuition->discount_amount = $monthlyTuition->amount - $tuition->monthly_amount;
                    $tuition->discount_percentage = ($tuition->discount_amount / $monthlyTuition->amount) * 100;
                }

                return $tuition;
            })
            ->values();

        return view('finance.student-tuitions.discount-report', compact('studentsWithDiscount', 'schoolYears', 'selectedSchoolYearId'));
    }
}
