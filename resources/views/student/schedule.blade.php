<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mi Horario de Clases
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if($student)
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Información del Estudiante</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre</p>
                                <p class="mt-1 text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Matrícula</p>
                                <p class="mt-1 text-gray-900">{{ $student->enrollment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Grado y Grupo</p>
                                <p class="mt-1 text-gray-900">{{ $student->grade_level }} - Grupo {{ $student->group }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Horario Completo</h3>
                </div>
                <div class="p-6">
                    @if($schedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Día</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Maestro</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Hora Inicio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Hora Fin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Salón</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @php
                                        $days = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
                                        $currentDay = null;
                                    @endphp
                                    @foreach($schedules as $schedule)
                                        @php
                                            $dayName = ucfirst($schedule->day_of_week->value);
                                            $isNewDay = $currentDay !== $dayName;
                                            $currentDay = $dayName;
                                        @endphp
                                        <tr class="{{ $isNewDay ? 'border-t-2 border-gray-300' : '' }}">
                                            <td class="whitespace-nowrap px-6 py-4 {{ $isNewDay ? 'font-semibold text-gray-900' : 'text-gray-500' }}">
                                                {{ $isNewDay ? $dayName : '' }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ $schedule->subject->name }}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $schedule->subject->teacher->user->name ?? 'Sin asignar' }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $schedule->classroom ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-lg bg-yellow-50 p-4 text-center">
                            <p class="text-gray-700">No hay horarios registrados para tu grupo.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('student.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
