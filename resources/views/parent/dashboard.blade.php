<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Panel de Padre de Familia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            <!-- Mensajes no leídos -->
            @if($unreadMessageCount > 0)
                <x-unread-messages-card :unreadMessageCount="$unreadMessageCount" />
            @endif

            <!-- Mis Hijos Section -->
            @if($students->count() > 0)
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mis Hijos en la Escuela</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($students as $student)
                                <div class="rounded-lg border-2 border-gray-200 p-4 transition hover:border-blue-500 hover:shadow-lg">
                                    <div class="flex items-center">
                                        <div class="shrink-0">
                                            <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $student->user->full_name }}</h4>
                                            <p class="text-sm text-gray-600">Matrícula: {{ $student->enrollment_number }}</p>
                                            <p class="text-sm text-gray-600">{{ $student->grade_level }} - Grupo {{ $student->group }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <a href="{{ route('parent.pickup-people.index', $student) }}" class="flex-1 rounded-md bg-purple-600 px-3 py-2 text-center text-xs font-medium text-white hover:bg-purple-700">
                                            Personas Autorizadas
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Estado de Pagos -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Resumen de Pagos -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Estado de Pagos</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Al corriente / Pendiente -->
                            @if($overdueTuitions->count() === 0 && $totalDueTuitions > 0)
                                <div class="flex items-center rounded-lg bg-green-50 p-4">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-bold text-green-900">¡Al Corriente!</h4>
                                        <p class="text-sm text-green-700">No hay pagos vencidos</p>
                                    </div>
                                </div>
                            @elseif($overdueTuitions->count() > 0)
                                <div class="flex items-center rounded-lg bg-red-50 p-4">
                                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-lg font-bold text-red-900">Pagos Vencidos</h4>
                                        <p class="text-sm text-red-700">{{ $overdueTuitions->count() }} colegiatura(s) vencida(s)</p>
                                        <p class="mt-1 text-xl font-bold text-red-900">${{ number_format($totalOverdue, 2) }}</p>
                                        @if($totalLateFees > 0)
                                            <p class="mt-1 text-xs text-red-600">Incluye ${{ number_format($totalLateFees, 2) }} en recargos</p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center rounded-lg bg-blue-50 p-4">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-bold text-blue-900">Sin colegiaturas asignadas</h4>
                                        <p class="text-sm text-blue-700">No hay colegiaturas registradas aún</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Estadísticas -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <p class="text-sm text-gray-500">Pagadas</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $paidTuitionsCount }}</p>
                                    <button onclick="togglePaidTuitions()" class="mt-2 text-xs text-blue-600 hover:text-blue-800">
                                        <span id="togglePaidText">Mostrar</span>
                                    </button>
                                </div>
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <p class="text-sm text-gray-500">Pendientes</p>
                                    <p class="text-2xl font-bold text-red-600">{{ $displayPendingTuitions->count() }}</p>
                                    <button onclick="togglePendingTuitions()" class="mt-2 text-xs text-blue-600 hover:text-blue-800">
                                        <span id="togglePendingText">Mostrar</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Detalle de pagos realizados (oculto por defecto) -->
                            <div id="paidTuitionsList" class="mt-4 hidden">
                                <h5 class="mb-2 text-sm font-semibold text-gray-700">Pagos realizados:</h5>
                                <div class="max-h-48 space-y-2 overflow-y-auto">
                                    @foreach($paidTuitions as $tuition)
                                        <div class="rounded border border-green-200 bg-green-50 p-2 text-xs">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ $tuition->student->user->full_name }}</span>
                                                    <span class="text-gray-600">- {{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMMM YYYY') }}</span>
                                                </div>
                                                <span class="ml-2 font-bold text-green-700">${{ number_format($tuition->total_amount, 2) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Detalle de pendientes (oculto por defecto) -->
                            @if($displayPendingTuitions->count() > 0)
                                <div id="pendingTuitionsList" class="mt-4 hidden">
                                    <h5 class="mb-2 text-sm font-semibold text-gray-700">Meses pendientes (vencidos + mes actual):</h5>
                                    <div class="max-h-96 space-y-2 overflow-y-auto">
                                        @foreach($displayPendingTuitions as $tuition)
                                            <div class="rounded border border-red-200 bg-red-50 p-3 text-xs">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div>
                                                            <span class="font-medium text-gray-900">{{ $tuition->student->user->full_name }}</span>
                                                            <span class="text-gray-600">- {{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMMM YYYY') }}</span>
                                                        </div>
                                                        @if($tuition->late_fee > 0)
                                                            <div class="mt-1 text-xs text-red-600">
                                                                Colegiatura: ${{ number_format($tuition->final_amount, 2) }} + Recargo: ${{ number_format($tuition->late_fee, 2) }}
                                                                ({{ $tuition->days_late }} día(s) de atraso)
                                                            </div>
                                                        @endif
                                                        <div class="mt-2">
                                                            <button
                                                                onclick="openPaymentModal({{ $tuition->id }}, '{{ $tuition->student->user->full_name }}', '{{ \Carbon\Carbon::create($tuition->year, $tuition->month)->locale('es')->isoFormat('MMMM YYYY') }}', {{ $tuition->total_amount }}, {{ $tuition->student_id }}, {{ $tuition->year }}, {{ $tuition->month }})"
                                                                class="rounded bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-700">
                                                                Adjuntar Comprobante
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <span class="ml-2 font-bold text-red-700">${{ number_format($tuition->total_amount, 2) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Historial de Comprobantes Recientes -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Historial de Comprobantes</h3>
                            <a href="{{ route('parent.payment-receipts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentReceipts->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentReceipts as $receipt)
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $receipt->student->user->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $receipt->payment_date->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900">${{ number_format($receipt->amount_paid, 2) }}</p>
                                            @if($receipt->status->value === 'pending')
                                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">Pendiente</span>
                                            @elseif($receipt->status->value === 'approved')
                                                <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Aprobado</span>
                                            @elseif($receipt->status->value === 'rejected')
                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Rechazado</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-lg bg-gray-50 p-4 text-center">
                                <p class="text-sm text-gray-500">No hay comprobantes registrados</p>
                                <a href="{{ route('parent.payment-receipts.create') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">Subir primer comprobante</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Comprobantes de Pago -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Comprobantes de Pago</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('parent.payment-receipts.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Subir Comprobante
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.payment-receipts.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Ver Mis Comprobantes
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.payment-receipts.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Historial de Pagos
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Justificantes Médicos -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Justificantes Médicos</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('parent.medical-justifications.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Subir Justificante
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.medical-justifications.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Ver Mis Justificantes
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('parent.medical-justifications.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Historial de Ausencias
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Calificaciones -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Calificaciones</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            @forelse($students as $student)
                                <li>
                                    <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Calificaciones de {{ $student->user->full_name }}
                                    </a>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500">No hay hijos registrados</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Tareas -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Tareas</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            @forelse($students as $student)
                                <li>
                                    <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Tareas de {{ $student->user->full_name }}
                                    </a>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500">No hay hijos registrados</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Anuncios -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Anuncios</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                    Ver Anuncios Recientes
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    Notificaciones
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Mi Perfil -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Perfil</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Editar Perfil
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800" onclick="alert('Próximamente: Vista con toda la información de tus hijos'); return false;">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Información de Hijos
                                </a>
                            </li>
                            <li>
                                <a href="mailto:contacto@escuela.edu.mx" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Contactar a la Escuela
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if($students->isEmpty())
                <div class="rounded-lg bg-yellow-50 p-4 text-center">
                    <p class="text-gray-700">No se encontraron estudiantes asociados a esta cuenta.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Adjuntar Comprobante -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Adjuntar Comprobante de Pago</h3>
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
                        <label class="block text-sm font-medium text-gray-700">Estudiante</label>
                        <p id="modal_student_name" class="mt-1 text-sm text-gray-900"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Período</label>
                        <p id="modal_period" class="mt-1 text-sm text-gray-900"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Monto a Pagar</label>
                        <p id="modal_amount" class="mt-1 text-lg font-bold text-gray-900"></p>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                        <input type="date" name="payment_date" id="payment_date" required value="{{ now()->format('Y-m-d') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700">Monto Pagado</label>
                        <input type="number" name="amount_paid" id="amount_paid" step="0.01" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="receipt_file" class="block text-sm font-medium text-gray-700">Comprobante (Imagen/PDF)</label>
                        <input type="file" name="receipt_file" id="receipt_file" required accept="image/*,.pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas (Opcional)</label>
                        <textarea name="notes" id="notes" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <button type="button" onclick="closePaymentModal()" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Subir Comprobante
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
                text.textContent = 'Ocultar';
            } else {
                list.classList.add('hidden');
                text.textContent = 'Mostrar';
            }
        }

        function togglePendingTuitions() {
            const list = document.getElementById('pendingTuitionsList');
            const text = document.getElementById('togglePendingText');
            if (list.classList.contains('hidden')) {
                list.classList.remove('hidden');
                text.textContent = 'Ocultar';
            } else {
                list.classList.add('hidden');
                text.textContent = 'Mostrar';
            }
        }

        function openPaymentModal(tuitionId, studentName, period, amount, studentId, year, month) {
            document.getElementById('modal_student_id').value = studentId;
            document.getElementById('modal_payment_year').value = year;
            document.getElementById('modal_payment_month').value = month;
            document.getElementById('modal_student_name').textContent = studentName;
            document.getElementById('modal_period').textContent = period;
            document.getElementById('modal_amount').textContent = '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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
