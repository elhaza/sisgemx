<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Deudas Concentrado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .school-year-info {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background-color: #f5f5f5;
        }

        thead th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        tbody td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tfoot {
            background-color: #ffebee;
            font-weight: bold;
        }

        tfoot td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
            background-color: #ffcdd2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }

        .summary-card.red {
            background-color: #ffebee;
            border-color: #ef5350;
        }

        .summary-card.orange {
            background-color: #fff3e0;
            border-color: #fb8c00;
        }

        .summary-card p {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card h3 {
            font-size: 14px;
            font-weight: bold;
        }

        .summary-card.red h3 {
            color: #d32f2f;
        }

        .summary-card.orange h3 {
            color: #e65100;
        }

        .month-header {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
        }

        .page-break {
            page-break-after: always;
        }

        .amount-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .future-month {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte Concentrado de Deudas</h1>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="school-year-info">
            <strong>Ciclo Escolar Actual:</strong> {{ $activeSchoolYear->name }} ({{ $activeSchoolYear->year }})
        </div>

        @if($reportData->isEmpty())
            <p style="text-align: center; padding: 20px;">No hay deudas registradas en el ciclo escolar actual.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Estudiante</th>
                        <th style="min-width: 100px;">Grado</th>
                        @foreach($months as $month)
                            <th class="text-center" style="min-width: 60px;">
                                {{ substr($month['name'], 0, 3) }}<br>{{ $month['year'] }}
                            </th>
                        @endforeach
                        <th class="text-center" style="min-width: 80px;">Saldo al<br>Corte</th>
                        <th class="text-center" style="min-width: 70px;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($reportData as $student)
                        <tr>
                            <td><strong>{{ $student['name'] }}</strong></td>
                            <td>{{ $student['grade'] }}</td>

                            @foreach($months as $month)
                                @php
                                    $monthKey = $month['number'].'-'.$month['year'];
                                    $monthDebt = $student['monthly_debts'][$monthKey] ?? ['tuition' => 0, 'late_fee' => 0, 'total' => 0];
                                    $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                                @endphp
                                <td class="amount-cell @if($isMonthFuture) future-month @endif">
                                    @if($monthDebt['total'] > 0)
                                        @if($monthDebt['late_fee'] > 0)
                                            ${{ number_format($monthDebt['tuition'], 2) }}<br>+${{ number_format($monthDebt['late_fee'], 2) }}
                                        @else
                                            ${{ number_format($monthDebt['tuition'], 2) }}
                                        @endif
                                    @endif
                                </td>
                            @endforeach

                            <td class="amount-cell" style="background-color: #ffcdd2; font-weight: bold;">
                                ${{ number_format($student['total_debt_due'], 2) }}
                            </td>
                            <td class="amount-cell" style="background-color: #fff3e0; font-weight: bold;">
                                ${{ number_format($student['total_debt'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2"><strong>TOTAL GENERAL</strong></td>

                        @php
                            $monthlyTotals = [];
                            foreach($months as $month) {
                                $monthKey = $month['number'].'-'.$month['year'];
                                $tuitionTotal = 0;
                                $lateFeeTotal = 0;
                                foreach($reportData as $student) {
                                    $debt = $student['monthly_debts'][$monthKey] ?? ['tuition' => 0, 'late_fee' => 0, 'total' => 0];
                                    $tuitionTotal += $debt['tuition'] ?? 0;
                                    $lateFeeTotal += $debt['late_fee'] ?? 0;
                                }
                                $monthlyTotals[$monthKey] = [
                                    'tuition' => $tuitionTotal,
                                    'late_fee' => $lateFeeTotal,
                                    'total' => $tuitionTotal + $lateFeeTotal,
                                ];
                            }
                        @endphp

                        @foreach($months as $month)
                            @php
                                $monthKey = $month['number'].'-'.$month['year'];
                                $monthlyTotal = $monthlyTotals[$monthKey] ?? ['tuition' => 0, 'late_fee' => 0, 'total' => 0];
                                $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                            @endphp
                            <td class="amount-cell @if($isMonthFuture) future-month @endif">
                                @if($monthlyTotal['total'] > 0)
                                    @if($monthlyTotal['late_fee'] > 0)
                                        ${{ number_format($monthlyTotal['tuition'], 2) }}<br>+${{ number_format($monthlyTotal['late_fee'], 2) }}
                                    @else
                                        ${{ number_format($monthlyTotal['tuition'], 2) }}
                                    @endif
                                @endif
                            </td>
                        @endforeach

                        <td class="amount-cell" style="background-color: #ffcdd2; font-weight: bold;">
                            ${{ number_format($totalDebtDue, 2) }}
                        </td>
                        <td class="amount-cell" style="background-color: #fff3e0; font-weight: bold;">
                            ${{ number_format($totalDebt, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="summary">
                <div class="summary-card">
                    <p>Estudiantes con Deuda</p>
                    <h3>{{ $reportData->count() }}</h3>
                </div>

                <div class="summary-card red">
                    <p>Deuda Total (Matrícula)</p>
                    @php
                        $totalTuitionDebt = 0;
                        foreach($reportData as $student) {
                            foreach($student['monthly_debts'] as $debt) {
                                $totalTuitionDebt += $debt['tuition'];
                            }
                        }
                    @endphp
                    <h3>${{ number_format($totalTuitionDebt, 2) }}</h3>
                </div>

                <div class="summary-card orange">
                    <p>Total Recargos por Mora</p>
                    @php
                        $totalLateFeeDebt = $totalDebt - $totalTuitionDebt;
                    @endphp
                    <h3>${{ number_format($totalLateFeeDebt, 2) }}</h3>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
