<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Configuración General
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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

                        <div class="mb-6">
                            <label for="logo" class="block text-sm font-medium text-gray-700">
                                Logotipo de la Institución
                            </label>
                            <div class="mt-2">
                                @if ($settings?->logo_path)
                                    <div class="mb-4 flex justify-center">
                                        <img src="{{ asset('storage/' . $settings->logo_path) }}"
                                            alt="Logo"
                                            class="h-32 w-auto object-contain">
                                    </div>
                                @endif
                                <div class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10">
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
