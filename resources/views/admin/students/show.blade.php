<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalles del Estudiante
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Editar
                </a>
                <a href="{{ route('admin.students.index') }}" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <!-- Información Personal -->
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Personal</h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $student->user->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $student->enrollment_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CURP</label>
                            <p class="mt-1 text-gray-900">{{ $student->curp ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Género</label>
                            <p class="mt-1 text-gray-900">
                                @if($student->gender === 'male')
                                    Masculino
                                @elseif($student->gender === 'female')
                                    Femenino
                                @else
                                    No especificado
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <p class="mt-1 text-gray-900">{{ $student->phone_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <p class="mt-1">
                                <span class="inline-block rounded-full px-3 py-1 text-sm font-semibold {{ $student->status->value === 'active' ? 'bg-green-100 text-green-700' : ($student->status->value === 'graduated' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($student->status->value) }}
                                </span>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <p class="mt-1 text-gray-900">{{ $student->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Académica -->
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Académica</h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $student->schoolYear->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grado</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $student->schoolGrade->grade_level ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sección</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $student->schoolGrade->section ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Tutores -->
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tutores/Padres</h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        @if($student->tutor1)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <h4 class="font-semibold text-gray-900">Padre/Tutor 1</h4>
                                <p class="mt-2 text-gray-700">
                                    <span class="font-medium">Nombre:</span> {{ $student->tutor1->full_name }}
                                </p>
                                <p class="mt-1 text-gray-700">
                                    <span class="font-medium">Email:</span>
                                    <a href="mailto:{{ $student->tutor1->email }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $student->tutor1->email }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($student->tutor2)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <h4 class="font-semibold text-gray-900">Padre/Tutor 2</h4>
                                <p class="mt-2 text-gray-700">
                                    <span class="font-medium">Nombre:</span> {{ $student->tutor2->full_name }}
                                </p>
                                <p class="mt-1 text-gray-700">
                                    <span class="font-medium">Email:</span>
                                    <a href="mailto:{{ $student->tutor2->email }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $student->tutor2->email }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colegiaturas -->
            <div id="colegiaturas" class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Colegiaturas</h3>
                </div>
                <div class="p-6">
                    @if($student->tuitions->isEmpty())
                        <p class="text-gray-500">No hay colegiaturas registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 bg-gray-50">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Período</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Monto Base / Descuento</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Recargos</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Total a Pagar</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Vencimiento</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Estado</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->tuitions as $tuition)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                {{ $tuition->month_name }} {{ $tuition->year }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="font-semibold">${{ number_format($tuition->monthly_amount, 2) }}</div>
                                                @if($tuition->discount_percentage > 0)
                                                    <div class="text-xs text-green-600 mt-1">
                                                        -{{ number_format($tuition->discount_percentage, 2) }}%
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" onclick="openEditLateFeeModal({{ $tuition->id }}, {{ $tuition->calculated_late_fee_amount }}, {{ $tuition->final_amount }})" class="inline-block rounded-full {{ $tuition->calculated_late_fee_amount > 0 ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-3 py-1 font-semibold cursor-pointer transition">
                                                    ${{ number_format($tuition->calculated_late_fee_amount, 2) }}
                                                </button>
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold">
                                                <span id="total-{{ $tuition->id }}">
                                                    ${{ number_format($tuition->calculated_total_amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $tuition->due_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($tuition->isPaid())
                                                    <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-blue-700 font-semibold">
                                                        Pagada
                                                    </span>
                                                @elseif($tuition->due_date && $tuition->due_date->isPast())
                                                    <button type="button" onclick="openPaymentDetailsModal({{ $student->id }}, {{ $tuition->id }})" class="inline-block rounded-full bg-red-100 px-3 py-1 text-red-700 font-semibold hover:bg-red-200 cursor-pointer transition">
                                                        Vencida ({{ $tuition->days_late }} días)
                                                    </button>
                                                @else
                                                    <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-green-700 font-semibold">
                                                        Pendiente
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex gap-2 justify-center flex-wrap">
                                                    @if($tuition->isPaid())
                                                        <span class="rounded-md bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                                                            Liquidada
                                                        </span>
                                                    @else
                                                        @php
                                                            $hasEarlierUnpaidTuition = $student->tuitions
                                                                ->where('school_year_id', $tuition->school_year_id)
                                                                ->filter(function ($t) use ($tuition) {
                                                                    if ($t->year < $tuition->year) return true;
                                                                    if ($t->year === $tuition->year && $t->month < $tuition->month) return true;
                                                                    return false;
                                                                })
                                                                ->filter(function ($t) {
                                                                    return ! $t->isPaid();
                                                                })
                                                                ->count() > 0;
                                                        @endphp
                                                        <form action="{{ route('admin.students.pay-tuition', [$student, $tuition]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @if($hasEarlierUnpaidTuition)
                                                                <button type="button" class="rounded-md bg-gray-400 px-3 py-1 text-sm font-medium text-white transition cursor-not-allowed" disabled title="Debe liquidar primero las mensualidades anteriores">
                                                                    Liquidar
                                                                </button>
                                                            @else
                                                                <button type="submit" class="rounded-md bg-green-600 px-3 py-1 text-sm font-medium text-white hover:bg-green-700 transition" onclick="return confirm('¿Liquidar mensualidad de ${{ number_format($tuition->calculated_total_amount, 2) }}?')">
                                                                    Liquidar
                                                                </button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar recargos -->
    <div id="editLateFeeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Recargos por Mora</h3>
            <form id="editLateFeeForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto de Recargos</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-700">$</span>
                        <input type="number" id="lateFeeAmount" name="late_fee_amount" step="0.01" min="0" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total a Pagar (Mensualidad + Recargos)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-700">$</span>
                        <input type="text" id="totalAmount" disabled class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-900">
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditLateFeeModal()" class="flex-1 rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para detalles de adeudo y recargos -->
    <div id="paymentDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-96 overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Detalles de Adeudo</h3>
                <button type="button" onclick="closePaymentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="paymentDetailsContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>

    <script>
        let currentTuitionId = null;
        let currentFinalAmount = 0;

        function openEditLateFeeModal(tuitionId, lateFeeAmount, finalAmount) {
            currentTuitionId = tuitionId;
            currentFinalAmount = finalAmount;
            document.getElementById('lateFeeAmount').value = lateFeeAmount;
            updateTotal();
            document.getElementById('editLateFeeModal').classList.remove('hidden');
            document.getElementById('editLateFeeForm').action = `{{ route('admin.students.update-late-fee', [$student, ':tuitionId']) }}`.replace(':tuitionId', tuitionId);
        }

        function closeEditLateFeeModal() {
            document.getElementById('editLateFeeModal').classList.add('hidden');
        }

        document.getElementById('lateFeeAmount').addEventListener('input', updateTotal);

        function updateTotal() {
            const lateFeeAmount = parseFloat(document.getElementById('lateFeeAmount').value) || 0;
            const total = currentFinalAmount + lateFeeAmount;
            document.getElementById('totalAmount').value = total.toFixed(2);
        }

        document.getElementById('editLateFeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            this.submit();
        });

        // Close modal when clicking outside
        document.getElementById('editLateFeeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditLateFeeModal();
            }
        });

        // Payment Details Modal Functions
        function openPaymentDetailsModal(studentId, tuitionId) {
            const modal = document.getElementById('paymentDetailsModal');
            const content = document.getElementById('paymentDetailsContent');

            // Show loading state
            content.innerHTML = '<div class="text-center py-8"><div class="inline-block"><svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div></div>';
            modal.classList.remove('hidden');

            // Fetch payment details
            fetch(`/admin/students/${studentId}/tuitions/${tuitionId}/details`)
                .then(response => {
                    if (!response.ok) throw new Error('Error al obtener detalles');
                    return response.json();
                })
                .then(data => {
                    renderPaymentDetails(data.data);
                })
                .catch(error => {
                    content.innerHTML = `<div class="text-center py-8 text-red-600"><p>${error.message}</p></div>`;
                });
        }

        function closePaymentDetailsModal() {
            document.getElementById('paymentDetailsModal').classList.add('hidden');
        }

        function renderPaymentDetails(data) {
            const content = document.getElementById('paymentDetailsContent');
            const debtDetails = data.debt_details;
            const lateFeeDetails = data.late_fee_details;
            const paymentSummary = data.payment_summary;
            const explanation = lateFeeDetails.explanation;

            let html = `
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="border-b pb-4">
                        <h3 class="text-xl font-bold text-gray-900">${data.period}</h3>
                        <p class="text-sm text-gray-600">Estudiante: ${data.student_name}</p>
                        <p class="text-sm text-gray-600">Vencimiento: ${data.due_date}</p>
                    </div>

                    <!-- Detalles de Adeudo -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Detalles de Adeudo</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monto Base:</span>
                                <span class="font-semibold">$${parseFloat(debtDetails.base_amount).toFixed(2)}</span>
                            </div>
                            ${debtDetails.discount_percentage > 0 ? `
                                <div class="flex justify-between text-green-700">
                                    <span class="text-gray-600">Descuento (${parseFloat(debtDetails.discount_percentage).toFixed(2)}%):</span>
                                    <span class="font-semibold">-$${parseFloat(debtDetails.discount_amount).toFixed(2)}</span>
                                </div>
                                ${debtDetails.discount_reason ? `
                                    <div class="text-xs text-gray-500 italic">
                                        Razón: ${debtDetails.discount_reason}
                                    </div>
                                ` : ''}
                            ` : ''}
                            <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                                <span>Monto a Liquidar:</span>
                                <span>$${parseFloat(debtDetails.final_amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de Recargos por Mora -->
                    ${explanation && Object.keys(explanation).length > 0 ? `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-semibold text-red-900 mb-3">Detalle de Recargos por Mora</h4>
                            <div class="space-y-3 text-sm">
                                <div class="grid grid-cols-2 gap-2">
                                    <span class="text-gray-600">Días Vencido:</span>
                                    <span class="font-semibold text-red-700">${explanation.days_late} días</span>

                                    ${explanation.grace_period_days ? `
                                        <span class="text-gray-600">Período de Gracia:</span>
                                        <span class="font-semibold">${explanation.grace_period_days} días</span>
                                    ` : ''}

                                    <span class="text-gray-600">Días Facturables:</span>
                                    <span class="font-semibold text-red-700">${explanation.billable_days} días</span>
                                </div>

                                <div class="border-t border-red-200 pt-3">
                                    <p class="text-gray-700 font-medium mb-2">Cálculo del Recargo:</p>
                                    <p class="text-gray-600 bg-white p-2 rounded">${explanation.description}</p>
                                </div>

                                ${explanation.fee_type === 'DAILY' && explanation.calculated_from === 'amount_per_day' ? `
                                    <div class="bg-white p-2 rounded text-xs">
                                        <p><span class="font-semibold">Recargo diario:</span> $${parseFloat(explanation.daily_fee).toFixed(2)}</p>
                                        <p><span class="font-semibold">Cálculo:</span> ${explanation.billable_days} días × $${parseFloat(explanation.daily_fee).toFixed(2)} = $${parseFloat(explanation.billable_days * explanation.daily_fee).toFixed(2)}</p>
                                    </div>
                                ` : ''}

                                ${explanation.fee_type === 'MONTHLY' && explanation.calculated_from === 'amount_per_month' ? `
                                    <div class="bg-white p-2 rounded text-xs">
                                        <p><span class="font-semibold">Recargo mensual:</span> $${parseFloat(explanation.monthly_fee).toFixed(2)}</p>
                                        <p><span class="font-semibold">Meses vencidos:</span> ${Math.ceil(explanation.billable_days / 30)}</p>
                                    </div>
                                ` : ''}

                                <div class="border-t border-red-200 pt-3 flex justify-between font-bold text-red-700">
                                    <span>Total Recargos:</span>
                                    <span>$${parseFloat(lateFeeDetails.amount).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    ` : ''}

                    <!-- Resumen de Pago -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-3">Resumen de Pago</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Colegiatura:</span>
                                <span class="font-semibold">$${parseFloat(paymentSummary.base_tuition).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Recargos por Mora:</span>
                                <span class="font-semibold text-red-700">$${parseFloat(paymentSummary.late_fees).toFixed(2)}</span>
                            </div>
                            <div class="border-t border-blue-200 pt-2 flex justify-between font-bold text-lg text-blue-900">
                                <span>Total a Pagar:</span>
                                <span>$${parseFloat(paymentSummary.total_due).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            content.innerHTML = html;
        }

        // Close payment details modal when clicking outside
        document.getElementById('paymentDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentDetailsModal();
            }
        });
    </script>
</x-app-layout>
