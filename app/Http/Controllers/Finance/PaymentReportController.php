<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
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

        // Get all months from this school year's tuitions (school year can span multiple years)
        $months = StudentTuition::where('school_year_id', $activeSchoolYear->id)
            ->distinct()
            ->orderBy('year')
            ->orderBy('month')
            ->get(['month', 'year'])
            ->map(function ($tuition) {
                return [
                    'number' => $tuition->month,
                    'year' => $tuition->year,
                    'name' => $this->getMonthName($tuition->month),
                ];
            })
            ->unique(fn ($m) => $m['number'].'-'.$m['year'])
            ->values();

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
        $reportData = $students->map(function (Student $student) use ($months, $chargeTemplates) {
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
                    ->where('year', $month['year'])
                    ->first();

                $monthKey = $month['number'].'-'.$month['year'];

                if ($tuition && $tuition->isPaid()) {
                    $tuitionAmount = (float) $tuition->final_amount;
                    $lateFeeAmount = (float) ($tuition->late_fee_amount ?? 0);
                    $totalAmount = $tuitionAmount + $lateFeeAmount;

                    $studentData['monthly_payments'][$monthKey] = [
                        'tuition' => $tuitionAmount,
                        'late_fee' => $lateFeeAmount,
                        'total' => $totalAmount,
                    ];
                    $studentData['monthly_total'] += $totalAmount;
                } else {
                    $studentData['monthly_payments'][$monthKey] = [
                        'tuition' => 0,
                        'late_fee' => 0,
                        'total' => 0,
                    ];
                }
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
     * Display consolidated debt report
     */
    public function debtReport(): \Illuminate\View\View
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return view('finance.payment-reports.consolidated-debt', [
                'students' => collect(),
                'months' => collect(),
                'reportData' => collect(),
                'activeSchoolYear' => null,
                'totalDebt' => 0,
            ]);
        }

        // Get all active students
        $students = Student::where('school_year_id', $activeSchoolYear->id)
            ->where('status', 'active')
            ->with(['user', 'schoolGrade'])
            ->orderBy('id')
            ->get();

        // Get all months from this school year's tuitions
        $months = StudentTuition::where('school_year_id', $activeSchoolYear->id)
            ->distinct()
            ->orderBy('year')
            ->orderBy('month')
            ->get(['month', 'year'])
            ->map(function ($tuition) {
                return [
                    'number' => $tuition->month,
                    'year' => $tuition->year,
                    'name' => $this->getMonthName($tuition->month),
                ];
            })
            ->unique(fn ($m) => $m['number'].'-'.$m['year'])
            ->values();

        // Build report data - only include students with debt
        $currentDate = now();
        $reportData = $students->map(function (Student $student) use ($months, $currentDate) {
            $studentData = [
                'id' => $student->id,
                'name' => $student->user->full_name,
                'grade' => $student->schoolGrade->grade_level.'° - '.$student->schoolGrade->name,
                'monthly_debts' => [],
                'total_debt' => 0,
                'total_debt_due' => 0,
            ];

            // Get unpaid monthly tuitions
            foreach ($months as $month) {
                $tuition = StudentTuition::where('student_id', $student->id)
                    ->where('month', $month['number'])
                    ->where('year', $month['year'])
                    ->first();

                $monthKey = $month['number'].'-'.$month['year'];

                if ($tuition && ! $tuition->isPaid()) {
                    $tuitionAmount = (float) $tuition->final_amount;
                    $lateFeeAmount = (float) ($tuition->late_fee_amount ?? 0);
                    $totalAmount = $tuitionAmount + $lateFeeAmount;

                    $studentData['monthly_debts'][$monthKey] = [
                        'tuition' => $tuitionAmount,
                        'late_fee' => $lateFeeAmount,
                        'total' => $totalAmount,
                    ];
                    $studentData['total_debt'] += $totalAmount;

                    // Only count towards due debt if month has already passed
                    $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                    if (! $isMonthFuture) {
                        $studentData['total_debt_due'] += $totalAmount;
                    }
                }
            }

            return $studentData;
        })->filter(fn ($student) => $student['total_debt'] > 0); // Only include students with debt

        // Calculate total debt across all students
        $totalDebt = $reportData->sum('total_debt');
        $totalDebtDue = $reportData->sum('total_debt_due');

        return view('finance.payment-reports.consolidated-debt', [
            'students' => $students,
            'months' => $months,
            'reportData' => $reportData,
            'activeSchoolYear' => $activeSchoolYear,
            'totalDebt' => $totalDebt,
            'totalDebtDue' => $totalDebtDue,
            'currentDate' => now(),
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
