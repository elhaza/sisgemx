<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Subir Comprobante de Pago
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">
            <!-- Tabla de Adeudos -->
            @if($allPendingTuitions->count() > 0)
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">📋 Adeudos Pendientes</h3>
                        <p class="mt-1 text-sm text-gray-600">Ordenados del más antiguo al más nuevo</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estudiante</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Período</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Colegiatura</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Recargo por Mora</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Días Atraso</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $totalAmount = 0;
                                        $totalLateFees = 0;
                                        $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    @endphp
                                    @foreach($allPendingTuitions as $tuition)
                                        @php
                                            $totalAmount += $tuition->final_amount;
                                            $totalLateFees += $tuition->calculated_late_fee_amount;
                                            $rowClass = $tuition->days_late > 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $tuition->student->user->full_name }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                                {{ $monthNames[$tuition->month] }} {{ $tuition->year }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-700">
                                                ${{ number_format($tuition->final_amount, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                                @if($tuition->calculated_late_fee_amount > 0)
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                        ${{ number_format($tuition->calculated_late_fee_amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-gray-900">
                                                ${{ number_format($tuition->calculated_total_amount, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm">
                                                @if($tuition->days_late > 0)
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                        {{ $tuition->days_late }} días
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">0 días</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-semibold">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-sm text-gray-900">
                                            TOTAL A PAGAR
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-900">
                                            ${{ number_format($totalAmount, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-red-800">
                                            ${{ number_format($totalLateFees, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-lg text-gray-900">
                                            ${{ number_format($totalAmount + $totalLateFees, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-lg bg-green-50 border border-green-200 p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-900">¡Al Corriente!</h3>
                            <p class="text-sm text-green-700">No tienes adeudos pendientes en este momento.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario de Carga de Comprobante -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">📤 Subir Comprobante de Pago</h3>
                </div>
                <div class="p-6">
                    <p class="mb-6 text-sm text-gray-600">
                        Por favor complete el formulario con los datos de su transferencia. El comprobante será revisado por el área de finanzas.
                    </p>

                    <form action="{{ route('parent.payment-receipts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Estudiante</label>
                            <select name="student_id" id="student_id" required onchange="updatePendingTuitions()"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar estudiante</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->full_name }} - {{ $student->enrollment_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4" id="tuition_select_container" style="display: none;">
                            <label for="tuition_id" class="block text-sm font-medium text-gray-700">Mes a Pagar *</label>
                            <select name="tuition_id" id="tuition_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar mes</option>
                            </select>
                            <input type="hidden" name="payment_year" id="payment_year">
                            <input type="hidden" name="payment_month" id="payment_month">
                            @error('payment_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('payment_month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700">Monto Pagado</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required
                                placeholder="Ej: 5000.00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('amount_paid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="reference" class="block text-sm font-medium text-gray-700">Referencia / No. de Transacción</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" required
                                placeholder="Ej: TRANSF-20240903-1234"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="account_holder_name" class="block text-sm font-medium text-gray-700">Nombre del Titular</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name') }}" required
                                placeholder="Nombre completo de quien realizó el pago"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('account_holder_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="issuing_bank" class="block text-sm font-medium text-gray-700">Banco Emisor</label>
                            <input type="text" name="issuing_bank" id="issuing_bank" value="{{ old('issuing_bank') }}" required
                                placeholder="Ej: Banco Nacional"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('issuing_bank')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                            <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="transfer" {{ old('payment_method', 'transfer') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="receipt_image" class="block text-sm font-medium text-gray-700">Comprobante (Imagen)</label>
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</p>
                            @error('receipt_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 rounded-lg bg-yellow-50 p-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Nota:</strong> Su comprobante quedará en estado "Pendiente de Validación" hasta que sea revisado por el área de finanzas.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('parent.payment-receipts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Subir Comprobante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pending tuitions data by student
        const pendingTuitionsByStudent = @json($pendingTuitionsByStudent);
        const currentYear = {{ now()->year }};
        const currentMonth = {{ now()->month }};

        const monthNames = {
            1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
            5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
            9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
        };

        function updatePendingTuitions() {
            const studentId = document.getElementById('student_id').value;
            const tuitionSelect = document.getElementById('tuition_id');
            const container = document.getElementById('tuition_select_container');
            const amountField = document.getElementById('amount_paid');

            // Clear existing options
            tuitionSelect.innerHTML = '<option value="">Seleccionar mes</option>';

            if (!studentId || !pendingTuitionsByStudent[studentId]) {
                container.style.display = 'none';
                return;
            }

            const pendingTuitions = pendingTuitionsByStudent[studentId];

            if (pendingTuitions.length === 0) {
                container.style.display = 'none';
                alert('Este estudiante no tiene colegiaturas pendientes.');
                return;
            }

            // Show container
            container.style.display = 'block';

            // Add options for each pending tuition
            pendingTuitions.forEach((tuition, index) => {
                const option = document.createElement('option');
                option.value = tuition.id;
                option.dataset.year = tuition.year;
                option.dataset.month = tuition.month;
                option.dataset.amount = tuition.calculated_total_amount;

                const monthName = monthNames[tuition.month];
                let label = `${monthName} ${tuition.year}`;

                // Mark current month
                if (tuition.year === currentYear && tuition.month === currentMonth) {
                    label += ' (Mes actual)';
                }

                // Show amount including late fee
                label += ` - $${parseFloat(tuition.calculated_total_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;

                if (tuition.calculated_late_fee_amount > 0) {
                    label += ` (Inc. recargo: $${parseFloat(tuition.calculated_late_fee_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')})`;
                }

                option.text = label;

                // Select first option by default
                if (index === 0) {
                    option.selected = true;
                }

                tuitionSelect.appendChild(option);
            });

            // Trigger change to update amount
            updateAmount();
        }

        function updateAmount() {
            const tuitionSelect = document.getElementById('tuition_id');
            const selectedOption = tuitionSelect.options[tuitionSelect.selectedIndex];
            const amountField = document.getElementById('amount_paid');
            const yearField = document.getElementById('payment_year');
            const monthField = document.getElementById('payment_month');

            if (selectedOption && selectedOption.value) {
                const amount = selectedOption.dataset.amount;
                const year = selectedOption.dataset.year;
                const month = selectedOption.dataset.month;

                amountField.value = parseFloat(amount).toFixed(2);
                yearField.value = year;
                monthField.value = month;
            }
        }

        // Add event listener to tuition select
        document.getElementById('tuition_id').addEventListener('change', updateAmount);

        // Auto-select first student if only one
        document.addEventListener('DOMContentLoaded', function() {
            const studentSelect = document.getElementById('student_id');
            if (studentSelect.options.length === 2) { // Only "Seleccionar" + 1 student
                studentSelect.selectedIndex = 1;
                updatePendingTuitions();
            }
        });
    </script>
</x-app-layout>
