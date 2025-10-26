<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Franja Horaria
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Crear Nueva Franja Horaria
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Define las franjas horarias que estarán disponibles en el horario escolar.
                    </p>
                </div>

                <form action="{{ route('admin.time-slots.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Días de la Semana (Múltiples) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Días de la Semana * (Selecciona uno o más)
                            </label>
                            <div class="space-y-2">
                                @php
                                    $days = [
                                        'monday' => 'Lunes',
                                        'tuesday' => 'Martes',
                                        'wednesday' => 'Miércoles',
                                        'thursday' => 'Jueves',
                                        'friday' => 'Viernes',
                                        'saturday' => 'Sábado',
                                        'sunday' => 'Domingo',
                                    ];
                                    $selectedDays = old('days_of_week', []);
                                @endphp
                                @foreach($days as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $value }}"
                                            {{ in_array($value, $selectedDays) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('days_of_week')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora de Inicio -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">
                                Hora de Inicio *
                            </label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ old('start_time') }}"
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
                                value="{{ old('end_time') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Crear Franja Horaria
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
