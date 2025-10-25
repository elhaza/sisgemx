<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Configuración de Colegiaturas
            </h2>
            <a href="{{ route('finance.tuition-configs.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Nueva Configuración
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto Mensual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha Creación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($tuitionConfigs as $config)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $config->schoolYear->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($config->amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $config->created_at->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <a href="{{ route('finance.tuition-configs.edit', $config) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                        <form action="{{ route('finance.tuition-configs.destroy', $config) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay configuraciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $tuitionConfigs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
