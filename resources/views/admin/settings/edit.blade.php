<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Configuración General
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <!-- Sección de Importar Estudiantes -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Importar Estudiantes</h3>
                </div>
                <div class="p-6">
                    <p class="mb-4 text-sm text-gray-700">
                        Importa estudiantes desde un archivo Excel. El sistema creará automáticamente los usuarios de estudiantes y padres, asignará números de matrícula y configurará los ciclos escolares si es necesario.
                    </p>
                    <a href="{{ route('admin.students.import') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Importar Estudiantes
                    </a>
                </div>
            </div>

            <!-- Sección de Operaciones de Base de Datos -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Operaciones de Base de Datos</h3>
                </div>
                <div class="space-y-4 p-6">
                    <!-- Backup Section -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Respaldar Base de Datos</h4>
                            <p class="mt-1 text-sm text-gray-600">
                                Crea una copia de seguridad de todos los datos del sistema. Los respaldos se almacenan en el servidor.
                            </p>
                        </div>
                        <form action="{{ route('admin.database.backup') }}" method="GET" style="display: inline;">
                            <button type="submit"
                                class="ml-4 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 whitespace-nowrap">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Respaldar Ahora
                            </button>
                        </form>
                    </div>

                    <!-- Restore Section -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Restaurar Base de Datos</h4>
                            <p class="mt-1 text-sm text-gray-600">
                                Restaura los datos del sistema desde un respaldo anterior. Esta acción reemplazará todos los datos actuales.
                            </p>
                        </div>
                        <a href="{{ route('admin.database.restore-form') }}"
                            class="ml-4 inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 whitespace-nowrap">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Restaurar
                        </a>
                    </div>

                    <!-- Clear All Section -->
                    <div class="flex items-start justify-between rounded-lg border border-2 border-red-300 bg-red-50 p-4">
                        <div>
                            <h4 class="font-medium text-red-900">Eliminar Todos los Datos</h4>
                            <p class="mt-1 text-sm text-red-800">
                                <strong>Advertencia:</strong> Esta acción eliminará TODOS los datos del sistema de forma irreversible. Solo se conservará un usuario administrador por defecto. Se requiere un token especial para confirmar.
                            </p>
                        </div>
                        <a href="{{ route('admin.database.clear-form') }}"
                            class="ml-4 inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 whitespace-nowrap">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar Datos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sección de Información de la Escuela -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información de la Institución</h3>
                </div>
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        {{ session('success') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nombre de la Escuela -->
                        <div class="mb-6">
                            <label for="school_name" class="block text-sm font-medium text-gray-700">
                                Nombre de la Institución
                            </label>
                            <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $settings?->school_name) }}"
                                placeholder="Ej: Instituto Tecnológico San Martín"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('school_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Este nombre aparecerá en el título de las páginas y en el encabezado.</p>
                        </div>

                        <!-- Logo de la Escuela -->
                        <div class="mb-6">
                            <label for="school_logo" class="block text-sm font-medium text-gray-700">
                                Logo de la Institución
                            </label>
                            <div class="mt-2">
                                <div id="preview-container-school" class="mb-4 flex justify-center hidden">
                                    <img id="preview-image-school" src="" alt="Preview" class="h-32 w-auto object-contain">
                                </div>
                                @if ($settings?->school_logo)
                                    <div id="current-logo-school" class="mb-4 flex justify-center">
                                        <img src="{{ asset('storage/' . $settings->school_logo) }}"
                                            alt="Logo"
                                            class="h-32 w-auto object-contain">
                                    </div>
                                @endif
                                <div id="upload-area-school" class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 transition-colors hover:border-blue-400 hover:bg-blue-50">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 20M9 20l3.172-3.172a4 4 0 015.656 0L21 20"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <label for="school_logo" class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                                Sube un archivo
                                            </label>
                                            o arrastra y suelta
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF o WEBP hasta 2MB
                                        </p>
                                    </div>
                                    <input id="school_logo" name="school_logo" type="file" class="sr-only" accept="image/png,image/jpeg,image/gif,image/webp">
                                </div>
                            </div>
                            @error('school_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Este logo aparecerá en el favicon del navegador y en el encabezado de las páginas.</p>
                        </div>

                        <!-- Horario de Receso Global -->
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h4 class="mb-4 text-base font-semibold text-gray-800">Horario de Receso (Predeterminado Global)</h4>
                            <p class="mb-4 text-sm text-gray-600">Este es el horario de receso predeterminado para todos los grupos. Cada grupo puede tener su propio horario si es necesario.</p>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="break_time_start" class="block text-sm font-medium text-gray-700">
                                        Hora de Inicio del Receso
                                    </label>
                                    <input type="time" name="break_time_start" id="break_time_start"
                                        value="{{ old('break_time_start', $settings?->break_time_start) }}"
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
                                        value="{{ old('break_time_end', $settings?->break_time_end) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('break_time_end')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mb-6">
                            <button type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Guardar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sección de Logo (Legacy) -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Logotipo de la Institución (Heredado)</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="logo" class="block text-sm font-medium text-gray-700">
                                Logotipo de la Institución
                            </label>
                            <div class="mt-2">
                                <div id="preview-container" class="mb-4 flex justify-center hidden">
                                    <img id="preview-image" src="" alt="Preview" class="h-32 w-auto object-contain">
                                </div>
                                @if ($settings?->logo_path)
                                    <div id="current-logo" class="mb-4 flex justify-center">
                                        <img src="{{ asset('storage/' . $settings->logo_path) }}"
                                            alt="Logo"
                                            class="h-32 w-auto object-contain">
                                    </div>
                                @endif
                                <div id="upload-area" class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 transition-colors hover:border-blue-400 hover:bg-blue-50">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 20M9 20l3.172-3.172a4 4 0 015.656 0L21 20"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <label for="logo" class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                                Sube un archivo
                                            </label>
                                            o arrastra y suelta
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF o WEBP hasta 2MB
                                        </p>
                                    </div>
                                    <input id="logo" name="logo" type="file" class="sr-only" accept="image/png,image/jpeg,image/gif,image/webp">
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <script>
                            // School logo handler
                            const schoolLogoInput = document.getElementById('school_logo');
                            const uploadAreaSchool = document.getElementById('upload-area-school');
                            const previewContainerSchool = document.getElementById('preview-container-school');
                            const previewImageSchool = document.getElementById('preview-image-school');
                            const currentLogoSchool = document.getElementById('current-logo-school');

                            if (schoolLogoInput) {
                                // Click to select file
                                schoolLogoInput.addEventListener('change', handleFileSelectSchool);

                                // Drag and drop
                                uploadAreaSchool.addEventListener('dragover', (e) => {
                                    e.preventDefault();
                                    uploadAreaSchool.classList.add('border-blue-400', 'bg-blue-50');
                                });

                                uploadAreaSchool.addEventListener('dragleave', () => {
                                    uploadAreaSchool.classList.remove('border-blue-400', 'bg-blue-50');
                                });

                                uploadAreaSchool.addEventListener('drop', (e) => {
                                    e.preventDefault();
                                    uploadAreaSchool.classList.remove('border-blue-400', 'bg-blue-50');

                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        schoolLogoInput.files = files;
                                        handleFileSelectSchool();
                                    }
                                });

                                function handleFileSelectSchool() {
                                    const file = schoolLogoInput.files[0];
                                    if (file && file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            previewImageSchool.src = e.target.result;
                                            previewContainerSchool.classList.remove('hidden');
                                            if (currentLogoSchool) {
                                                currentLogoSchool.classList.add('hidden');
                                            }
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                }
                            }
                        </script>

                        <script>
                            const logoInput = document.getElementById('logo');
                            const uploadArea = document.getElementById('upload-area');
                            const previewContainer = document.getElementById('preview-container');
                            const previewImage = document.getElementById('preview-image');
                            const currentLogo = document.getElementById('current-logo');

                            // Click to select file
                            logoInput.addEventListener('change', handleFileSelect);

                            // Drag and drop
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
                                    logoInput.files = files;
                                    handleFileSelect();
                                }
                            });

                            function handleFileSelect() {
                                const file = logoInput.files[0];
                                if (file && file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        previewImage.src = e.target.result;
                                        previewContainer.classList.remove('hidden');
                                        if (currentLogo) {
                                            currentLogo.classList.add('hidden');
                                        }
                                    };
                                    reader.readAsDataURL(file);
                                }
                            }
                        </script>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
