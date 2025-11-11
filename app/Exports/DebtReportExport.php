<?php

namespace App\Exports;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DebtReportExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return [];
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

        // Build report data
        $currentDate = now();
        $reportData = $students->map(function (Student $student) use ($months, $currentDate) {
            $studentData = [
                'Estudiante' => $student->user->full_name,
                'Grado' => $student->schoolGrade->grade_level.'° - '.$student->schoolGrade->name,
            ];

            // Add monthly debt columns
            foreach ($months as $month) {
                $tuition = StudentTuition::where('student_id', $student->id)
                    ->where('month', $month['number'])
                    ->where('year', $month['year'])
                    ->first();

                $monthKey = $month['number'].'-'.$month['year'];
                $columnHeader = substr($month['name'], 0, 3).' '.$month['year'];

                if ($tuition && ! $tuition->isPaid()) {
                    $tuitionAmount = (float) $tuition->final_amount;
                    $lateFeeAmount = (float) ($tuition->late_fee_amount ?? 0);
                    $total = $tuitionAmount + $lateFeeAmount;

                    $studentData[$columnHeader] = $total;
                } else {
                    $studentData[$columnHeader] = '';
                }
            }

            // Calculate totals
            $totalDebt = 0;
            $totalDebtDue = 0;

            foreach ($months as $month) {
                $tuition = StudentTuition::where('student_id', $student->id)
                    ->where('month', $month['number'])
                    ->where('year', $month['year'])
                    ->first();

                if ($tuition && ! $tuition->isPaid()) {
                    $tuitionAmount = (float) $tuition->final_amount;
                    $lateFeeAmount = (float) ($tuition->late_fee_amount ?? 0);
                    $total = $tuitionAmount + $lateFeeAmount;
                    $totalDebt += $total;

                    $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                    if (! $isMonthFuture) {
                        $totalDebtDue += $total;
                    }
                }
            }

            $studentData['Saldo al Corte'] = $totalDebtDue;
            $studentData['Total'] = $totalDebt;

            return $studentData;
        })->filter(function ($student) {
            return isset($student['Saldo al Corte']) && $student['Saldo al Corte'] > 0;
        })->values();

        // Add totals row
        $totalsRow = ['Estudiante' => 'TOTAL GENERAL', 'Grado' => ''];

        foreach ($months as $month) {
            $monthKey = $month['number'].'-'.$month['year'];
            $columnHeader = substr($month['name'], 0, 3).' '.$month['year'];

            $tuitionTotal = 0;
            $lateFeeTotal = 0;

            foreach ($students as $student) {
                $tuition = StudentTuition::where('student_id', $student->id)
                    ->where('month', $month['number'])
                    ->where('year', $month['year'])
                    ->first();

                if ($tuition && ! $tuition->isPaid()) {
                    $tuitionTotal += (float) $tuition->final_amount;
                    $lateFeeTotal += (float) ($tuition->late_fee_amount ?? 0);
                }
            }

            $columnTotal = $tuitionTotal + $lateFeeTotal;
            $totalsRow[$columnHeader] = $columnTotal > 0 ? $columnTotal : '';
        }

        // Calculate total column values
        $totalDebtAllStudents = $reportData->sum('Total');
        $totalDebtDueAllStudents = $reportData->sum('Saldo al Corte');

        $totalsRow['Saldo al Corte'] = $totalDebtDueAllStudents;
        $totalsRow['Total'] = $totalDebtAllStudents;

        $reportData->push($totalsRow);

        return $reportData->toArray();
    }

    public function headings(): array
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return ['Estudiante', 'Grado'];
        }

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

        $headings = ['Estudiante', 'Grado'];

        foreach ($months as $month) {
            $headings[] = substr($month['name'], 0, 3).' '.$month['year'];
        }

        $headings[] = 'Saldo al Corte';
        $headings[] = 'Total';

        return $headings;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFE74C3C']]],
        ];
    }

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
