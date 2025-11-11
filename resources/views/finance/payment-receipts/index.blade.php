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
            <!-- View Selection Tabs -->
            <div class="mb-6 flex border-b border-gray-200">
                <a href="{{ route('finance.payment-receipts.index', array_merge(request()->query(), ['view' => 'month'])) }}" class="px-4 py-2 font-medium text-sm border-b-2 {{ request('view', 'month') === 'month' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                    Por Mes
                </a>
                <a href="{{ route('finance.payment-receipts.index', array_merge(request()->query(), ['view' => 'school_year'])) }}" class="px-4 py-2 font-medium text-sm border-b-2 {{ request('view', 'month') === 'school_year' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                    Ciclo Escolar Completo
                </a>
            </div>

            <!-- Month/Period Label -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $monthLabel }}
                </h3>
            </div>

            <!-- Estadísticas -->
            <div class="mb-6 grid gap-6 md:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm cursor-pointer hover:shadow-md transition-shadow" onclick="filterPending()">
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

                <div class="overflow-hidden rounded-lg bg-white shadow-sm cursor-pointer hover:shadow-md transition-shadow" onclick="openValidatedReceiptsModal()">
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
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1 space-y-3">
                                <div class="cursor-pointer hover:opacity-80" onclick="openIncomeDetailsModal()">
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ $incomeLabel }}</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">${{ number_format($incomeCurrentMonth, 2) }}</dd>
                                </div>
                                @if($view === 'month' && $incomeAccumulated > 0)
                                    <div class="pt-2 border-t border-gray-200 cursor-pointer hover:opacity-80" onclick="openIncomeDetailsModal()">
                                        <dt class="truncate text-sm font-medium text-gray-500">Ingresos Acumulados</dt>
                                        <dd class="text-xl font-semibold text-gray-700">${{ number_format($incomeAccumulated, 2) }}</dd>
                                    </div>
                                @endif
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
            <div class="mb-4 overflow-hidden rounded-lg bg-white shadow-sm" style="max-height: 200px;">
                <div class="p-3">
                    <form action="{{ route('finance.payment-receipts.index') }}" method="GET">
                        <input type="hidden" name="view" value="{{ request('view', 'month') }}">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="status" class="w-40 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Estado</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validados</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazados</option>
                            </select>

                            <select name="month" class="w-32 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500" {{ request('view', 'month') === 'school_year' ? 'disabled' : '' }}>
                                <option value="">Mes</option>
                                <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>Enero</option>
                                <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>Febrero</option>
                                <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>Abril</option>
                                <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>Mayo</option>
                                <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>Junio</option>
                                <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>Julio</option>
                                <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Diciembre</option>
                            </select>

                            <select name="year" class="w-24 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500" {{ request('view', 'month') === 'school_year' ? 'disabled' : '' }}>
                                <option value="">Año</option>
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>

                            <button type="submit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Filtrar</button>

                            <a href="{{ route('finance.payment-receipts.index') }}" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div id="bulkActionsBar" class="mb-4 hidden rounded-lg bg-blue-50 border border-blue-200 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span id="selectedCount" class="text-sm font-medium text-gray-700">0 seleccionados</span>
                        <div class="flex gap-2">
                            <button type="button" onclick="bulkChangeStatus('validated')" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                ✓ Validar
                            </button>
                            <button type="button" onclick="bulkChangeStatus('rejected')" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                ✕ Rechazar
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="clearSelection()" class="text-gray-600 hover:text-gray-900">
                        Limpiar selección
                    </button>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Padre/Tutor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Comprobante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($receipts as $receipt)
                                <tr class="{{ isset($receipt->type) && $receipt->type === 'admin_payment' ? 'bg-blue-50' : '' }}" data-receipt-id="{{ $receipt->id ?? '' }}">
                                    <td class="px-4 py-4">
                                        @if(!isset($receipt->type) || $receipt->type !== 'admin_payment')
                                            <input type="checkbox" class="receipt-checkbox rounded border-gray-300" onchange="updateBulkActionsBar()" data-receipt-id="{{ $receipt->id }}">
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->payment_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->student->user->full_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $receipt->parent?->name ?? 'N/A' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($receipt->amount_paid, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Admin</span>
                                        @else
                                            <span class="rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800">Padre</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($receipt->receipt_image && $receipt->receipt_image_url)
                                            <button type="button" onclick="openImageModal('{{ $receipt->receipt_image_url }}')" class="inline-block">
                                                <img src="{{ $receipt->receipt_image_url }}" alt="Comprobante" class="h-8 w-8 cursor-pointer rounded object-cover hover:opacity-80">
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Validado</span>
                                        @elseif($receipt->status->value === 'pending')
                                            <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Pendiente</span>
                                        @elseif($receipt->status->value === 'validated')
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Validado</span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Rechazado</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                                            <span class="text-gray-500 text-sm">-</span>
                                        @else
                                            <div class="flex gap-2">
                                                <button type="button" onclick="openStatusModal({{ $receipt->id }}, '{{ $receipt->student->user->full_name }}')" class="rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700">
                                                    Cambiar
                                                </button>
                                                <a href="{{ route('finance.payment-receipts.show', $receipt) }}" class="rounded-md bg-gray-600 px-3 py-1 text-xs font-medium text-white hover:bg-gray-700">
                                                    Ver
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No hay comprobantes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{-- Simple pagination info since we're mixing collections --}}
                        <div class="text-sm text-gray-600">
                            Mostrando {{ count($receipts) }} registros
                        </div>
                    </div>
                </div>
            </div>

    <!-- Modal para ver imagen en grande -->
    <div id="imageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
        <div class="relative max-h-screen max-w-2xl">
            <button type="button" onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl">
                ✕
            </button>
            <img id="modalImage" src="" alt="Comprobante" class="max-h-screen max-w-2xl object-contain">
        </div>
    </div>

    <!-- Modal para detalles de ingresos -->
    <div id="incomeDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detalles de Ingresos</h3>
                <button type="button" onclick="closeIncomeDetailsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div class="space-y-3">
                    @if($view === 'month')
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Ingresos por Mes de Referencia</h4>
                            @forelse($incomeMonthlyDetails as $detail)
                                @php
                                    $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    $monthName = $monthNames[$detail->month] ?? 'Mes desconocido';
                                @endphp
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-700">{{ $monthName }}</span>
                                    <span class="font-semibold text-gray-900">${{ number_format($detail->total, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 text-sm">No hay ingresos registrados</p>
                            @endforelse
                        </div>

                        @if($advancePaymentsDetails->count() > 0)
                            <div class="pt-3 border-t border-gray-300">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Pagos Adelantados Realizados en Este Mes</h4>
                                @foreach($advancePaymentsDetails as $advance)
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <span class="text-gray-700">{{ $advance->label }}</span>
                                        <span class="font-semibold text-blue-900">${{ number_format($advance->total, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- School Year View -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Ingresos por Mes del Ciclo Escolar</h4>
                            @forelse($incomeMonthlyDetails as $detail)
                                @php
                                    $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                    $monthName = $monthNames[$detail->month] ?? 'Mes desconocido';
                                @endphp
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-700">{{ $monthName }} {{ $detail->year }}</span>
                                    <span class="font-semibold text-gray-900">${{ number_format($detail->total, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 text-sm">No hay ingresos registrados en este ciclo escolar</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver comprobantes validados -->
    <div id="validatedReceiptsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Comprobantes Validados</h3>
                <button type="button" onclick="closeValidatedReceiptsModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Fecha</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Estudiante</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Tipo</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-700">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($allValidatedReceipts as $receipt)
                            @php
                                // Handle both array and object access
                                $paymentDate = is_array($receipt) ? ($receipt['payment_date'] ?? null) : ($receipt->payment_date ?? null);
                                if (is_string($paymentDate)) {
                                    $paymentDate = \Carbon\Carbon::parse($paymentDate);
                                }

                                $type = is_array($receipt) ? ($receipt['type'] ?? 'validated_receipt') : ($receipt->type ?? 'validated_receipt');
                                $student = is_array($receipt) ? ($receipt['student'] ?? null) : ($receipt->student ?? null);
                                $amountPaid = is_array($receipt) ? ($receipt['amount_paid'] ?? 0) : ($receipt->amount_paid ?? 0);
                            @endphp
                            <tr class="{{ $type === 'admin_payment' ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                <td class="px-4 py-2">{{ $paymentDate?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    @if($student)
                                        @if(is_array($student))
                                            {{ $student['user']['full_name'] ?? $student['user']['name'] ?? 'N/A' }}
                                        @elseif(is_object($student))
                                            {{ $student->user?->full_name ?? $student->user?->name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($type === 'admin_payment')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                            Aplicado por Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                            Validado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-semibold">${{ number_format($amountPaid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No hay comprobantes validados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        function openIncomeDetailsModal() {
            document.getElementById('incomeDetailsModal').classList.remove('hidden');
        }

        function closeIncomeDetailsModal() {
            document.getElementById('incomeDetailsModal').classList.add('hidden');
        }

        // Close income modal when clicking outside
        document.getElementById('incomeDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeIncomeDetailsModal();
            }
        });

        function openValidatedReceiptsModal() {
            document.getElementById('validatedReceiptsModal').classList.remove('hidden');
        }

        function closeValidatedReceiptsModal() {
            document.getElementById('validatedReceiptsModal').classList.add('hidden');
        }

        // Close validated receipts modal when clicking outside
        document.getElementById('validatedReceiptsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeValidatedReceiptsModal();
            }
        });

        function filterPending() {
            // Filter by pending status without month restriction to show all 38 pending receipts
            const url = new URL('{{ route('finance.payment-receipts.index') }}');
            url.searchParams.set('view', '{{ request('view', 'month') }}');
            url.searchParams.set('status', 'pending');
            url.searchParams.set('year', '{{ request('year', now()->year) }}');
            // Don't set month parameter - this shows all pending
            window.location.href = url.toString();
        }

        // Individual Status Change Modal
        function openStatusModal(receiptId, studentName) {
            document.getElementById('statusModalReceiptId').value = receiptId;
            document.getElementById('statusModalStudentName').textContent = studentName;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        document.getElementById('statusModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        function submitStatusChange() {
            const receiptId = document.getElementById('statusModalReceiptId').value;
            const status = document.getElementById('statusModalStatus').value;
            const rejectionReason = document.getElementById('statusModalRejectionReason').value;
            const notes = document.getElementById('statusModalNotes').value;

            if (!status) {
                alert('Selecciona un estado');
                return;
            }

            if (status === 'rejected' && !rejectionReason) {
                alert('Debes proporcionar un motivo para rechazar');
                return;
            }

            // Submit via form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/finance/payment-receipts/${receiptId}/update-status`;

            form.innerHTML = `
                @csrf
                <input type="hidden" name="status" value="${status}">
                <input type="hidden" name="rejection_reason" value="${rejectionReason}">
                <input type="hidden" name="notes" value="${notes}">
            `;

            document.body.appendChild(form);
            form.submit();
        }

        // Bulk Selection Functions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.receipt-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkActionsBar();
        }

        function updateBulkActionsBar() {
            const checkboxes = document.querySelectorAll('.receipt-checkbox:checked');
            const count = checkboxes.length;
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');

            if (count > 0) {
                bulkActionsBar.classList.remove('hidden');
                selectedCount.textContent = `${count} seleccionado${count !== 1 ? 's' : ''}`;
            } else {
                bulkActionsBar.classList.add('hidden');
                document.getElementById('selectAll').checked = false;
            }
        }

        function clearSelection() {
            document.querySelectorAll('.receipt-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAll').checked = false;
            updateBulkActionsBar();
        }

        function bulkChangeStatus(status) {
            const checkboxes = document.querySelectorAll('.receipt-checkbox:checked');
            const receiptIds = Array.from(checkboxes).map(cb => cb.dataset.receiptId);

            if (receiptIds.length === 0) {
                alert('Selecciona al menos un comprobante');
                return;
            }

            const message = status === 'validated'
                ? `¿Validar ${receiptIds.length} comprobante${receiptIds.length !== 1 ? 's' : ''}?`
                : `¿Rechazar ${receiptIds.length} comprobante${receiptIds.length !== 1 ? 's' : ''}?`;

            if (confirm(message)) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/finance/payment-receipts/bulk/update-status';

                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="status" value="${status}">
                    <input type="hidden" name="receipt_ids" value="${receiptIds.join(',')}">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Individual Status Change Modal -->
    <div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Cambiar Estado del Comprobante</h3>
                <button type="button" onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    <strong>Estudiante:</strong> <span id="statusModalStudentName"></span>
                </p>

                <div>
                    <label for="statusModalStatus" class="block text-sm font-medium text-gray-700 mb-2">
                        Nuevo Estado
                    </label>
                    <select id="statusModalStatus" onchange="updateRejectionField()" class="w-full rounded-md border-gray-300 px-3 py-2 border focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar estado...</option>
                        <option value="validated">✓ Validar</option>
                        <option value="rejected">✕ Rechazar</option>
                        <option value="pending">⏳ Pendiente</option>
                    </select>
                </div>

                <div id="rejectionReasonField" class="hidden">
                    <label for="statusModalRejectionReason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo de Rechazo *
                    </label>
                    <textarea id="statusModalRejectionReason" class="w-full rounded-md border-gray-300 px-3 py-2 border focus:border-blue-500 focus:ring-blue-500" rows="3" placeholder="Especifica el motivo del rechazo..."></textarea>
                </div>

                <div>
                    <label for="statusModalNotes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notas (opcional)
                    </label>
                    <textarea id="statusModalNotes" class="w-full rounded-md border-gray-300 px-3 py-2 border focus:border-blue-500 focus:ring-blue-500" rows="2" placeholder="Notas adicionales..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 p-6">
                <button type="button" onclick="closeStatusModal()" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" onclick="submitStatusChange()" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" id="statusModalReceiptId">

    <script>
        function updateRejectionField() {
            const status = document.getElementById('statusModalStatus').value;
            const field = document.getElementById('rejectionReasonField');
            if (status === 'rejected') {
                field.classList.remove('hidden');
                document.getElementById('statusModalRejectionReason').required = true;
            } else {
                field.classList.add('hidden');
                document.getElementById('statusModalRejectionReason').required = false;
                document.getElementById('statusModalRejectionReason').value = '';
            }
        }
    </script>
</x-app-layout>
