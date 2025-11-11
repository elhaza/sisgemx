<?php

namespace App\Http\Controllers\Finance;

use App\Models\ChargeTemplate;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentAssignedCharge;
use App\Models\StudentTuition;

class PaymentReportController extends Controller
{
    /**
     * Display consolidated payment report
     */
    public function consolidatedPayments(): \Illuminate\View\View
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return view('finance.payment-reports.consolidated-payments', [
                'students' => collect(),
                'months' => collect(),
                'chargeTypes' => collect(),
                'reportData' => collect(),
                'activeSchoolYear' => null,
            ]);
        }

        // Get all active students
        $students = Student::where('school_year_id', $activeSchoolYear->id)
            ->where('status', 'active')
            ->with(['user', 'schoolGrade'])
            ->orderBy('id')
            ->get();

        // Get all months (1-12)
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'number' => $month,
                'name' => $this->getMonthName($month),
            ];
        });

        // Get all active charge types from templates
        $chargeTypes = ChargeTemplate::where('school_year_id', $activeSchoolYear->id)
            ->where('is_active', true)
            ->distinct('charge_type')
            ->pluck('charge_type', 'charge_type')
            ->toArray();

        // Get charge template names by type
        $chargeTemplates = ChargeTemplate::where('school_year_id', $activeSchoolYear->id)
            ->where('is_active', true)
            ->get()
            ->groupBy('charge_type');

        // Build report data
        $reportData = $students->map(function (Student $student) use ($months, $chargeTemplates, $activeSchoolYear) {
            $studentData = [
                'id' => $student->id,
                'name' => $student->user->full_name,
                'grade' => $student->schoolGrade->grade_level.'° - '.$student->schoolGrade->name,
                'monthly_payments' => [],
                'extra_charges' => [],
                'monthly_total' => 0,
                'extra_total' => 0,
                'grand_total' => 0,
            ];

            // Get monthly tuition payments
            foreach ($months as $month) {
                $tuition = StudentTuition::where('student_id', $student->id)
                    ->where('month', $month['number'])
                    ->where('year', $activeSchoolYear->year)
                    ->first();

                $paid = $tuition && $tuition->isPaid() ? (float) $tuition->final_amount : 0;
                $studentData['monthly_payments'][$month['number']] = $paid;
                $studentData['monthly_total'] += $paid;
            }

            // Get extra charges (inscription, materials, etc.)
            foreach ($chargeTemplates as $chargeType => $templates) {
                $templateNames = $templates->pluck('name')->implode(', ');

                $assignedCharge = StudentAssignedCharge::where('student_id', $student->id)
                    ->whereIn('charge_template_id', $templates->pluck('id'))
                    ->first();

                $paid = $assignedCharge && $assignedCharge->is_paid ? (float) $assignedCharge->amount : 0;
                $studentData['extra_charges'][$chargeType] = [
                    'name' => $templateNames,
                    'amount' => $paid,
                ];
                $studentData['extra_total'] += $paid;
            }

            $studentData['grand_total'] = $studentData['monthly_total'] + $studentData['extra_total'];

            return $studentData;
        });

        return view('finance.payment-reports.consolidated-payments', [
            'students' => $students,
            'months' => $months,
            'chargeTypes' => $chargeTypes,
            'chargeTemplates' => $chargeTemplates,
            'reportData' => $reportData,
            'activeSchoolYear' => $activeSchoolYear,
        ]);
    }

    /**
     * Get month name in Spanish
     */
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $months[$month] ?? '';
    }
}
