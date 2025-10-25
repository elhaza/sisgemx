<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Horarios
            </h2>
            <a href="{{ route('admin.schedules.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Nuevo Horario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado/Grupo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Día</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Horario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aula</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Maestro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule->subject->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->grade_level }} - {{ $schedule->group }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        @php
                                            $days = [
                                                'monday' => 'Lunes',
                                                'tuesday' => 'Martes',
                                                'wednesday' => 'Miércoles',
                                                'thursday' => 'Jueves',
                                                'friday' => 'Viernes'
                                            ];
                                        @endphp
                                        {{ $days[$schedule->day_of_week->value] ?? $schedule->day_of_week->value }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->classroom }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->subject->teacher->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este horario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay horarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $schedules->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
