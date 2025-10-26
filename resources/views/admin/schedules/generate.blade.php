<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Generar Horario Automático
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Configuración de Generación
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        El sistema generará automáticamente un horario semanal respetando todas las restricciones académicas y de disponibilidad.
                    </p>
                </div>

                <form action="{{ route('admin.schedules.generate') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Ciclo Escolar -->
                        <div>
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">
                                Ciclo Escolar *
                            </label>
                            <select name="school_year_id" id="school_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar ciclo escolar</option>
                                @foreach ($schoolYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ $activeSchoolYear?->id === $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grupo (Opcional) -->
                        <div>
                            <label for="grade_section_id" class="block text-sm font-medium text-gray-700">
                                Grupo (Opcional)
                            </label>
                            <p class="mt-1 text-sm text-gray-500">
                                Dejar en blanco para generar horario para todos los grupos
                            </p>
                            <select name="grade_section_id" id="grade_section_id"
                                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todos los grupos</option>
                            </select>
                            @error('grade_section_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Información Importante -->
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Requisitos Previos
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-inside list-disc space-y-1">
                                            <li>
                                                <a href="{{ route('admin.grade-sections.index') }}"
                                                   class="underline hover:text-blue-900 font-medium">
                                                    Grupos (Secciones de Grado) creados
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.subjects.index') }}"
                                                   class="underline hover:text-blue-900 font-medium">
                                                    Materias asignadas a grupos y docentes
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.time-slots.index') }}"
                                                   class="underline hover:text-blue-900 font-medium">
                                                    Franjas horarias (Time Slots) definidas
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.teacher-availabilities.index') }}"
                                                   class="underline hover:text-blue-900 font-medium">
                                                    Disponibilidad de docentes registrada
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.teacher-subjects.index') }}"
                                                   class="underline hover:text-blue-900 font-medium">
                                                    Competencias de docentes por materia establecidas
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Generar Propuesta de Horario
                            </button>
                            <a href="{{ route('admin.schedules.index') }}"
                                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load grade sections when school year changes
        document.getElementById('school_year_id').addEventListener('change', function() {
            const schoolYearId = this.value;
            const gradeSectionSelect = document.getElementById('grade_section_id');

            if (!schoolYearId) {
                gradeSectionSelect.innerHTML = '<option value="">Todos los grupos</option>';
                return;
            }

            // Fetch grade sections for selected school year
            fetch(`/api/school-years/${schoolYearId}/school-grades`)
                .then(response => response.json())
                .then(data => {
                    let html = '<option value="">Todos los grupos</option>';
                    data.forEach(grade => {
                        html +=
                            `<option value="${grade.id}">${grade.name}</option>`;
                    });
                    gradeSectionSelect.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</x-app-layout>
