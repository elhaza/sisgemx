<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Cobros Adicionales
            </h2>
            @if($activeSchoolYear)
                <a href="{{ route('finance.extra-charges.create') }}"
                   class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Nuevo Cargo
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(!$activeSchoolYear)
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-6 shadow-sm">
                    <h3 class="font-semibold text-red-800">No hay ciclo escolar activo</h3>
                    <p class="mt-2 text-sm text-red-700">
                        Debes crear o activar un ciclo escolar para gestionar cargos adicionales.
                    </p>
                </div>
            @elseif($templates->isEmpty())
                <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No hay cargos configurados</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Comienza creando nuevos tipos de cargos como inscripción, materiales, etc.
                    </p>
                    <a href="{{ route('finance.extra-charges.create') }}"
                       class="mt-4 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Primer Cargo
                    </a>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($templates as $template)
                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ $template->charge_type }}</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold"
                                          :class="$template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ $template->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-6 py-4">
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Monto:</p>
                                        <p class="text-2xl font-bold text-gray-900">${{ number_format($template->amount, 2) }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-600">Fecha Vencimiento:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $template->default_due_date->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    @if($template->description)
                                        <div>
                                            <p class="text-sm text-gray-600">Descripción:</p>
                                            <p class="text-sm text-gray-700">{{ $template->description }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-sm text-gray-600">Asignaciones:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $template->assignedCharges->count() }} estudiantes
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 bg-gray-50 px-6 py-3 flex gap-2">
                                <a href="{{ route('finance.extra-charges.show', $template) }}"
                                   class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </a>
                                <a href="{{ route('finance.extra-charges.edit', $template) }}"
                                   class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </a>
                                <form action="{{ route('finance.extra-charges.destroy', $template) }}" method="POST" class="flex-1"
                                      onsubmit="return confirm('¿Está seguro? Se eliminarán todas las asignaciones asociadas.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-md border border-red-300 bg-white px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
