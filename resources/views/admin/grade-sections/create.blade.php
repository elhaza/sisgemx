<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Sección de Grado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.grade-sections.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grado</label>
                            <select name="grade_level" id="grade_level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar grado</option>
                                @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ old('grade_level') == $level ? 'selected' : '' }}>
                                        {{ $level }}° Grado
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="section" class="block text-sm font-medium text-gray-700">Sección</label>
                            <select name="section" id="section" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar sección</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}" {{ old('section') == $sec ? 'selected' : '' }}>
                                        Sección {{ $sec }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select name="school_year_id" id="school_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar ciclo escolar</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" {{ old('school_year_id', $activeSchoolYear?->id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Horario de Receso -->
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <label for="use_global_break" class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="use_global_break" name="use_global_break"
                                        value="1" checked
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Usar horario de receso global</span>
                                </label>
                            </div>
                            <p class="mb-4 text-sm text-gray-600">Si desactivas esta opción, podrás establecer un horario de receso personalizado para este grupo.</p>

                            <div id="break-time-fields" class="hidden grid grid-cols-2 gap-4">
                                <div>
                                    <label for="break_time_start" class="block text-sm font-medium text-gray-700">
                                        Hora de Inicio del Receso
                                    </label>
                                    <input type="time" name="break_time_start" id="break_time_start"
                                        value="{{ old('break_time_start') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('break_time_start')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="break_time_end" class="block text-sm font-medium text-gray-700">
                                        Hora de Fin del Receso
                                    </label>
                                    <input type="time" name="break_time_end" id="break_time_end"
                                        value="{{ old('break_time_end') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('break_time_end')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.grade-sections.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Crear Sección
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const useGlobalBreak = document.getElementById('use_global_break');
            const breakTimeFields = document.getElementById('break-time-fields');

            function updateBreakTimeVisibility() {
                if (useGlobalBreak.checked) {
                    breakTimeFields.classList.add('hidden');
                    // Clear the values when using global break
                    document.getElementById('break_time_start').value = '';
                    document.getElementById('break_time_end').value = '';
                } else {
                    breakTimeFields.classList.remove('hidden');
                }
            }

            useGlobalBreak.addEventListener('change', updateBreakTimeVisibility);
            updateBreakTimeVisibility();
        });
    </script>
</x-app-layout>
