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

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
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
                                <tr class="{{ isset($receipt->type) && $receipt->type === 'admin_payment' ? 'bg-blue-50' : '' }}">
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
                                        @elseif($receipt->status->value === 'approved')
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Aprobado</span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Rechazado</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                                            <span class="text-gray-500 text-sm">-</span>
                                        @else
                                            <a href="{{ route('finance.payment-receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900">Ver Detalle</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay comprobantes registrados.</td>
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
                        <p class="text-center text-gray-500">Desglose de ingresos por ciclo escolar no disponible</p>
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
                            <tr class="{{ isset($receipt->type) && $receipt->type === 'admin_payment' ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                <td class="px-4 py-2">{{ $receipt->payment_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    @if(isset($receipt->student) && $receipt->student)
                                        {{ $receipt->student->user->full_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                            Aplicado por Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                            Validado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-semibold">${{ number_format($receipt->amount_paid, 2) }}</td>
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
    </script>
</x-app-layout>
