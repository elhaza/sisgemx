<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Eliminar Todos los Datos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                @if (session('error'))
                    <div class="mb-4 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-6 rounded-md bg-red-50 p-4 border-l-4 border-red-400">
                    <div class="flex">
                        <div>
                            <h3 class="text-sm font-medium text-red-800">
                                Advertencia: Acción Destructiva
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Esta acción eliminará <strong>TODOS</strong> los datos del sistema</li>
                                    <li>Se truncarán todas las tablas de la base de datos</li>
                                    <li>Solo se creará un usuario administrador con email <code>admin@escuela.com</code> y contraseña <code>password</code></li>
                                    <li>Esta acción <strong>NO se puede deshacer</strong></li>
                                    <li>Se recomienda hacer un respaldo antes de proceder</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.database.clear') }}" method="POST" onsubmit="return confirm('¿REALMENTE deseas eliminar TODOS los datos del sistema? Esta acción NO se puede deshacer.');">
                    @csrf

                    <div class="mb-6">
                        <label for="special_token" class="block text-sm font-medium text-gray-700">
                            Token de Confirmación Especial
                        </label>
                        <input type="password" name="special_token" id="special_token" required placeholder="Ingresa el token especial"
                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            El token está configurado en la variable de entorno TOKEN_SPECIAL_COMMANDS
                        </p>
                        @error('special_token')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="confirmation" value="1" required
                                class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">
                                Entiendo que esta acción eliminará todos los datos y no se puede deshacer
                            </span>
                        </label>
                        @error('confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.settings.edit') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                            Eliminar Todos los Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
