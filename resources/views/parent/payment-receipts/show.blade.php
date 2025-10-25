<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalle del Comprobante de Pago
            </h2>
            <a href="{{ route('parent.payment-receipts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
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

            <!-- Mensajes de estado para el padre -->
            @if($paymentReceipt->status->value === 'pending')
            <div class="mt-6 rounded-lg bg-yellow-50 p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Su comprobante está siendo revisado</h3>
                        <p class="mt-2 text-sm text-yellow-700">
                            El área de finanzas validará su comprobante en breve. Le notificaremos cuando haya una actualización.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($paymentReceipt->status->value === 'validated')
            <div class="mt-6 rounded-lg bg-green-50 p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Comprobante validado</h3>
                        <p class="mt-2 text-sm text-green-700">
                            Su pago ha sido verificado y aplicado correctamente. Gracias por su puntualidad.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($paymentReceipt->status->value === 'rejected')
            <div class="mt-6 rounded-lg bg-red-50 p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Comprobante rechazado</h3>
                        <p class="mt-2 text-sm text-red-700">
                            Por favor revise la razón del rechazo y suba un nuevo comprobante corrigiendo los detalles indicados.
                        </p>
                    </div>
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
                                    {{ ucfirst($log->previous_status) }} → {{ ucfirst($log->new_status) }}
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
</x-app-layout>
