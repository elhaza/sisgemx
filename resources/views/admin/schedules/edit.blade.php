<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Horario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Materia</label>
                            <select name="subject_id" id="subject_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar materia</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} - {{ $subject->teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grado</label>
                            <input type="text" name="grade_level" id="grade_level" value="{{ old('grade_level', $schedule->grade_level) }}" required
                                placeholder="Ej: Primero, Segundo, Tercero"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('grade_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="group" class="block text-sm font-medium text-gray-700">Grupo/Sección</label>
                            <input type="text" name="group" id="group" value="{{ old('group', $schedule->group) }}" required
                                placeholder="Ej: A, B, C"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('group')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">Día de la Semana</label>
                            <select name="day_of_week" id="day_of_week" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar día</option>
                                @foreach($daysOfWeek as $day)
                                    @php
                                        $dayNames = [
                                            'monday' => 'Lunes',
                                            'tuesday' => 'Martes',
                                            'wednesday' => 'Miércoles',
                                            'thursday' => 'Jueves',
                                            'friday' => 'Viernes'
                                        ];
                                    @endphp
                                    <option value="{{ $day->value }}" {{ old('day_of_week', $schedule->day_of_week->value) == $day->value ? 'selected' : '' }}>
                                        {{ $dayNames[$day->value] ?? $day->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', date('H:i', strtotime($schedule->start_time))) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">Hora de Fin</label>
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', date('H:i', strtotime($schedule->end_time))) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="classroom" class="block text-sm font-medium text-gray-700">Aula/Salón</label>
                            <input type="text" name="classroom" id="classroom" value="{{ old('classroom', $schedule->classroom) }}" required
                                placeholder="Ej: Aula 101, Salón A"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('classroom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Horario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
