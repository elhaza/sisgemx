<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Restaurar Base de Datos
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

                @if (count($backups) === 0)
                    <div class="mb-4 rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    No hay respaldos disponibles en el sistema.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="mb-6 text-sm text-gray-700">
                        Selecciona un respaldo para restaurar la base de datos. <strong>Advertencia:</strong> Esta acción reemplazará todos los datos actuales con los datos del respaldo seleccionado.
                    </p>

                    <form action="{{ route('admin.database.restore') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas restaurar la base de datos desde este respaldo? Esta acción no se puede deshacer.');">
                        @csrf

                        <div class="mb-6">
                            <label for="backup_file" class="block text-sm font-medium text-gray-700">
                                Selecciona un Respaldo
                            </label>
                            <select name="backup_file" id="backup_file" required
                                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Selecciona un respaldo --</option>
                                @foreach ($backups as $backup)
                                    <option value="{{ $backup['filename'] }}">
                                        {{ $backup['filename'] }} ({{ number_format($backup['size'] / 1024, 2) }} KB - {{ date('Y-m-d H:i:s', $backup['date']) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('backup_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="confirmation" value="1" required
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    Entiendo que esta acción reemplazará todos los datos actuales
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
                                Restaurar Base de Datos
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
