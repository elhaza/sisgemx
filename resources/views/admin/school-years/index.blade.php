<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Ciclos Escolares
            </h2>
            <a href="{{ route('admin.school-years.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Nuevo Ciclo Escolar
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

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha Inicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha Fin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($schoolYears as $schoolYear)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $schoolYear->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $schoolYear->start_date->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $schoolYear->end_date->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if($schoolYear->is_active)
                                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Activo</span>
                                            @else
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.school-years.edit', $schoolYear) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            @if(!$schoolYear->is_active)
                                                <form action="{{ route('admin.school-years.destroy', $schoolYear) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay ciclos escolares registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $schoolYears->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
