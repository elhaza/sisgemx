<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold leading-tight text-gray-900">
                📋 Mis Comprobantes de Pago
            </h2>
            <a href="{{ route('parent.payment-receipts.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Comprobante
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            <!-- Métricas de Comprobantes -->
            <div class="grid gap-6 md:grid-cols-3">
                <!-- Pendientes de Validación -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Pendientes de Validar</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $pendingReceiptsCount }}</dd>
                                    <p class="text-xs text-gray-600 mt-1">En revisión por finanzas</p>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validados -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Comprobantes Validados</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $validatedReceiptsCount }} de {{ $validatedReceiptsCount + $pendingReceiptsCount }}</dd>
                                    <p class="text-xs text-gray-600 mt-1">Aprobados por finanzas</p>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rechazados -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m0 0l-2 2m2-2l2 2"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Comprobantes Rechazados</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $rejectedReceiptsCount }}</dd>
                                    <p class="text-xs text-gray-600 mt-1">Requieren acción</p>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Comprobantes -->
            @if($receipts->count() > 0)
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">📄 Historial de Comprobantes</h3>
                        <p class="mt-1 text-sm text-gray-600">Todos tus comprobantes de pago enviados</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estudiante</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Período de Pago</th>
                                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Monto</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Fecha Envío</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Estado</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($receipts as $receipt)
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'validated' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusColor = $statusColors[$receipt->status->value] ?? 'bg-gray-100 text-gray-800';
                                            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $receipt->student?->user?->full_name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                                @if($receipt->payment_month && $receipt->payment_year)
                                                    {{ $monthNames[$receipt->payment_month] ?? '' }} {{ $receipt->payment_year }}
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                                ${{ number_format($receipt->amount_paid, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                                {{ $receipt->payment_date?->format('d/m/Y') ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold leading-5 {{ $statusColor }}">
                                                    @switch($receipt->status->value)
                                        @case('pending')
                                            ⏳ Pendiente
                                        @break
                                        @case('validated')
                                            ✅ Validado
                                        @break
                                        @case('rejected')
                                            ❌ Rechazado
                                        @break
                                        @default
                                            {{ ucfirst($receipt->status->value) }}
                                    @endswitch
                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                                <a href="{{ route('parent.payment-receipts.show', $receipt) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900 font-medium">
                                                    Ver
                                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $receipts->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Sin comprobantes</h3>
                    <p class="mt-1 text-sm text-gray-600">Aún no has enviado ningún comprobante de pago.</p>
                    <a href="{{ route('parent.payment-receipts.create') }}" class="mt-4 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Subir tu Primer Comprobante
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
