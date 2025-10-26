<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Colegiaturas por Estudiante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex gap-4">
                <form action="{{ route('finance.student-tuitions.index') }}" method="GET" class="flex gap-4">
                    <select name="school_year_id" class="rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                        <option value="">Todos los ciclos</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}" {{ $selectedSchoolYearId == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('finance.student-tuitions.discount-report') }}" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    Ver Reporte de Descuentos
                </a>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Meses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto Mensual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descuentos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($studentTuitions as $period)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">{{ $period->student->user->full_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $period->school_year->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                            {{ $period->month_count }} meses
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($period->monthly_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($period->has_discounts)
                                            <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-sm font-medium text-orange-800">
                                                -${{ number_format($period->total_discount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">Sin descuentos</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('finance.student-tuitions.edit', $period->first_tuition) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar Período</a>
                                    </td>
                                </tr>
                                <tr class="hidden" id="details-{{ $period->first_tuition->id }}">
                                    <td colspan="6" class="px-6 py-4 bg-gray-50">
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-gray-900 mb-3">Detalle de Meses</h4>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <thead>
                                                        <tr class="border-b border-gray-200">
                                                            <th class="text-left py-2 px-3">Mes</th>
                                                            <th class="text-right py-2 px-3">Monto</th>
                                                            <th class="text-right py-2 px-3">Descuento</th>
                                                            <th class="text-right py-2 px-3">Total</th>
                                                            <th class="text-left py-2 px-3">Razón Descuento</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($period->months as $month)
                                                            <tr class="border-b border-gray-100">
                                                                <td class="py-2 px-3">{{ $month->month_name }} {{ $month->year }}</td>
                                                                <td class="text-right py-2 px-3">${{ number_format($month->monthly_amount, 2) }}</td>
                                                                <td class="text-right py-2 px-3">
                                                                    @if($month->discount_amount > 0)
                                                                        <span class="text-orange-600 font-semibold">-${{ number_format($month->discount_amount, 2) }}</span>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td class="text-right py-2 px-3 font-semibold">${{ number_format($month->monthly_amount - $month->discount_amount, 2) }}</td>
                                                                <td class="py-2 px-3 text-xs">{{ $month->discount_reason ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay colegiaturas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $studentTuitions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
