<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">
                    Bienvenido, {{ auth()->user()->first_name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Panel de Padre de Familia</p>
            </div>
            @if($unreadMessageCount > 0)
                <div class="rounded-lg bg-blue-50 px-4 py-2 border border-blue-200">
                    <p class="text-sm font-semibold text-blue-900">📬 {{ $unreadMessageCount }} mensaje(s)</p>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            <!-- Hero Banner con Métricas Clave - Similar al Admin Dashboard -->
            @if($students->count() > 0)
                <div class="mb-8">
                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- Estudiantes -->
                        <div class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500">Estudiantes</dt>
                                            <dd class="text-2xl font-semibold text-gray-900">{{ $students->count() }}</dd>
                                            <p class="text-xs text-gray-500 mt-1">En el sistema</p>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagos Realizados -->
                        <div class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500">Pagos Realizados</dt>
                                            <dd class="text-2xl font-semibold text-gray-900">{{ $paidTuitionsCount }}</dd>
                                            <p class="text-xs text-gray-500 mt-1">Aprobados</p>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pendientes -->
                        <div class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500">Pendientes</dt>
                                            <dd class="text-2xl font-semibold text-gray-900">{{ $displayPendingTuitions->count() }}</dd>
                                            <p class="text-xs text-gray-500 mt-1">Por pagar</p>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comprobantes -->
                        <div class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500">Comprobantes</dt>
                                            <dd class="text-2xl font-semibold text-gray-900">{{ $recentReceipts->count() }}</dd>
                                            <p class="text-xs text-gray-500 mt-1">Subidos</p>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Alerta de Mensajes no leídos -->
            @if($unreadMessageCount > 0)
                <x-unread-messages-card :unreadMessageCount="$unreadMessageCount" />
            @endif

            <!-- Estado de Pagos - Prominente -->
            <div class="overflow-hidden rounded-lg bg-white shadow-lg border-t-4 border-blue-500">
                <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">💳 Estado de Pagos</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Estado General -->
                        @if($overdueTuitions->count() === 0 && $totalDueTuitions > 0)
                            <div class="flex items-center rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 p-4 border-l-4 border-green-500">
                                <svg class="h-8 w-8 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-bold text-green-900">¡Al Corriente!</h4>
                                    <p class="text-sm text-green-700">No hay pagos vencidos. Excelente gestión.</p>
                                </div>
                            </div>
                        @elseif($overdueTuitions->count() > 0)
                            <div class="flex items-center rounded-lg bg-gradient-to-r from-red-50 to-rose-50 p-4 border-l-4 border-red-500">
                                <svg class="h-8 w-8 shrink-0 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-bold text-red-900">⚠️ Pagos Vencidos</h4>
                                    <div class="mt-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <p class="text-sm text-red-700"><strong>{{ $overdueTuitions->count() }}</strong> colegiatura(s) vencida(s)</p>
                                        <p class="text-xl font-bold text-red-900">${{ number_format($totalOverdue, 2) }}</p>
                                    </div>
                                    @if($totalLateFees > 0)
                                        <p class="mt-2 text-xs text-red-600">+ <strong>${{ number_format($totalLateFees, 2) }}</strong> en recargos por mora</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center rounded-lg bg-gradient-to-r from-blue-50 to-cyan-50 p-4 border-l-4 border-blue-500">
                                <svg class="h-8 w-8 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-4">
                                    <h4 class="text-lg font-bold text-blue-900">ℹ️ Sin colegiaturas</h4>
                                    <p class="text-sm text-blue-700">No hay colegiaturas registradas aún</p>
                                </div>
                            </div>
                        @endif

                        <!-- Estadísticas de Resumen -->
                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center p-3 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg">
                                <p class="text-3xl font-bold text-green-600">{{ $paidTuitionsCount }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-600">Pagadas</p>
                                <button onclick="togglePaidTuitions()" class="mt-2 text-xs font-semibold text-green-600 hover:text-green-700 transition">
                                    <span id="togglePaidText">▼ Ver</span>
                                </button>
                            </div>
                            <div class="text-center p-3 bg-gradient-to-br from-orange-50 to-red-50 rounded-lg">
                                <p class="text-3xl font-bold text-orange-600">{{ $displayPendingTuitions->count() }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-600">Pendientes</p>
                                <button onclick="togglePendingTuitions()" class="mt-2 text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                                    <span id="togglePendingText">▼ Ver</span>
                                </button>
                            </div>
                            <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg">
                                <p class="text-3xl font-bold text-blue-600">{{ $recentReceipts->count() }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-600">Comprobantes</p>
                            </div>
                        </div>

                        <!-- Detalle de pagos realizados -->
                        <div id="paidTuitionsList" class="mt-4 hidden">
                            <h5 class="mb-3 text-sm font-bold text-gray-900 text-center">Pagos Realizados</h5>
                            <div class="max-h-48 space-y-2 overflow-y-auto">
                                @foreach($paidTuitions as $tuition)
                                    <div class="rounded-lg border border-green-200 bg-green-50/50 p-3 text-xs transition hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $tuition->student->user->full_name }}</span>
                                                <span class="ml-2 text-gray-600">{{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMM YYYY') }}</span>
                                            </div>
                                            <span class="ml-2 font-bold text-green-700 text-sm">${{ number_format($tuition->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detalle de pendientes -->
                        @if($displayPendingTuitions->count() > 0)
                            <div id="pendingTuitionsList" class="mt-4 hidden">
                                <h5 class="mb-3 text-sm font-bold text-gray-900 text-center">Pagos Pendientes</h5>
                                <div class="max-h-48 space-y-2 overflow-y-auto">
                                    @foreach($displayPendingTuitions as $tuition)
                                        <div class="rounded-lg border border-red-200 bg-red-50/50 p-3 text-xs transition hover:shadow-md">
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <span class="font-semibold text-gray-900">{{ $tuition->student->user->full_name }}</span>
                                                    <span class="ml-2 text-gray-600">{{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMM YYYY') }}</span>
                                                </div>
                                                <span class="ml-2 font-bold text-red-700 text-sm">${{ number_format($tuition->total_amount, 2) }}</span>
                                            </div>
                                            @if($tuition->late_fee > 0)
                                                <div class="text-xs text-red-600 mb-2">
                                                    🔔 <strong>{{ $tuition->days_late }} días</strong> de atraso (Recargo: ${{{ number_format($tuition->late_fee, 2) }}})
                                                </div>
                                            @endif
                                            <button
                                                onclick="openPaymentModal({{ $tuition->id }}, '{{ $tuition->student->user->full_name }}', '{{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMMM YYYY') }}', {{ $tuition->total_amount }}, {{ $tuition->student_id }}, {{ $tuition->year }}, {{ $tuition->month }})"
                                                class="inline-flex items-center gap-1 rounded bg-blue-600 px-2 py-1 text-xs text-white hover:bg-blue-700 transition">
                                                📤 Subir Comprobante
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mis Hijos Section -->
            @if($students->count() > 0)
                <div class="overflow-hidden rounded-lg bg-white shadow-lg border-t-4 border-purple-500">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">👨‍👩‍👧‍👦 Mis Hijos en la Escuela</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($students as $student)
                                <div class="rounded-lg border-2 border-gray-200 p-5 transition hover:border-purple-500 hover:shadow-lg hover:bg-purple-50/30">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="shrink-0">
                                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h4 class="text-lg font-bold text-gray-900">{{ $student->user->full_name }}</h4>
                                                <p class="text-xs text-gray-500 font-medium">{{ $student->grade_level }} • Grupo {{ $student->group }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 p-2 bg-gray-50 rounded border border-gray-200">
                                        <p class="text-xs text-gray-600"><span class="font-semibold">Matrícula:</span> {{ $student->enrollment_number }}</p>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('parent.pickup-people.index', $student) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-purple-600 px-3 py-2 text-xs font-semibold text-white hover:bg-purple-700 active:scale-95 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            👥 Autorizados
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Historial de Comprobantes Recientes -->
            <div class="overflow-hidden rounded-lg bg-white shadow-lg border-t-4 border-green-500">
                <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">📋 Historial de Comprobantes</h3>
                    <a href="{{ route('parent.payment-receipts.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">Ver todos →</a>
                </div>
                <div class="p-6">
                    @if($recentReceipts->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentReceipts as $receipt)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:shadow-md transition">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $receipt->student->user->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $receipt->payment_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">${{ number_format($receipt->amount_paid, 2) }}</p>
                                        @if($receipt->status->value === 'pending')
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800">⏳ Pendiente</span>
                                        @elseif($receipt->status->value === 'approved')
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">✅ Aprobado</span>
                                        @elseif($receipt->status->value === 'rejected')
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">❌ Rechazado</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg bg-gray-50 p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">No hay comprobantes registrados aún</p>
                            <a href="{{ route('parent.payment-receipts.create') }}" class="mt-3 inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                                📤 Subir mi primer comprobante
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Comprobantes de Pago -->
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">📤 Comprobantes</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('parent.payment-receipts.create') }}" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Subir Comprobante
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.payment-receipts.index') }}" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Ver Mis Comprobantes
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Justificantes Médicos -->
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">🏥 Justificantes</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('parent.medical-justifications.create') }}" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Subir Justificante
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.medical-justifications.index') }}" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Mis Justificantes
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Mi Perfil -->
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">👤 Mi Perfil</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Editar Perfil
                                </a>
                            </li>
                            <li>
                                <a href="mailto:contacto@escuela.edu.mx" class="flex items-center gap-3 rounded-lg p-3 text-blue-600 hover:bg-blue-50 transition font-semibold">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Contactar Escuela
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if($students->isEmpty())
                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-gray-700 font-semibold">No se encontraron estudiantes asociados a esta cuenta.</p>
                    <p class="text-sm text-gray-600">Por favor, contacta a la escuela para registrar a tus hijos.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Adjuntar Comprobante -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">📤 Adjuntar Comprobante de Pago</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="paymentForm" action="{{ route('parent.payment-receipts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="student_id" id="modal_student_id">
                <input type="hidden" name="payment_year" id="modal_payment_year">
                <input type="hidden" name="payment_month" id="modal_payment_month">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Estudiante</label>
                        <p id="modal_student_name" class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Período</label>
                        <p id="modal_period" class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Monto a Pagar</label>
                        <p id="modal_amount" class="mt-1 text-2xl font-bold text-blue-600 bg-blue-50 p-2 rounded"></p>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-semibold text-gray-700">Fecha de Pago</label>
                        <input type="date" name="payment_date" id="payment_date" required value="{{ now()->format('Y-m-d') }}"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="amount_paid" class="block text-sm font-semibold text-gray-700">Monto Pagado</label>
                        <input type="number" name="amount_paid" id="amount_paid" step="0.01" required
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="receipt_file" class="block text-sm font-semibold text-gray-700">Comprobante (Imagen/PDF)</label>
                        <input type="file" name="receipt_file" id="receipt_file" required accept="image/*,.pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700">Notas (Opcional)</label>
                        <textarea name="notes" id="notes" rows="2"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <button type="button" onclick="closePaymentModal()" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 active:scale-95 transition">
                        📤 Subir Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePaidTuitions() {
            const list = document.getElementById('paidTuitionsList');
            const text = document.getElementById('togglePaidText');
            if (list.classList.contains('hidden')) {
                list.classList.remove('hidden');
                text.textContent = '▲ Ocultar';
            } else {
                list.classList.add('hidden');
                text.textContent = '▼ Ver';
            }
        }

        function togglePendingTuitions() {
            const list = document.getElementById('pendingTuitionsList');
            const text = document.getElementById('togglePendingText');
            if (list.classList.contains('hidden')) {
                list.classList.remove('hidden');
                text.textContent = '▲ Ocultar';
            } else {
                list.classList.add('hidden');
                text.textContent = '▼ Ver';
            }
        }

        function openPaymentModal(tuitionId, studentName, period, amount, studentId, year, month) {
            document.getElementById('modal_student_id').value = studentId;
            document.getElementById('modal_payment_year').value = year;
            document.getElementById('modal_payment_month').value = month;
            document.getElementById('modal_student_name').textContent = studentName;
            document.getElementById('modal_period').textContent = period;
            document.getElementById('modal_amount').textContent = '$' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('amount_paid').value = amount.toFixed(2);

            const modal = document.getElementById('paymentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('paymentForm').reset();
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePaymentModal();
            }
        });

        // Close modal on background click
        document.getElementById('paymentModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closePaymentModal();
            }
        });
    </script>
</x-app-layout>
