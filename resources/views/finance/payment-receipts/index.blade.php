<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Comprobantes de Pago
            </h2>
            <a href="{{ route('finance.payment-receipts.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Registrar Comprobante
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Estadísticas -->
            <div class="mb-6 grid gap-6 md:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Comprobantes Pendientes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $pendingReceiptsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $validatedReceiptsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Ingresos del Mes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">${{ number_format($monthlyIncome, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Comprobantes Rechazados</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $rejectedReceiptsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <!-- Filtros -->
            <div class="mb-4">
                <form action="{{ route('finance.payment-receipts.index') }}" method="GET" class="flex gap-4">
                    <select name="status" class="rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validados</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazados</option>
                    </select>
                </form>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Padre/Tutor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($receipts as $receipt)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->payment_date->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->student->user->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->parent->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($receipt->amount_paid, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($receipt->status->value === 'pending')
                                            <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Pendiente</span>
                                        @elseif($receipt->status->value === 'validated')
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Validado</span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Rechazado</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('finance.payment-receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900">Ver Detalle</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay comprobantes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $receipts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
