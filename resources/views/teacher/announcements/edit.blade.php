<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Anuncio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Formulario de Edición</h3>
                </div>

                <div class="p-6">
                    <form action="{{ route('teacher.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Título -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Título del Anuncio
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="{{ old('title', $announcement->title) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                    placeholder="Ej: Cambio de horario"
                                    required
                                />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contenido -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700">
                                    Contenido del Anuncio
                                </label>
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="6"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('content') border-red-500 @enderror"
                                    placeholder="Escribe el contenido del anuncio aquí..."
                                    required
                                >{{ old('content', $announcement->content) }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Imagen -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">
                                    Imagen del Anuncio (Opcional)
                                </label>

                                @if($announcement->image_path)
                                    <div class="mb-4">
                                        <p class="mb-2 text-sm font-medium text-gray-600">Imagen actual:</p>
                                        <img
                                            src="{{ Storage::url($announcement->image_path) }}"
                                            alt="{{ $announcement->title }}"
                                            class="h-48 w-full rounded-md object-cover"
                                        />
                                    </div>
                                @endif

                                <div class="mt-1 flex items-center justify-center rounded-md border-2 border-dashed border-gray-300 px-6 py-10" id="dropzone">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <circle cx="34" cy="14" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                                            <path d="M40 40l-7.5-7.5M28 28l-7-7m14 0l-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 hover:text-blue-500">
                                                <span>Sube una imagen</span>
                                                <input
                                                    id="image"
                                                    name="image"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                                    onchange="previewImage()"
                                                />
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP hasta 2MB</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <!-- Preview de imagen -->
                                <div id="imagePreview" class="mt-4 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="h-48 w-full rounded-md object-cover">
                                    <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                        Eliminar imagen
                                    </button>
                                </div>
                            </div>

                            <!-- Fechas de Vigencia -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="valid_from" class="block text-sm font-medium text-gray-700">
                                        Válido desde (Opcional)
                                    </label>
                                    <input
                                        type="date"
                                        id="valid_from"
                                        name="valid_from"
                                        value="{{ old('valid_from', $announcement->valid_from?->format('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    />
                                    @error('valid_from')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="valid_until" class="block text-sm font-medium text-gray-700">
                                        Válido hasta (Opcional)
                                    </label>
                                    <input
                                        type="date"
                                        id="valid_until"
                                        name="valid_until"
                                        value="{{ old('valid_until', $announcement->valid_until?->format('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    />
                                    @error('valid_until')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Destinatarios -->
                            <div>
                                <label for="target_audience" class="block text-sm font-medium text-gray-700">
                                    Destinatarios (Opcional)
                                </label>
                                <div class="mt-2 space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="target_audience[]"
                                            value="students"
                                            @if(in_array('students', old('target_audience', $announcement->target_audience ?? []))) checked @endif
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Estudiantes</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="target_audience[]"
                                            value="parents"
                                            @if(in_array('parents', old('target_audience', $announcement->target_audience ?? []))) checked @endif
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Padres de Familia</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="target_audience[]"
                                            value="teachers"
                                            @if(in_array('teachers', old('target_audience', $announcement->target_audience ?? []))) checked @endif
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Maestros</span>
                                    </label>
                                </div>
                                @error('target_audience')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Información -->
                            <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600">
                                <p>
                                    <strong>Creado:</strong> {{ $announcement->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p>
                                    <strong>Última edición:</strong> {{ $announcement->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Guardar Cambios
                                </button>
                                <a
                                    href="{{ route('teacher.announcements.index') }}"
                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
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
        function previewImage() {
            const fileInput = document.getElementById('image');
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const dropzone = document.getElementById('dropzone');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    dropzone.classList.add('hidden');
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        function clearImage() {
            const fileInput = document.getElementById('image');
            const preview = document.getElementById('imagePreview');
            const dropzone = document.getElementById('dropzone');

            fileInput.value = '';
            preview.classList.add('hidden');
            dropzone.classList.remove('hidden');
        }

        // Drag and drop
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('image');

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
                previewImage();
            }
        });
    </script>
</x-app-layout>
