<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Configuración de Colegiatura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finance.tuition-configs.update', $tuitionConfig) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <input type="text" value="{{ $tuitionConfig->schoolYear->name }}" disabled
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        <!-- Monthly Tuitions Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Colegiaturas Mensuales</h3>
                            <p class="mb-4 text-sm text-gray-600">Configure el monto de colegiatura para cada mes del ciclo escolar.</p>

                            <!-- Quick Assign Section -->
                            <div class="mb-6 rounded-md bg-blue-50 p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asignación Rápida</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                            <input type="number" id="quick_assign_amount" step="0.01" min="0" placeholder="0.00"
                                                class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <button type="button" onclick="assignToAllMonths()"
                                        class="whitespace-nowrap rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Asignar a Todos los Meses
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Este campo es solo para facilitar la asignación. Ingrese un monto y haga clic en el botón para aplicarlo a todos los meses.</p>
                            </div>

                            <div id="monthly-tuitions-container" class="space-y-3">
                                @forelse($monthlyTuitions as $index => $monthlyTuition)
                                    @php
                                        $monthNames = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                                        ];
                                        $isNew = empty($monthlyTuition['id']);
                                    @endphp
                                    <div class="flex items-center gap-4 @if($isNew)rounded-lg bg-yellow-50 border border-yellow-200 p-3 @endif">
                                        @if(!$isNew)
                                            <input type="hidden" name="monthly_tuitions[{{ $index }}][id]" value="{{ $monthlyTuition['id'] }}">
                                        @endif
                                        <input type="hidden" name="monthly_tuitions[{{ $index }}][year]" value="{{ $monthlyTuition['year'] }}">
                                        <input type="hidden" name="monthly_tuitions[{{ $index }}][month]" value="{{ $monthlyTuition['month'] }}">
                                        <label class="w-48 text-sm font-medium text-gray-700">
                                            {{ $monthNames[$monthlyTuition['month']] }} {{ $monthlyTuition['year'] }}
                                            @if($isNew)
                                                <span class="ml-2 inline-block bg-yellow-200 text-yellow-900 text-xs px-2 py-1 rounded">Nuevo</span>
                                            @endif
                                        </label>
                                        <div class="flex-1">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                                <input type="number"
                                                    name="monthly_tuitions[{{ $index }}][amount]"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    value="{{ old('monthly_tuitions.' . $index . '.amount', $monthlyTuition['amount']) }}"
                                                    placeholder="0.00"
                                                    class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($isNew)bg-yellow-50 border-yellow-300 @endif">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No hay fechas configuradas para este ciclo escolar.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('finance.tuition-configs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function assignToAllMonths() {
            const quickAmount = document.getElementById('quick_assign_amount').value;

            if (!quickAmount || parseFloat(quickAmount) <= 0) {
                alert('Por favor ingrese un monto válido mayor a 0');
                return;
            }

            // Get all monthly tuition amount inputs
            const monthlyInputs = document.querySelectorAll('input[name^="monthly_tuitions"][name$="[amount]"]');

            if (monthlyInputs.length === 0) {
                alert('No hay colegiaturas mensuales configuradas para este ciclo escolar');
                return;
            }

            // Assign the amount to all inputs
            monthlyInputs.forEach(input => {
                input.value = quickAmount;
            });
        }
    </script>
</x-app-layout>
