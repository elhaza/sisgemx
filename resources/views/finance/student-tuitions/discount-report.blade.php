<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reporte de Descuentos en Colegiaturas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-4">
                <form action="{{ route('finance.student-tuitions.discount-report') }}" method="GET" class="flex gap-4">
                    <select name="school_year_id" class="rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                        <option value="">Seleccionar ciclo</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}" {{ $selectedSchoolYearId == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($studentsWithDiscount->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">No hay estudiantes con descuento en este ciclo escolar.</p>
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Período</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Base</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">% Descuento</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Descuento</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total a Pagar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($studentsWithDiscount as $tuition)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $tuition->student_name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="text-sm text-gray-700">{{ $tuition->month_name }} {{ $tuition->year }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <span class="font-semibold text-gray-900">${{ number_format($tuition->monthly_amount, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($tuition->discount_percentage > 0)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                                    {{ number_format($tuition->discount_percentage, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            @if($tuition->discount_percentage > 0)
                                                @php
                                                    $discountAmount = ($tuition->monthly_amount * $tuition->discount_percentage) / 100;
                                                @endphp
                                                <span class="font-semibold text-green-600">-${{ number_format($discountAmount, 2) }}</span>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            @php
                                                $discountAmount = ($tuition->monthly_amount * $tuition->discount_percentage) / 100;
                                                $finalAmount = $tuition->monthly_amount - $discountAmount;
                                            @endphp
                                            <span class="font-bold text-gray-900">${{ number_format($finalAmount, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
