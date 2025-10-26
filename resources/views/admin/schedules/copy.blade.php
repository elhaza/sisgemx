<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Copiar Horarios de Ciclo Anterior
            </h2>
            <a href="{{ route('admin.schedules.visual') }}" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Volver a Horarios
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Información Importante</h3>
                        <div class="mt-2 rounded-lg bg-blue-50 p-4">
                            <ul class="list-inside list-disc space-y-2 text-sm text-blue-900">
                                <li>Esta función copiará todos los horarios de un ciclo escolar a otro</li>
                                <li>Solo se puede copiar de un ciclo escolar anterior hacia uno nuevo (no hacia atrás)</li>
                                <li>El ciclo escolar destino NO debe tener horarios previamente asignados</li>
                                <li>Los horarios copiados pueden ser editados posteriormente</li>
                                <li>Si activas las opciones de auto-creación, se crearán automáticamente grupos y materias faltantes</li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('admin.schedules.copy') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="source_school_year_id" class="block text-sm font-medium text-gray-700">
                                Ciclo Escolar Origen (desde donde copiar)
                            </label>
                            <select name="source_school_year_id" id="source_school_year_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un ciclo escolar</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" data-start-date="{{ $year->start_date->timestamp }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('source_school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="target_school_year_id" class="block text-sm font-medium text-gray-700">
                                Ciclo Escolar Destino (hacia donde copiar)
                            </label>
                            <select name="target_school_year_id" id="target_school_year_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un ciclo escolar</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" data-start-date="{{ $year->start_date->timestamp }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('target_school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900">Opciones de Auto-Creación</h4>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" name="create_missing_grades" id="create_missing_grades" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3">
                                        <label for="create_missing_grades" class="text-sm font-medium text-gray-700">
                                            Crear grupos faltantes automáticamente
                                        </label>
                                        <p class="text-xs text-gray-500">Si un grupo con el mismo nivel y sección no existe en el ciclo destino, se creará automáticamente</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" name="create_missing_subjects" id="create_missing_subjects" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3">
                                        <label for="create_missing_subjects" class="text-sm font-medium text-gray-700">
                                            Crear materias faltantes automáticamente
                                        </label>
                                        <p class="text-xs text-gray-500">Si una materia no existe en el ciclo destino, se creará automáticamente con el mismo maestro</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.schedules.visual') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Copiar Horarios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sourceSelect = document.getElementById('source_school_year_id');
            const targetSelect = document.getElementById('target_school_year_id');
            const targetOptions = Array.from(targetSelect.querySelectorAll('option'));
            const defaultOption = targetOptions[0]; // Guardar la opción "Seleccione..."

            sourceSelect.addEventListener('change', function() {
                const selectedSourceId = this.value;
                const selectedSourceOption = this.querySelector(`option[value="${selectedSourceId}"]`);

                // Limpiar el select de destino
                targetSelect.innerHTML = '';

                // Agregar opción por defecto
                targetSelect.appendChild(defaultOption.cloneNode(true));

                if (!selectedSourceId) {
                    // Si no hay origen seleccionado, mostrar todas las opciones de destino
                    targetOptions.slice(1).forEach(option => {
                        targetSelect.appendChild(option.cloneNode(true));
                    });
                    targetSelect.value = '';
                    return;
                }

                // Obtener la fecha de inicio del ciclo origen
                const sourceStartDate = parseInt(selectedSourceOption.dataset.startDate);

                // Filtrar y agregar solo ciclos posteriores al origen
                targetOptions.slice(1).forEach(option => {
                    const targetStartDate = parseInt(option.dataset.startDate);
                    const targetId = option.value;

                    // Solo mostrar si:
                    // 1. La fecha de inicio es posterior al origen
                    // 2. No es el mismo ciclo que el origen
                    if (targetStartDate > sourceStartDate && targetId !== selectedSourceId) {
                        targetSelect.appendChild(option.cloneNode(true));
                    }
                });

                // Limpiar selección anterior en destino
                targetSelect.value = '';
            });
        });
    </script>
</x-app-layout>
