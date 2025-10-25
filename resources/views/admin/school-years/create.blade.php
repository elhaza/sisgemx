<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nuevo Ciclo Escolar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.school-years.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Ciclo</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                placeholder="Ej: {{ date('Y') }}-{{ date('Y') + 1 }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Marcar como ciclo activo</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Si se marca como activo, el ciclo anterior será desactivado automáticamente.</p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Copy from Previous School Year Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Copiar Desde Ciclo Anterior</h3>
                            <p class="mb-4 text-sm text-gray-600">Seleccione qué elementos desea copiar del ciclo escolar anterior.</p>

                            <div class="space-y-3">
                                <label class="flex items-start">
                                    <input type="checkbox" name="copy_groups" id="copy_groups" value="1" {{ old('copy_groups') ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Copiar grupos</span>
                                        <p class="text-xs text-gray-500">Se crearán los mismos grupos (grados y secciones) del ciclo anterior.</p>
                                    </div>
                                </label>

                                <label class="flex items-start">
                                    <input type="checkbox" name="copy_subjects" id="copy_subjects" value="1" {{ old('copy_subjects') ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Copiar materias con maestros</span>
                                        <p class="text-xs text-gray-500">Se copiarán las materias con sus maestros asignados del ciclo anterior.</p>
                                    </div>
                                </label>

                                <label class="flex items-start">
                                    <input type="checkbox" name="copy_schedules" id="copy_schedules" value="1" {{ old('copy_schedules') ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Copiar horarios</span>
                                        <p class="text-xs text-gray-500">Se copiarán los horarios de clases del ciclo anterior.</p>
                                    </div>
                                </label>

                                <label class="flex items-start">
                                    <input type="checkbox" name="copy_students" id="copy_students" value="1" {{ old('copy_students') ? 'checked' : '' }}
                                        class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Asignar estudiantes al nuevo ciclo</span>
                                        <p class="text-xs text-gray-500">Después de crear el ciclo, podrá asignar estudiantes activos al nivel superior y crear sus colegiaturas.</p>
                                    </div>
                                </label>
                            </div>
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

                            <div id="monthly-tuitions-container">
                                <!-- Monthly tuition inputs will be generated here by JavaScript -->
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.school-years.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Crear Ciclo Escolar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        function assignToAllMonths() {
            const quickAmount = document.getElementById('quick_assign_amount').value;

            if (!quickAmount || parseFloat(quickAmount) <= 0) {
                alert('Por favor ingrese un monto válido mayor a 0');
                return;
            }

            // Get all monthly tuition amount inputs
            const monthlyInputs = document.querySelectorAll('input[name^="monthly_tuitions"][name$="[amount]"]');

            if (monthlyInputs.length === 0) {
                alert('Primero seleccione las fechas de inicio y fin del ciclo escolar');
                return;
            }

            // Assign the amount to all inputs
            monthlyInputs.forEach(input => {
                input.value = quickAmount;
            });
        }

        function generateMonthlyTuitions() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const container = document.getElementById('monthly-tuitions-container');

            if (!startDate || !endDate) {
                container.innerHTML = '<p class="text-sm text-gray-500">Seleccione las fechas de inicio y fin para configurar las colegiaturas mensuales.</p>';
                return;
            }

            const start = new Date(startDate);
            const end = new Date(endDate);
            const months = [];

            // Generate all months between start and end date
            let current = new Date(start.getFullYear(), start.getMonth(), 1);
            while (current <= end) {
                months.push({
                    year: current.getFullYear(),
                    month: current.getMonth() + 1,
                    name: monthNames[current.getMonth()]
                });
                current.setMonth(current.getMonth() + 1);
            }

            // Generate HTML for each month
            let html = '<div class="space-y-3">';
            months.forEach((monthData, index) => {
                const oldValue = '{{ old("monthly_tuitions.' + index + '.amount", "") }}';
                html += `
                    <div class="flex items-center gap-4">
                        <input type="hidden" name="monthly_tuitions[${index}][year]" value="${monthData.year}">
                        <input type="hidden" name="monthly_tuitions[${index}][month]" value="${monthData.month}">
                        <label class="w-48 text-sm font-medium text-gray-700">
                            ${monthData.name} ${monthData.year}
                        </label>
                        <div class="flex-1">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                <input type="number"
                                    name="monthly_tuitions[${index}][amount]"
                                    step="0.01"
                                    min="0"
                                    required
                                    value="${oldValue}"
                                    placeholder="0.00"
                                    class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
        }

        // Listen to date changes
        document.getElementById('start_date').addEventListener('change', generateMonthlyTuitions);
        document.getElementById('end_date').addEventListener('change', generateMonthlyTuitions);

        // Generate on page load if dates are already filled
        document.addEventListener('DOMContentLoaded', generateMonthlyTuitions);
    </script>
</x-app-layout>
