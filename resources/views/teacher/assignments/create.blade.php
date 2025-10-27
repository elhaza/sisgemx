<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ➕ Asignar Nueva Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Formulario de Nueva Tarea</h3>
                </div>

                <div class="p-6">
                    <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Materia -->
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">
                                    Materia <span class="text-red-600">*</span>
                                </label>
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('subject_id') border-red-500 @enderror">
                                    <option value="">Seleccionar materia...</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                            {{ $subject->name }} ({{ $subject->gradeSection?->name ?? $subject->grade_level }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Título -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Título de la Tarea <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Ej: Ejercicios de Matemáticas" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Descripción <span class="text-red-600">*</span>
                                </label>
                                <textarea id="description" name="description" rows="6" required placeholder="Descripción de la tarea..." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Vencimiento -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">
                                    Fecha de Vencimiento <span class="text-red-600">*</span>
                                </label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('due_date') border-red-500 @enderror">
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Puntos Máximos -->
                            <div>
                                <label for="max_points" class="block text-sm font-medium text-gray-700">
                                    Puntos Máximos (Opcional)
                                </label>
                                <input type="number" id="max_points" name="max_points" value="{{ old('max_points') }}" placeholder="100" min="0" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('max_points') border-red-500 @enderror">
                                @error('max_points')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Archivo Adjunto -->
                            <div>
                                <label for="attachment" class="block text-sm font-medium text-gray-700">
                                    Archivo Adjunto (Imagen, Documento o PDF)
                                </label>
                                <div class="mt-1 flex items-center justify-center rounded-md border-2 border-dashed border-gray-300 px-6 py-10" id="dropzone">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <circle cx="34" cy="14" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                                            <path d="M40 40l-7.5-7.5M28 28l-7-7m14 0l-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="attachment" class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 hover:text-blue-500">
                                                <span>Sube un archivo</span>
                                                <input id="attachment" name="attachment" type="file" class="sr-only" accept="image/*,.pdf,.doc,.docx" onchange="previewFile()">
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs text-gray-500">Imágenes, PDF, Word hasta 10MB</p>
                                    </div>
                                </div>
                                @error('attachment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <!-- Preview -->
                                <div id="filePreview" class="mt-4 hidden">
                                    <p class="text-sm font-medium text-gray-600">Archivo seleccionado:</p>
                                    <p id="fileName" class="text-sm text-gray-900 font-medium mt-1"></p>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 pt-4">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Asignar Tarea
                                </button>
                                <a href="{{ route('teacher.assignments.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewFile() {
            const fileInput = document.getElementById('attachment');
            const preview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const dropzone = document.getElementById('dropzone');

            if (fileInput.files && fileInput.files[0]) {
                fileName.textContent = fileInput.files[0].name;
                preview.classList.remove('hidden');
                dropzone.classList.add('hidden');
            }
        }

        // Drag and drop
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('attachment');

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                previewFile();
            }
        });
    </script>
</x-app-layout>
