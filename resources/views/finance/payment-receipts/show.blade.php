<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalle del Comprobante de Pago
            </h2>
            <a href="{{ route('finance.payment-receipts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Volver a la lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Información del Comprobante -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Información del Pago</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estudiante</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->student->user->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Padre/Tutor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->parent->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registrado por</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->registeredBy->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Pago</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->payment_date->format('d/m/Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Monto Pagado</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($paymentReceipt->amount_paid, 2) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Referencia</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->reference }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Titular de la Cuenta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->account_holder_name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Banco Emisor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->issuing_bank }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Método de Pago</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($paymentReceipt->payment_method->value === 'transfer') Transferencia
                                    @elseif($paymentReceipt->payment_method->value === 'cash') Efectivo
                                    @elseif($paymentReceipt->payment_method->value === 'card') Tarjeta
                                    @else Cheque
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado Actual</dt>
                                <dd class="mt-1">
                                    @if($paymentReceipt->status->value === 'pending')
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">Pendiente</span>
                                    @elseif($paymentReceipt->status->value === 'validated')
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">Validado</span>
                                    @else
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">Rechazado</span>
                                    @endif
                                </dd>
                            </div>

                            @if($paymentReceipt->validated_by)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Validado por</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->validatedBy->name }}</dd>
                            </div>
                            @endif

                            @if($paymentReceipt->validated_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Validación</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentReceipt->validated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @endif

                            @if($paymentReceipt->rejection_reason)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Razón de Rechazo</dt>
                                <dd class="mt-1 text-sm text-red-600">{{ $paymentReceipt->rejection_reason }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Imagen del Comprobante -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Comprobante</h3>
                    </div>
                    <div class="p-6">
                        <img src="{{ asset('storage/' . $paymentReceipt->receipt_image) }}"
                             alt="Comprobante de pago"
                             class="w-full rounded-lg border border-gray-300">
                        <a href="{{ asset('storage/' . $paymentReceipt->receipt_image) }}"
                           target="_blank"
                           class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                            Ver imagen en tamaño completo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cambiar Estado -->
            @if($paymentReceipt->status->value !== 'validated')
            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Cambiar Estado del Comprobante</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('finance.payment-receipts.update-status', $paymentReceipt) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Nuevo Estado</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pending">Pendiente</option>
                                <option value="validated">Validar Comprobante</option>
                                <option value="rejected">Rechazar Comprobante</option>
                            </select>
                        </div>

                        <div class="mb-4" id="rejection_reason_container" style="display: none;">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Razón de Rechazo</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notas Adicionales (opcional)</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Estado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Historial de Cambios -->
            @if($paymentReceipt->statusLogs->count() > 0)
            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Historial de Cambios</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($paymentReceipt->statusLogs->sortByDesc('created_at') as $log)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-medium text-gray-900">
                                @if($log->previous_status)
                                    {{ ucfirst($log->previous_status) }} ’ {{ ucfirst($log->new_status) }}
                                @else
                                    Comprobante creado ({{ ucfirst($log->new_status) }})
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">Por: {{ $log->changedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                            @if($log->notes)
                            <p class="mt-1 text-sm text-gray-700">{{ $log->notes }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            const rejectionContainer = document.getElementById('rejection_reason_container');
            const rejectionField = document.getElementById('rejection_reason');

            if (this.value === 'rejected') {
                rejectionContainer.style.display = 'block';
                rejectionField.required = true;
            } else {
                rejectionContainer.style.display = 'none';
                rejectionField.required = false;
            }
        });
    </script>
</x-app-layout>
