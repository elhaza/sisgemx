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

        $query = StudentTuition::with(['student.user', 'schoolYear'])->orderBy('student_id')->orderBy('month');

        if ($selectedSchoolYearId) {
            $query->where('school_year_id', $selectedSchoolYearId);
        }

        $allTuitions = $query->get();

        // Group by student and school year to get periods
        $studentTuitionPeriods = $allTuitions->groupBy(function ($tuition) {
            return $tuition->student_id.'_'.$tuition->school_year_id;
        })->map(function ($group) {
            $firstTuition = $group->first();

            return (object) [
                'student_id' => $firstTuition->student_id,
                'student' => $firstTuition->student,
                'school_year_id' => $firstTuition->school_year_id,
                'school_year' => $firstTuition->schoolYear,
                'month_count' => $group->count(),
                'monthly_amount' => $firstTuition->monthly_amount,
                'has_discounts' => $group->some(function ($t) {
                    return $t->discount_amount > 0;
                }),
                'total_discount' => $group->sum('discount_amount'),
                'first_tuition' => $firstTuition,
                'months' => $group,
            ];
        })->values();

        // Paginate the grouped results
        $page = $request->get('page', 1);
        $perPage = 10;
        $studentTuitions = collect($studentTuitionPeriods)
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return view('finance.student-tuitions.index', compact('studentTuitions', 'schoolYears', 'selectedSchoolYearId'));
    }

    public function edit(StudentTuition $studentTuition)
    {
        $studentTuition->load(['student.user', 'schoolYear']);

        // Get all tuitions for this student and school year
        $allTuitions = StudentTuition::where('student_id', $studentTuition->student_id)
            ->where('school_year_id', $studentTuition->school_year_id)
            ->orderBy('month')
            ->get();

        return view('finance.student-tuitions.edit', compact('studentTuition', 'allTuitions'));
    }

    public function update(Request $request, StudentTuition $studentTuition)
    {
        $validated = $request->validate([
            'tuitions' => 'required|array',
            'tuitions.*.monthly_amount' => 'required|numeric|min:0',
            'tuitions.*.discount_amount' => 'required|numeric|min:0',
            'tuitions.*.discount_reason' => 'nullable|string',
            'tuitions.*.notes' => 'nullable|string',
        ]);

        // Get all tuitions for this student and school year
        $allTuitions = StudentTuition::where('student_id', $studentTuition->student_id)
            ->where('school_year_id', $studentTuition->school_year_id)
            ->get();

        // Update each tuition
        foreach ($validated['tuitions'] as $tuitionId => $data) {
            $tuition = $allTuitions->firstWhere('id', $tuitionId);
            if ($tuition) {
                $tuition->update($data);
            }
        }

        return redirect()->route('finance.student-tuitions.index', ['school_year_id' => $studentTuition->school_year_id])
            ->with('success', 'Colegiaturas del período actualizado exitosamente.');
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
