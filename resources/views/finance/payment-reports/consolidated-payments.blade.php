<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reporte Concentrado de Pagos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            @if(!$activeSchoolYear)
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-6 shadow-sm">
                    <h3 class="font-semibold text-red-800">No hay ciclo escolar activo</h3>
                    <p class="mt-2 text-sm text-red-700">
                        Debes crear o activar un ciclo escolar para ver el reporte de pagos.
                    </p>
                </div>
            @elseif($reportData->isEmpty())
                <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No hay estudiantes activos</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        No hay estudiantes activos en el ciclo escolar actual.
                    </p>
                </div>
            @else
                <!-- Ciclo Escolar Info -->
                <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
                    <p class="text-sm text-blue-900">
                        <strong>Ciclo Escolar Actual:</strong> {{ $activeSchoolYear->name }} ({{ $activeSchoolYear->year }})
                    </p>
                </div>

                <!-- Tabla Concentrada -->
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

                                <!-- Meses -->
                                <th colspan="12" class="border-r border-gray-200 bg-blue-50 px-4 py-3 text-center font-semibold text-blue-900">
                                    Matrícula Mensual
                                </th>

                                <!-- Otros Pagos -->
                                @if(!empty($chargeTypes))
                                    <th colspan="{{ count($chargeTypes) }}" class="border-r border-gray-200 bg-green-50 px-4 py-3 text-center font-semibold text-green-900">
                                        Otros Pagos
                                    </th>
                                @endif

                                <!-- Totales -->
                                <th class="bg-purple-50 px-4 py-3 text-center font-semibold text-purple-900">
                                    Total
                                </th>
                            </tr>

                            <!-- Sublabels para meses -->
                            <tr class="border-b border-gray-200">
                                <th class="sticky left-0 z-10 border-r border-gray-200 bg-gray-50"></th>
                                <th class="border-r border-gray-200 bg-gray-50"></th>

                                @foreach($months as $month)
                                    <th class="border-r border-gray-200 bg-blue-50 px-3 py-2 text-center text-xs font-medium text-blue-700 min-w-max">
                                        {{ substr($month['name'], 0, 3) }}
                                    </th>
                                @endforeach

                                <!-- Sublabels para otros pagos -->
                                @foreach($chargeTypes as $chargeType)
                                    <th class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-medium text-green-700 min-w-max">
                                        {{ ucfirst(str_replace('_', ' ', $chargeType)) }}
                                    </th>
                                @endforeach

                                <th class="bg-purple-50 px-3 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach($reportData as $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- Estudiante -->
                                    <td class="sticky left-0 z-5 border-r border-gray-200 bg-white px-4 py-3 font-semibold text-gray-900 hover:bg-gray-50">
                                        {{ $student['name'] }}
                                    </td>

                                    <!-- Grado -->
                                    <td class="border-r border-gray-200 px-4 py-3 text-sm text-gray-700 min-w-max">
                                        {{ $student['grade'] }}
                                    </td>

                                    <!-- Pagos Mensuales -->
                                    @foreach($months as $month)
                                        <td class="border-r border-gray-200 px-3 py-3 text-center text-sm font-medium">
                                            @if($student['monthly_payments'][$month['number']] > 0)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-800">
                                                    ${{ number_format($student['monthly_payments'][$month['number']], 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Otros Pagos -->
                                    @foreach($chargeTypes as $chargeType)
                                        <td class="border-r border-gray-200 px-3 py-3 text-center text-sm font-medium">
                                            @if(isset($student['extra_charges'][$chargeType]) && $student['extra_charges'][$chargeType]['amount'] > 0)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800" title="{{ $student['extra_charges'][$chargeType]['name'] }}">
                                                    ${{ number_format($student['extra_charges'][$chargeType]['amount'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Total -->
                                    <td class="bg-purple-50 px-4 py-3 text-center text-sm font-bold text-purple-900">
                                        ${{ number_format($student['grand_total'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <!-- Fila de Totales -->
                        <tfoot class="border-t-2 border-gray-300 bg-gray-100">
                            <tr class="font-bold text-gray-900">
                                <td class="sticky left-0 z-10 border-r border-gray-300 bg-gray-100 px-4 py-3">
                                    TOTAL GENERAL
                                </td>
                                <td class="border-r border-gray-300 px-4 py-3"></td>

                                @php
                                    $monthlyTotals = [];
                                    foreach($months as $month) {
                                        $total = 0;
                                        foreach($reportData as $student) {
                                            $total += $student['monthly_payments'][$month['number']];
                                        }
                                        $monthlyTotals[$month['number']] = $total;
                                    }
                                @endphp

                                @foreach($months as $month)
                                    <td class="border-r border-gray-300 bg-blue-100 px-3 py-3 text-center">
                                        @if($monthlyTotals[$month['number']] > 0)
                                            ${{ number_format($monthlyTotals[$month['number']], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach

                                @php
                                    $extraTotals = [];
                                    foreach($chargeTypes as $chargeType) {
                                        $total = 0;
                                        foreach($reportData as $student) {
                                            if(isset($student['extra_charges'][$chargeType])) {
                                                $total += $student['extra_charges'][$chargeType]['amount'];
                                            }
                                        }
                                        $extraTotals[$chargeType] = $total;
                                    }
                                @endphp

                                @foreach($chargeTypes as $chargeType)
                                    <td class="border-r border-gray-300 bg-green-100 px-3 py-3 text-center">
                                        @if($extraTotals[$chargeType] > 0)
                                            ${{ number_format($extraTotals[$chargeType], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach

                                <td class="bg-purple-200 px-4 py-3 text-center">
                                    @php
                                        $grandTotal = collect($reportData)->sum('grand_total');
                                    @endphp
                                    ${{ number_format($grandTotal, 2) }}
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
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-800 mr-2">
                                $X.XX
                            </span>
                            <span class="text-sm text-gray-700">Matrícula mensual pagada</span>
                        </div>
                        <div class="flex items-start">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800 mr-2">
                                $X.XX
                            </span>
                            <span class="text-sm text-gray-700">Otros pagos (inscripción, materiales, etc.)</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-gray-400">-</span>
                            <span class="text-sm text-gray-700 ml-2">Pago no realizado</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen Estadístico -->
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-white border border-gray-200 p-4 shadow-sm">
                        <p class="text-sm text-gray-600">Total de Estudiantes</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $reportData->count() }}</p>
                    </div>

                    @php
                        $totalMonthlyPaid = collect($reportData)->sum('monthly_total');
                        $totalExtraPaid = collect($reportData)->sum('extra_total');
                        $grandTotal = collect($reportData)->sum('grand_total');
                    @endphp

                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 shadow-sm">
                        <p class="text-sm text-blue-700">Matrícula Pagada</p>
                        <p class="mt-1 text-2xl font-bold text-blue-900">${{ number_format($totalMonthlyPaid, 2) }}</p>
                    </div>

                    <div class="rounded-lg bg-purple-50 border border-purple-200 p-4 shadow-sm">
                        <p class="text-sm text-purple-700">Total Recaudado</p>
                        <p class="mt-1 text-2xl font-bold text-purple-900">${{ number_format($grandTotal, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
