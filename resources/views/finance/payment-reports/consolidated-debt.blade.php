<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reporte Concentrado de Deudas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            @if(!$activeSchoolYear)
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-6 shadow-sm">
                    <h3 class="font-semibold text-red-800">No hay ciclo escolar activo</h3>
                    <p class="mt-2 text-sm text-red-700">
                        Debes crear o activar un ciclo escolar para ver el reporte de deudas.
                    </p>
                </div>
            @elseif($reportData->isEmpty())
                <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Sin deudas</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Excelente: No hay deudas registradas en el ciclo escolar actual.
                    </p>
                </div>
            @else
                <!-- Ciclo Escolar Info -->
                <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
                    <p class="text-sm text-blue-900">
                        <strong>Ciclo Escolar Actual:</strong> {{ $activeSchoolYear->name }} ({{ $activeSchoolYear->year }})
                    </p>
                </div>

                <!-- Tabla de Deudas -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 border-b border-gray-200 bg-gray-50">
                            <tr>
                                <!-- Estudiante y Grado -->
                                <th class="sticky left-0 z-10 border-r border-gray-200 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-900 min-w-max">
                                    Estudiante
                                </th>
                                <th class="border-r border-gray-200 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-900 min-w-max">
                                    Grado
                                </th>

                                <!-- Deudas por Mes -->
                                <th colspan="12" class="border-r border-gray-200 bg-red-50 px-4 py-3 text-center font-semibold text-red-900">
                                    Deudas por Mes
                                </th>

                                <!-- Total -->
                                <th class="bg-red-100 px-4 py-3 text-center font-semibold text-red-900">
                                    Total Deuda
                                </th>
                            </tr>

                            <!-- Sublabels para meses -->
                            <tr class="border-b border-gray-200">
                                <th class="sticky left-0 z-10 border-r border-gray-200 bg-gray-50"></th>
                                <th class="border-r border-gray-200 bg-gray-50"></th>

                                @foreach($months as $month)
                                    @php
                                        $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                                        $headerClass = $isMonthFuture ? 'bg-gray-100 text-gray-500' : 'bg-red-50 text-red-700';
                                    @endphp
                                    <th class="border-r border-gray-200 {{ $headerClass }} px-3 py-2 text-center text-xs font-medium min-w-max">
                                        {{ substr($month['name'], 0, 3) }} {{ $month['year'] }}
                                    </th>
                                @endforeach

                                <th class="bg-red-100 px-3 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach($reportData as $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- Estudiante -->
                                    <td class="sticky left-0 z-5 border-r border-gray-200 bg-white px-4 py-3 font-semibold hover:bg-gray-50">
                                        <a href="{{ route('admin.students.show', $student['id']) }}#colegiaturas" class="text-blue-600 hover:text-blue-900 hover:underline">
                                            {{ $student['name'] }}
                                        </a>
                                    </td>

                                    <!-- Grado -->
                                    <td class="border-r border-gray-200 px-4 py-3 text-sm text-gray-700 min-w-max">
                                        {{ $student['grade'] }}
                                    </td>

                                    <!-- Deudas Mensuales -->
                                    @foreach($months as $month)
                                        @php
                                            $monthKey = $month['number'].'-'.$month['year'];
                                            $monthDebt = $student['monthly_debts'][$monthKey] ?? ['tuition' => 0, 'late_fee' => 0, 'total' => 0];
                                            $isMonthFuture = ($month['year'] > $currentDate->year) || ($month['year'] == $currentDate->year && $month['number'] > $currentDate->month);
                                            $cellClass = $isMonthFuture ? 'bg-gray-100' : '';
                                        @endphp
                                        <td class="border-r border-gray-200 px-3 py-3 text-center text-sm {{ $cellClass }}">
                                            @if($monthDebt['total'] > 0)
                                                <div class="space-y-1">
                                                    @if($monthDebt['tuition'] > 0)
                                                        <div class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-bold text-red-800">
                                                            ${{ number_format($monthDebt['tuition'], 2) }}
                                                        </div>
                                                    @endif
                                                    @if($monthDebt['late_fee'] > 0)
                                                        <div class="inline-flex items-center rounded-full bg-orange-100 px-2 py-1 text-xs font-bold text-orange-800">
                                                            +${{ number_format($monthDebt['late_fee'], 2) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Total Deuda del Estudiante -->
                                    <td class="bg-red-100 px-4 py-3 text-center font-bold text-red-900">
                                        <div class="text-sm">
                                            ${{ number_format($student['total_debt_due'], 2) }}
                                        </div>
                                        <div class="text-xs text-red-700 mt-1">
                                            Total: ${{ number_format($student['total_debt'], 2) }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <!-- Fila de Totales -->
                        <tfoot class="border-t-2 border-gray-300 bg-red-50">
                            <tr class="font-bold text-gray-900">
                                <td class="sticky left-0 z-10 border-r border-gray-300 bg-red-50 px-4 py-3">
                                    TOTAL GENERAL
                                </td>
                                <td class="border-r border-gray-300 px-4 py-3"></td>

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
                                        $cellClass = $isMonthFuture ? 'bg-gray-200' : '';
                                    @endphp
                                    <td class="border-r border-gray-300 px-3 py-3 text-center text-sm {{ $cellClass }}">
                                        @if($monthlyTotal['total'] > 0)
                                            <div class="space-y-1">
                                                @if($monthlyTotal['tuition'] > 0)
                                                    <div class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-bold">
                                                        ${{ number_format($monthlyTotal['tuition'], 2) }}
                                                    </div>
                                                @endif
                                                @if($monthlyTotal['late_fee'] > 0)
                                                    <div class="bg-orange-200 text-orange-800 px-2 py-1 rounded text-xs font-bold">
                                                        +${{ number_format($monthlyTotal['late_fee'], 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach

                                <td class="bg-red-200 px-4 py-3 text-center font-bold text-red-900">
                                    <div class="text-sm">
                                        ${{ number_format($totalDebtDue, 2) }}
                                    </div>
                                    <div class="text-xs text-red-800 mt-1">
                                        Total: ${{ number_format($totalDebt, 2) }}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Leyenda -->
                <div class="mt-6 rounded-lg bg-white border border-gray-200 p-4 shadow-sm">
                    <h3 class="font-semibold text-gray-900 mb-3">Leyenda</h3>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="flex items-start">
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800 mr-2">
                                $X.XX
                            </span>
                            <span class="text-sm text-gray-700">Matrícula adeudada</span>
                        </div>
                        <div class="flex items-start">
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-800 mr-2">
                                +$X.XX
                            </span>
                            <span class="text-sm text-gray-700">Recargos por mora</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-gray-400">-</span>
                            <span class="text-sm text-gray-700 ml-2">Sin deuda en ese mes</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen Estadístico -->
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-white border border-gray-200 p-4 shadow-sm">
                        <p class="text-sm text-gray-600">Estudiantes con Deuda</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $reportData->count() }}</p>
                    </div>

                    <div class="rounded-lg bg-red-50 border border-red-200 p-4 shadow-sm">
                        <p class="text-sm text-red-700">Deuda Total (Matrícula)</p>
                        @php
                            $totalTuitionDebt = 0;
                            foreach($reportData as $student) {
                                foreach($student['monthly_debts'] as $debt) {
                                    $totalTuitionDebt += $debt['tuition'];
                                }
                            }
                        @endphp
                        <p class="mt-1 text-2xl font-bold text-red-900">${{ number_format($totalTuitionDebt, 2) }}</p>
                    </div>

                    <div class="rounded-lg bg-orange-50 border border-orange-200 p-4 shadow-sm">
                        <p class="text-sm text-orange-700">Total Recargos por Mora</p>
                        @php
                            $totalLateFeeDebt = $totalDebt - $totalTuitionDebt;
                        @endphp
                        <p class="mt-1 text-2xl font-bold text-orange-900">${{ number_format($totalLateFeeDebt, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
