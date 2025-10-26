<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Importar Estudiantes desde Excel
            </h2>
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Volver a Estudiantes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Información -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-3 text-lg font-semibold text-gray-900">Información Importante</h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><strong>Ciclo Activo:</strong> {{ $activeSchoolYear?->name ?? 'No hay ciclo activo' }}</p>
                    <p class="mt-4"><strong>Estructura del archivo Excel:</strong></p>
                    <ul class="mt-2 list-inside list-disc space-y-1 pl-2 text-xs">
                        <li><strong>nombre_estudiante:</strong> Nombre del estudiante</li>
                        <li><strong>apellido_paterno_estudiante:</strong> Apellido paterno</li>
                        <li><strong>apellido_materno_estudiante:</strong> Apellido materno</li>
                        <li><strong>correo_estudiante:</strong> Email del estudiante</li>
                        <li><strong>nombre_padre:</strong> Nombre del padre/tutor</li>
                        <li><strong>apellido_paterno_padre:</strong> Apellido paterno (OBLIGATORIO)</li>
                        <li><strong>apellido_materno_padre:</strong> Apellido materno (OBLIGATORIO)</li>
                        <li><strong>correo_padres:</strong> Email del padre/tutor (OBLIGATORIO)</li>
                        <li><strong>matricula:</strong> Número de matrícula (opcional, se asigna automáticamente si falta)</li>
                        <li><strong>grado:</strong> Grado escolar (ej: 1, 2, 3... OBLIGATORIO)</li>
                        <li><strong>seccion:</strong> Sección (ej: A, B, C... OBLIGATORIO)</li>
                        <li><strong>sexo:</strong> Sexo (M/F, OBLIGATORIO)</li>
                        <li><strong>curp:</strong> CURP (OBLIGATORIO)</li>
                        <li><strong>fecha_nacimiento:</strong> Fecha en formato DD/MM/YYYY</li>
                        <li><strong>pais_nacimiento:</strong> País de nacimiento (OBLIGATORIO)</li>
                        <li><strong>estado_nacimiento:</strong> Estado de nacimiento (OBLIGATORIO)</li>
                        <li><strong>ciudad_nacimiento:</strong> Ciudad de nacimiento (OBLIGATORIO)</li>
                        <li><strong>telefono:</strong> Teléfono de contacto (OBLIGATORIO)</li>
                        <li><strong>domicilio:</strong> Domicilio (OBLIGATORIO)</li>
                        <li><strong>contrasena:</strong> Contraseña (opcional, usa sisgemx123 si no se proporciona)</li>
                    </ul>
                </div>
            </div>

            @if ($activeSchoolYear)
                <!-- Formulario de Importación -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <form action="{{ route('admin.students.import-upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Seleccionar archivo Excel
                            </label>
                            <div id="upload-area" class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 transition-colors hover:border-blue-400 hover:bg-blue-50">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 20M9 20l3.172-3.172a4 4 0 015.656 0L21 20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                            Sube un archivo
                                        </label>
                                        o arrastra y suelta
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        XLSX, XLS, CSV
                                    </p>
                                </div>
                                <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                                Importar Estudiantes
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Mensaje de no hay ciclo activo -->
                <div class="rounded-lg bg-yellow-50 p-6 shadow-sm">
                    <div class="flex">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47a9 9 0 1116.84 0M9 12a3 3 0 116 0 3 3 0 01-6 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-yellow-900">
                                No hay ciclo escolar activo
                            </h3>
                            <p class="mt-2 text-sm text-yellow-800">
                                Antes de importar estudiantes, necesitas crear un ciclo escolar activo.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('admin.students.create-school-year') }}" class="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700">
                                    Crear Ciclo Escolar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('file');
        const uploadArea = document.getElementById('upload-area');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                console.log('Archivo seleccionado:', this.files[0].name);
            }
        });
    </script>
</x-app-layout>
