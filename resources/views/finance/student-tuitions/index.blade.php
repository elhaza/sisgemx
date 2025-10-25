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

            @if($defaultTuition)
                <div class="mb-4 rounded-lg bg-blue-50 p-4">
                    <p class="text-sm text-blue-800"><strong>Monto general del ciclo:</strong> ${{ number_format($defaultTuition->amount, 2) }}</p>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto Mensual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($studentTuitions as $tuition)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $tuition->student->user->full_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $tuition->schoolYear->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($tuition->monthly_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $tuition->notes ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('finance.student-tuitions.edit', $tuition) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay colegiaturas registradas.</td>
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
