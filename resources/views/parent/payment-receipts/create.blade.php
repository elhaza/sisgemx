<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Subir Comprobante de Pago
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">
            <!-- Tabla de Estudiantes con sus Adeudos -->
            @if($allPendingTuitions->count() > 0)
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">👨‍👩‍👧‍👦 Mis Estudiantes - Adeudos Pendientes</h3>
                        <p class="mt-1 text-sm text-gray-600">Selecciona un estudiante para subir comprobante de pago</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estudiante</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Colegiaturas Pendientes</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Total Adeudado</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Recargos</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $studentsByName = [];
                                        foreach ($allPendingTuitions as $tuition) {
                                            $studentId = $tuition->student_id;
                                            if (!isset($studentsByName[$studentId])) {
                                                $studentsByName[$studentId] = [
                                                    'student' => $tuition->student,
                                                    'tuitions' => collect(),
                                                    'total' => 0,
                                                    'totalFees' => 0,
                                                ];
                                            }
                                            $studentsByName[$studentId]['tuitions']->push($tuition);
                                            $studentsByName[$studentId]['total'] += $tuition->final_amount;
                                            $studentsByName[$studentId]['totalFees'] += $tuition->calculated_late_fee_amount;
                                        }
                                        $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    @endphp
                                    @foreach($studentsByName as $studentId => $data)
                                        @php
                                            $rowClass = $data['totalFees'] > 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $data['student']->user->full_name }}
                                                <span class="ml-2 text-xs text-gray-500">({{ $data['student']->enrollment_number }})</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <div class="space-y-1">
                                                    @foreach($data['tuitions'] as $tuition)
                                                        <div class="text-xs">
                                                            {{ $monthNames[$tuition->month] }} {{ $tuition->year }}
                                                            @if($tuition->days_late > 0)
                                                                <span class="text-red-600 font-semibold">({{ $tuition->days_late }} días)</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-gray-900">
                                                ${{ number_format($data['total'], 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                                @if($data['totalFees'] > 0)
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                        ${{ number_format($data['totalFees'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                                <button
                                                    onclick="selectStudent({{ $studentId }}, '{{ $data['student']->user->full_name }}')"
                                                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                                                    📤 Subir Comprobante
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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

            <!-- Formulario de Carga de Comprobante (Hidden by default) -->
            <div id="formContainer" style="display: none;" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">📤 Subir Comprobante de Pago</h3>
                    <p id="selectedStudentInfo" class="mt-1 text-sm text-gray-600"></p>
                </div>
                <div class="p-6">
                    <!-- Tabla de colegiaturas pendientes del estudiante seleccionado -->
                    <div class="mb-6" id="tuitionDetailsContainer" style="display: none;">
                        <h4 class="mb-3 text-sm font-semibold text-gray-900">Colegiaturas Pendientes a Pagar:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 bg-gray-50 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-900">Período</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-900">Colegiatura</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-900">Recargo</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-900">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tuitionTableBody" class="divide-y divide-gray-200">
                                </tbody>
                                <tfoot class="bg-gray-100 font-semibold">
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">TOTAL</td>
                                        <td class="px-4 py-2 text-right text-sm text-gray-900" id="totalTuitionAmount">$0.00</td>
                                        <td class="px-4 py-2 text-right text-sm text-red-800" id="totalLateFeeAmount">$0.00</td>
                                        <td class="px-4 py-2 text-right text-sm text-lg font-bold text-gray-900" id="totalDueAmount">$0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('parent.payment-receipts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="student_id" id="student_id" value="">

                        <!-- Información del Estudiante (Read-only) -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-700">
                                <strong>Estudiante Seleccionado:</strong> <span id="studentNameDisplay" class="font-semibold"></span>
                            </p>
                        </div>

                        <!-- Mes a Pagar -->
                        <div class="mb-4">
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

                        <!-- Fecha de Pago -->
                        <div class="mb-4">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Monto Pagado -->
                        <div class="mb-4">
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700">Monto Pagado</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required
                                placeholder="Ej: 5000.00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('amount_paid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Referencia / No. de Transacción -->
                        <div class="mb-4">
                            <label for="reference" class="block text-sm font-medium text-gray-700">Referencia / No. de Transacción</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" required
                                placeholder="Ej: TRANSF-20240903-1234"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombre del Titular -->
                        <div class="mb-4">
                            <label for="account_holder_name" class="block text-sm font-medium text-gray-700">Nombre del Titular</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name', auth()->user()->full_name) }}" required
                                placeholder="Nombre completo de quien realizó el pago"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('account_holder_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Banco Emisor -->
                        <div class="mb-4">
                            <label for="issuing_bank" class="block text-sm font-medium text-gray-700">Banco Emisor</label>
                            <input type="text" name="issuing_bank" id="issuing_bank" value="{{ old('issuing_bank') }}" required
                                placeholder="Ej: Banco Nacional"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('issuing_bank')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Método de Pago -->
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

                        <!-- Comprobante (Imagen) con Compresión -->
                        <div class="mb-4">
                            <label for="receipt_image" class="block text-sm font-medium text-gray-700">Comprobante (Imagen)</label>
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG. Tamaño máximo: 200KB (se comprime automáticamente)</p>
                            <div id="imageSizeWarning" class="mt-2 hidden">
                                <p id="imageSizeText" class="text-xs font-semibold text-orange-600"></p>
                            </div>
                        </div>

                        <!-- Nota -->
                        <div class="mb-4 rounded-lg bg-yellow-50 p-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Nota:</strong> Su comprobante quedará en estado "Pendiente de Validación" hasta que sea revisado por el área de finanzas.
                            </p>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end gap-4">
                            <button type="button" onclick="closeForm()" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</button>
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

        function selectStudent(studentId, studentName) {
            // Set form values
            document.getElementById('student_id').value = studentId;
            document.getElementById('studentNameDisplay').value = studentName;
            document.getElementById('selectedStudentInfo').textContent = 'Estudiante: ' + studentName;

            // Show form
            document.getElementById('formContainer').style.display = 'block';
            document.getElementById('tuitionDetailsContainer').style.display = 'block';

            // Scroll to form
            document.getElementById('formContainer').scrollIntoView({ behavior: 'smooth' });

            // Update tuition select
            updatePendingTuitions(studentId);

            // Update tuition details table
            updateTuitionDetailsTable(studentId);
        }

        function closeForm() {
            document.getElementById('formContainer').style.display = 'none';
            document.getElementById('student_id').value = '';
            document.getElementById('tuition_id').value = '';
            document.getElementById('amount_paid').value = '';
            document.getElementById('payment_year').value = '';
            document.getElementById('payment_month').value = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updatePendingTuitions(studentId) {
            const tuitionSelect = document.getElementById('tuition_id');
            const amountField = document.getElementById('amount_paid');

            // Clear existing options
            tuitionSelect.innerHTML = '<option value="">Seleccionar mes</option>';

            if (!studentId || !pendingTuitionsByStudent[studentId]) {
                return;
            }

            const pendingTuitions = pendingTuitionsByStudent[studentId];

            if (pendingTuitions.length === 0) {
                alert('Este estudiante no tiene colegiaturas pendientes.');
                return;
            }

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

        function updateTuitionDetailsTable(studentId) {
            const tbody = document.getElementById('tuitionTableBody');
            tbody.innerHTML = '';

            let totalTuition = 0;
            let totalFees = 0;

            if (pendingTuitionsByStudent[studentId]) {
                pendingTuitionsByStudent[studentId].forEach(tuition => {
                    const row = document.createElement('tr');
                    const monthName = monthNames[tuition.month];
                    const lateFeeAmount = tuition.calculated_late_fee_amount || 0;
                    const totalAmount = tuition.final_amount + lateFeeAmount;

                    totalTuition += tuition.final_amount;
                    totalFees += lateFeeAmount;

                    row.innerHTML = `
                        <td class="px-4 py-2 text-sm text-gray-900">${monthName} ${tuition.year}</td>
                        <td class="px-4 py-2 text-right text-sm text-gray-900">$${parseFloat(tuition.final_amount).toFixed(2)}</td>
                        <td class="px-4 py-2 text-right text-sm text-red-600">${lateFeeAmount > 0 ? '$' + parseFloat(lateFeeAmount).toFixed(2) : '-'}</td>
                        <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">$${parseFloat(totalAmount).toFixed(2)}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            // Update totals
            document.getElementById('totalTuitionAmount').textContent = '$' + parseFloat(totalTuition).toFixed(2);
            document.getElementById('totalLateFeeAmount').textContent = '$' + parseFloat(totalFees).toFixed(2);
            document.getElementById('totalDueAmount').textContent = '$' + parseFloat(totalTuition + totalFees).toFixed(2);
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

        // Image compression
        document.getElementById('receipt_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const maxSizeKB = 200;
            const maxSizeBytes = maxSizeKB * 1024;

            if (file.size <= maxSizeBytes) {
                // File is already small enough
                document.getElementById('imageSizeWarning').classList.add('hidden');
                return;
            }

            // Need to compress
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    // Calculate new dimensions maintaining aspect ratio
                    let width = img.width;
                    let height = img.height;
                    let quality = 0.9;

                    // Try compression with quality reduction
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);

                    // Reduce quality until under 200KB
                    let compressedBlob;
                    let attempts = 0;
                    const maxAttempts = 10;

                    function attemptCompress() {
                        canvas.toBlob(
                            function(blob) {
                                if (blob.size <= maxSizeBytes || attempts >= maxAttempts) {
                                    // Create new File object
                                    const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });

                                    // Replace file in input
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(compressedFile);
                                    document.getElementById('receipt_image').files = dataTransfer.files;

                                    // Show success message
                                    const warningDiv = document.getElementById('imageSizeWarning');
                                    warningDiv.classList.remove('hidden');
                                    const sizeText = document.getElementById('imageSizeText');
                                    sizeText.textContent = `✓ Imagen comprimida: ${(blob.size / 1024).toFixed(2)} KB (de ${(file.size / 1024).toFixed(2)} KB)`;
                                    sizeText.classList.remove('text-orange-600');
                                    sizeText.classList.add('text-green-600');
                                } else {
                                    // Try again with lower quality
                                    quality -= 0.1;
                                    attempts++;
                                    attemptCompress();
                                }
                            },
                            'image/jpeg',
                            quality
                        );
                    }

                    attemptCompress();
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>
