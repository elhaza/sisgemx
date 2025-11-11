<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Crear Nuevo Cargo Adicional
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if(!$activeSchoolYear)
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-6 shadow-sm">
                    <h3 class="font-semibold text-red-800">No hay ciclo escolar activo</h3>
                    <p class="mt-2 text-sm text-red-700">
                        Debes crear o activar un ciclo escolar para crear cargos adicionales.
                    </p>
                </div>
            @else
                <form action="{{ route('finance.extra-charges.store') }}" method="POST"
                      class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    @csrf

                    <div class="space-y-6 px-6 py-8">
                        <!-- Información Básica -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cargo</h3>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700">
                                        Nombre del Cargo
                                    </label>
                                    <input type="text" id="name" name="name"
                                           class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="Ej: Inscripción 2025, Material 5to Grado"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="charge_type" class="block text-sm font-semibold text-gray-700">
                                        Tipo de Cargo
                                    </label>
                                    <select id="charge_type" name="charge_type"
                                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 @error('charge_type') border-red-500 @enderror"
                                            required>
                                        <option value="">-- Selecciona un tipo --</option>
                                        <option value="inscription" {{ old('charge_type') === 'inscription' ? 'selected' : '' }}>
                                            Inscripción
                                        </option>
                                        <option value="materials" {{ old('charge_type') === 'materials' ? 'selected' : '' }}>
                                            Materiales
                                        </option>
                                        <option value="exam" {{ old('charge_type') === 'exam' ? 'selected' : '' }}>
                                            Examen
                                        </option>
                                        <option value="other" {{ old('charge_type') === 'other' ? 'selected' : '' }}>
                                            Otro
                                        </option>
                                    </select>
                                    @error('charge_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700">
                                        Descripción (Opcional)
                                    </label>
                                    <textarea id="description" name="description" rows="2"
                                              class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                              placeholder="Detalles adicionales sobre este cargo">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Montos y Fechas -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monto y Vencimiento</h3>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="amount" class="block text-sm font-semibold text-gray-700">
                                        Monto ($)
                                    </label>
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                           class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                                           placeholder="1500.00"
                                           value="{{ old('amount') }}"
                                           required>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="default_due_date" class="block text-sm font-semibold text-gray-700">
                                        Fecha de Vencimiento
                                    </label>
                                    <input type="date" id="default_due_date" name="default_due_date"
                                           class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 @error('default_due_date') border-red-500 @enderror"
                                           value="{{ old('default_due_date') }}"
                                           required>
                                    @error('default_due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Asignación -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Asignar a Estudiantes</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        ¿A quiénes asignar este cargo?
                                    </label>

                                    <div class="space-y-3">
                                        <label class="flex items-center">
                                            <input type="radio" name="apply_to" value="all"
                                                   class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                   {{ old('apply_to') === 'all' ? 'checked' : '' }}>
                                            <span class="ml-3 text-gray-900 font-medium">
                                                Todos los estudiantes activos
                                            </span>
                                        </label>

                                        <label class="flex items-center">
                                            <input type="radio" name="apply_to" value="grade_section"
                                                   class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                   {{ old('apply_to') === 'grade_section' ? 'checked' : '' }}>
                                            <span class="ml-3 text-gray-900 font-medium">
                                                Por grupo escolar
                                            </span>
                                        </label>

                                        <label class="flex items-center">
                                            <input type="radio" name="apply_to" value="individual"
                                                   class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                   {{ old('apply_to') === 'individual' ? 'checked' : '' }}>
                                            <span class="ml-3 text-gray-900 font-medium">
                                                Estudiantes individuales
                                            </span>
                                        </label>
                                    </div>
                                    @error('apply_to')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Grupos Escolares -->
                                <div id="grade-section-container" class="hidden mt-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Selecciona los grupos
                                    </label>
                                    <div class="grid gap-2 max-h-48 overflow-y-auto rounded-lg border border-gray-300 bg-white p-3">
                                        @forelse($gradeSections as $section)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="grade_section_ids[]" value="{{ $section->id }}"
                                                       class="h-4 w-4 border-gray-300 text-blue-600 rounded focus:ring-blue-500"
                                                       {{ in_array($section->id, old('grade_section_ids', [])) ? 'checked' : '' }}>
                                                <span class="ml-2 text-gray-700">
                                                    {{ $section->level->name }} - {{ $section->name }}
                                                </span>
                                            </label>
                                        @empty
                                            <p class="text-gray-500 text-sm">No hay grupos disponibles</p>
                                        @endforelse
                                    </div>
                                    @error('grade_section_ids')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estudiantes Individuales -->
                                <div id="student-container" class="hidden mt-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Selecciona los estudiantes
                                    </label>
                                    <input type="text" id="student-search"
                                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Busca por nombre...">
                                    <div class="mt-2 grid gap-2 max-h-48 overflow-y-auto rounded-lg border border-gray-300 bg-white p-3" id="student-list">
                                        <!-- Populated by JavaScript -->
                                    </div>
                                    @error('student_ids')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex gap-3">
                        <a href="{{ route('finance.extra-charges.index') }}"
                           class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Crear Cargo
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const applyToRadios = document.querySelectorAll('input[name="apply_to"]');
            const gradeSectionContainer = document.getElementById('grade-section-container');
            const studentContainer = document.getElementById('student-container');

            function updateContainers() {
                const selected = document.querySelector('input[name="apply_to"]:checked').value;

                gradeSectionContainer.classList.toggle('hidden', selected !== 'grade_section');
                studentContainer.classList.toggle('hidden', selected !== 'individual');
            }

            applyToRadios.forEach(radio => {
                radio.addEventListener('change', updateContainers);
            });

            // Initial state
            updateContainers();

            // Student search functionality (placeholder for now)
            const searchInput = document.getElementById('student-search');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    // This would be populated by AJAX in a real implementation
                });
            }
        });
    </script>
</x-app-layout>
