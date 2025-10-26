<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Vista Previa del Horario - {{ $schoolYear->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Resumen -->
            <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <div class="flex items-baseline">
                        <div class="text-5xl font-extrabold text-gray-900">
                            {{ $result['summary']['assignments_made'] }}
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Asignaciones Realizadas
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <div class="flex items-baseline">
                        <div class="text-5xl font-extrabold text-{{ $result['summary']['assignments_missing'] > 0 ? 'orange' : 'green' }}-900">
                            {{ $result['summary']['assignments_missing'] }}
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Materias sin Cubrir
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <p class="text-sm font-medium text-gray-500">Carga Docente</p>
                    <div class="mt-4 space-y-2">
                        @foreach ($result['summary']['teacher_loads'] as $load)
                            @if ($load['weekly'] > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="truncate text-gray-700">{{ $load['name'] }}</span>
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        {{ $load['weekly'] }}/{{ $load['max_weekly'] }}h
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <p class="text-sm font-medium text-gray-500">Estado</p>
                    <div class="mt-4">
                        @if (count($result['conflicts']) === 0)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800">
                                ✓ Sin conflictos
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-0.5 text-sm font-medium text-red-800">
                                ⚠ {{ count($result['conflicts']) }} conflictos
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Conflictos y Advertencias -->
            @if (count($result['conflicts']) > 0)
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Conflictos Detectados
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach ($result['conflicts'] as $conflict)
                                        <li>{{ $conflict }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Sugerencias -->
            @if (count($result['summary']['suggestions']) > 0)
                <div class="mb-6 rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Sugerencias de Mejora
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach ($result['summary']['suggestions'] as $suggestion)
                                        <li>{{ $suggestion }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Horarios por Grupo -->
            <div class="space-y-6">
                @foreach ($result['preview'] as $groupSchedule)
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                📘 {{ $groupSchedule['section_name'] }}
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Día
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Hora
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Materia
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Docente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Aula
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @php
                                        $hasAssignments = false;
                                    @endphp
                                    @foreach ($groupSchedule['schedule'] as $daySchedule)
                                        @if (count($daySchedule['assignments']) > 0)
                                            @php
                                                $hasAssignments = true;
                                            @endphp
                                            @foreach ($daySchedule['assignments'] as $assignment)
                                                <tr>
                                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                        {{ $daySchedule['day'] }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $assignment['start_time'])->format('H:i') }}
                                                        –
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $assignment['end_time'])->format('H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ $assignment['subject_name'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ $assignment['teacher_name'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ $assignment['classroom_code'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if (!$hasAssignments)
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Sin clases asignadas
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex gap-3 border-t border-gray-200 pt-6">
                <form action="{{ route('admin.schedules.confirm') }}" method="POST" class="flex gap-3">
                    @csrf
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        ✓ Confirmar y Guardar Horario
                    </button>
                </form>

                <a href="{{ route('admin.schedules.generate-form') }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    ← Generar Nuevamente
                </a>

                <a href="{{ route('admin.schedules.index') }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
