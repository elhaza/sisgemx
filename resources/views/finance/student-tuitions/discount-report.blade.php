<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reporte de Estudiantes con Descuento
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

            @if($defaultTuition)
                <div class="mb-4 rounded-lg bg-blue-50 p-4">
                    <p class="text-sm text-blue-800"><strong>Monto general:</strong> ${{ number_format($defaultTuition->amount, 2) }}</p>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto General</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto del Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descuento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">% Descuento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($studentsWithDiscount as $tuition)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $tuition->student->user->full_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($tuition->default_amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($tuition->monthly_amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-green-600 font-semibold">${{ number_format($tuition->discount_amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ number_format($tuition->discount_percentage, 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay estudiantes con descuento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
