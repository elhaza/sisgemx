<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Disponibilidad de Docente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Editar Disponibilidad de Docente
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Actualiza la disponibilidad del docente.
                    </p>
                </div>

                <form action="{{ route('admin.teacher-availabilities.update', $teacherAvailability) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Docente -->
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700">
                                Docente *
                            </label>
                            <select name="teacher_id" id="teacher_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar docente</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $teacherAvailability->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Día de la Semana -->
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">
                                Día de la Semana *
                            </label>
                            <select name="day_of_week" id="day_of_week" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar día</option>
                                @foreach ($daysOfWeek as $key => $label)
                                    <option value="{{ $key }}" {{ old('day_of_week', $teacherAvailability->day_of_week) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora de Inicio -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">
                                Hora de Inicio *
                            </label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time', $teacherAvailability->start_time) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora de Fin -->
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">
                                Hora de Fin *
                            </label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ old('end_time', $teacherAvailability->end_time) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nota (Opcional) -->
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Nota (Opcional)
                            </label>
                            <textarea name="note" id="note" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Solo disponible en la tarde, horario sujeto a cambios, etc.">{{ old('note', $teacherAvailability->note) }}</textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.teacher-availabilities.index') }}"
                                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
