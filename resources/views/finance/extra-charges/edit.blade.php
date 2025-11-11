<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Cargo: {{ $template->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <form action="{{ route('finance.extra-charges.update', $template) }}" method="POST"
                  class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="space-y-6 px-6 py-8">
                    <!-- Información Básica -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cargo</h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700">
                                    Nombre del Cargo
                                </label>
                                <input type="text" id="name" name="name"
                                       class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="Ej: Inscripción 2025"
                                       value="{{ old('name', $template->name) }}"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700">
                                    Tipo de Cargo
                                </label>
                                <input type="text" readonly
                                       class="mt-2 block w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-gray-600"
                                       value="@switch($template->charge_type)
                                           @case('inscription')
                                               Inscripción
                                               @break
                                           @case('materials')
                                               Materiales
                                               @break
                                           @case('exam')
                                               Examen
                                               @break
                                           @default
                                               Otro
                                       @endswitch">
                                <p class="mt-1 text-xs text-gray-500">No se puede cambiar el tipo de cargo</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700">
                                    Descripción (Opcional)
                                </label>
                                <textarea id="description" name="description" rows="2"
                                          class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                          placeholder="Detalles adicionales">{{ old('description', $template->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Montos y Fechas -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monto y Vencimiento</h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="amount" class="block text-sm font-semibold text-gray-700">
                                    Monto ($)
                                </label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                       class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                                       value="{{ old('amount', $template->amount) }}"
                                       required>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="default_due_date" class="block text-sm font-semibold text-gray-700">
                                    Fecha de Vencimiento
                                </label>
                                <input type="date" id="default_due_date" name="default_due_date"
                                       class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 @error('default_due_date') border-red-500 @enderror"
                                       value="{{ old('default_due_date', $template->default_due_date->format('Y-m-d')) }}"
                                       required>
                                @error('default_due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado</h3>

                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   class="h-4 w-4 border-gray-300 text-blue-600 rounded focus:ring-blue-500"
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-900 font-medium">
                                Cargo activo
                            </span>
                        </label>
                        <p class="mt-2 text-xs text-gray-600">
                            Los cargos inactivos no se pueden asignar a nuevos estudiantes
                        </p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex gap-3">
                    <a href="{{ route('finance.extra-charges.index') }}"
                       class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
