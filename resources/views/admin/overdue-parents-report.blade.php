<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Reporte de Padres/Tutores con Mora
            </h2>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900">
                ← Volver al Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-4 rounded-lg bg-blue-100 p-4 text-blue-700">
                <p class="font-semibold">Total de padres/tutores con mora: {{ $totalParents }}</p>
            </div>

            @if($parentReport->isEmpty())
                <div class="rounded-lg bg-green-100 p-4 text-green-700">
                    <p>¡Excelente! No hay padres/tutores con pagos en mora.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($parentReport as $parent)
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="border-b border-gray-200 bg-gray-50 p-6">
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700">Padre/Tutor 1</h3>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $parent['tutor_1'] }}</p>
                                    </div>
                                    @if($parent['tutor_2'])
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-700">Padre/Tutor 2</h3>
                                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $parent['tutor_2'] }}</p>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700">Pagos con Mora</h3>
                                        <p class="mt-1 text-lg font-bold text-red-600">{{ $parent['total_overdue_tuitions'] }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700">Días de Mora Máximo</h3>
                                        <p class="mt-1 text-lg font-bold text-red-600">{{ $parent['max_days_late'] }} días</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <h4 class="mb-4 font-semibold text-gray-900">Estudiantes Asociados</h4>
                                <div class="overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200 bg-gray-50">
                                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nombre del Estudiante</th>
                                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Pagos con Mora</th>
                                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Días de Mora</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parent['students'] as $student)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="px-4 py-3">
                                                        <a href="{{ route('admin.students.show', $student['id']) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                                            {{ $student['name'] }}
                                                        </a>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-red-700 font-semibold">
                                                            {{ $student['overdue_count'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block rounded-full bg-orange-100 px-3 py-1 text-orange-700 font-semibold">
                                                            {{ $student['max_days_late'] }} días
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
