<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Franja Horaria
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Editar Franja Horaria
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Actualiza los datos de la franja horaria.
                    </p>
                </div>

                <form action="{{ route('admin.time-slots.update', $timeSlot) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Día de la Semana -->
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700">
                                Día de la Semana *
                            </label>
                            <select name="day_of_week" id="day_of_week" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar día</option>
                                <option value="monday" {{ old('day_of_week', $timeSlot->day_of_week) === 'monday' ? 'selected' : '' }}>
                                    Lunes
                                </option>
                                <option value="tuesday" {{ old('day_of_week', $timeSlot->day_of_week) === 'tuesday' ? 'selected' : '' }}>
                                    Martes
                                </option>
                                <option value="wednesday" {{ old('day_of_week', $timeSlot->day_of_week) === 'wednesday' ? 'selected' : '' }}>
                                    Miércoles
                                </option>
                                <option value="thursday" {{ old('day_of_week', $timeSlot->day_of_week) === 'thursday' ? 'selected' : '' }}>
                                    Jueves
                                </option>
                                <option value="friday" {{ old('day_of_week', $timeSlot->day_of_week) === 'friday' ? 'selected' : '' }}>
                                    Viernes
                                </option>
                                <option value="saturday" {{ old('day_of_week', $timeSlot->day_of_week) === 'saturday' ? 'selected' : '' }}>
                                    Sábado
                                </option>
                                <option value="sunday" {{ old('day_of_week', $timeSlot->day_of_week) === 'sunday' ? 'selected' : '' }}>
                                    Domingo
                                </option>
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
                                value="{{ old('start_time', $timeSlot->start_time) }}"
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
                                value="{{ old('end_time', $timeSlot->end_time) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración (solo lectura) -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700">
                                Duración (minutos)
                            </label>
                            <input type="text" id="duration" disabled
                                value="{{ $timeSlot->duration_minutes }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                            <p class="mt-1 text-sm text-gray-500">
                                Se calcula automáticamente según las horas de inicio y fin.
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.time-slots.index') }}"
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
