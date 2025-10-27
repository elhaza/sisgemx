<script src="https://cdn.tailwindcss.com"></script> <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'], }, } } } </script> <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"> <style> /* Estilos globales para la previsualización */ body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; } /* Simulación de x-app-layout: Contenedor principal */ .app-layout-wrapper { min-height: 100vh; } </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Panel de Administración
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Alerta de Ciclo Escolar Fuera de Rango -->
            @if(!isset($activeSchoolYear))
                <div class="mb-6 overflow-hidden rounded-lg border-l-4 border-red-500 bg-red-50 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-red-800">No hay ciclo escolar activo</h3>
                                <p class="mt-2 text-sm text-red-700">
                                    Actualmente no existe un ciclo escolar marcado como activo. Es necesario crear un nuevo ciclo escolar o activar uno existente para que el sistema funcione correctamente.
                                </p>
                                <div class="mt-4 flex gap-3">
                                    <a href="{{ route('admin.school-years.create') }}" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Crear Nuevo Ciclo Escolar
                                    </a>
                                    <a href="{{ route('admin.school-years.index') }}" class="inline-flex items-center rounded-md border border-red-600 bg-white px-4 py-2 text-sm font-semibold text-red-600 shadow-sm transition hover:bg-red-50">
                                        Gestionar Ciclos Existentes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($isOutOfRange)
                <div class="mb-6 overflow-hidden rounded-lg border-l-4 border-yellow-500 bg-yellow-50 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-yellow-800">Ciclo Escolar Fuera del Rango Vigente</h3>
                                <p class="mt-2 text-sm text-yellow-700">
                                    El ciclo escolar actual <strong>{{ $activeSchoolYear->name }}</strong> está fuera de su rango de fechas vigente
                                    ({{ \Carbon\Carbon::parse($activeSchoolYear->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($activeSchoolYear->end_date)->format('d/m/Y') }}).
                                </p>
                                <p class="mt-1 text-sm text-yellow-700">
                                    @if(now()->lt($activeSchoolYear->start_date))
                                        El ciclo escolar aún no ha comenzado. Comienza el {{ \Carbon\Carbon::parse($activeSchoolYear->start_date)->format('d/m/Y') }}.
                                    @else
                                        El ciclo escolar finalizó el {{ \Carbon\Carbon::parse($activeSchoolYear->end_date)->format('d/m/Y') }}.
                                    @endif
                                </p>
                                <div class="mt-4 flex gap-3">
                                    <a href="{{ route('admin.school-years.create') }}" class="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-700">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Crear Nuevo Ciclo Escolar
                                    </a>
                                    <a href="{{ route('admin.school-years.edit', $activeSchoolYear) }}" class="inline-flex items-center rounded-md border border-yellow-600 bg-white px-4 py-2 text-sm font-semibold text-yellow-600 shadow-sm transition hover:bg-yellow-50">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Ampliar Ciclo Actual
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mensajes no leídos -->
            @if($unreadMessageCount > 0)
                <div class="mb-6">
                    <x-unread-messages-card :unreadMessageCount="$unreadMessageCount" />
                </div>
            @endif

            <!-- Finanzas - Información Importante -->
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">💰 Finanzas del Mes</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <!-- Pagos del Mes -->
                    <a href="{{ route('finance.payment-receipts.index', ['month' => now()->month, 'year' => now()->year, 'status' => 'validated']) }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Pagos del Mes</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">${{ number_format($financialStats['monthly_payments'], 2) }}</dd>
                                        <p class="text-xs text-gray-500 mt-1">Recaudado</p>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Pendientes de Pago del Mes -->
                    <a href="{{ route('finance.payment-receipts.index', ['month' => now()->month, 'year' => now()->year, 'status' => 'pending']) }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Recibos del Mes</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">${{ number_format($financialStats['pending_monthly_payments'], 2) }}</dd>
                                        <p class="text-xs text-gray-500 mt-1">Por validar</p>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Colegiaturas Pendientes del Mes -->
                    <a href="{{ route('finance.student-tuitions.index') }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Colegiaturas</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">${{ number_format($financialStats['unpaid_tuitions_month'], 2) }}</dd>
                                        <p class="text-xs text-gray-500 mt-1">Pendientes este mes</p>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Padres Retrazados en Pago -->
                    <a href="{{ route('admin.overdue-parents-report') }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Padres</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ $financialStats['parents_overdue'] }}</dd>
                                        <p class="text-xs text-gray-500 mt-1">con mora</p>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Recargos por Mora -->
                    <div class="block overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Recargos por Mora</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">${{ number_format($financialStats['late_fees'], 2) }}</dd>
                                        <p class="text-xs text-gray-500 mt-1">Por cobrar</p>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="mb-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.students.create') }}" class="block overflow-hidden rounded-lg bg-blue-600 px-6 py-8 text-white shadow-md transition hover:shadow-lg hover:bg-blue-700">
                    <div class="flex flex-col items-center text-center">
                        <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold">Inscribir Estudiante</h4>
                    </div>
                </a>

                <a href="{{ route('admin.subjects.index') }}" class="block overflow-hidden rounded-lg bg-green-600 px-6 py-8 text-white shadow-md transition hover:shadow-lg hover:bg-green-700">
                    <div class="flex flex-col items-center text-center">
                        <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h4 class="text-lg font-semibold">Materias</h4>
                    </div>
                </a>

                <a href="{{ route('admin.schedules.visual') }}" class="block overflow-hidden rounded-lg bg-purple-600 px-6 py-8 text-white shadow-md transition hover:shadow-lg hover:bg-purple-700">
                    <div class="flex flex-col items-center text-center">
                        <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold">Horarios</h4>
                    </div>
                </a>

                <a href="{{ route('finance.payment-receipts.index', ['status' => 'pending']) }}" class="block overflow-hidden rounded-lg bg-orange-600 px-6 py-8 text-white shadow-md transition hover:shadow-lg hover:bg-orange-700">
                    <div class="flex flex-col items-center text-center">
                        <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold">Comprobantes</h4>
                        <p class="mt-1 text-sm text-orange-100">{{ \App\Models\PaymentReceipt::where('status', 'pending')->count() }} pendientes</p>
                    </div>
                </a>
            </div>

            <!-- Estadísticas Generales -->
            <div class="mb-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.students.index') }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Estudiantes Activos</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">
                                        @php
                                            $activeYear = \App\Models\SchoolYear::where('is_active', true)->first();
                                            $count = $activeYear
                                                ? \App\Models\Student::where('school_year_id', $activeYear->id)->where('status', 'active')->count()
                                                : 0;
                                        @endphp
                                        {{ $count }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.subjects.index') }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Total Maestros</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ \App\Models\User::where('role', 'teacher')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('finance.payment-receipts.index', ['status' => 'pending']) }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md hover:border-l-4 hover:border-orange-600">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Validar Pagos Pendientes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">
                                        @php
                                            $pendingPayments = \App\Models\PaymentReceipt::where('status', 'pending')->count();
                                        @endphp
                                        {{ $pendingPayments }}
                                    </dd>
                                    <p class="text-xs text-gray-500 mt-1">comprobantes por validar</p>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.medical-justifications.index') }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md hover:border-l-4 hover:border-red-600">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Validar Justificantes Pendientes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">
                                        @php
                                            $pendingJustifications = \App\Models\MedicalJustification::where('status', 'pending')->count();
                                        @endphp
                                        {{ $pendingJustifications }}
                                    </dd>
                                    <p class="text-xs text-gray-500 mt-1">justificantes por validar</p>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Secciones Principales - Académico y Anuncios -->
            <div class="mb-8 grid gap-6 md:grid-cols-3">
                <!-- 📚 Académico -->
                <div class="md:col-span-2 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <span class="mr-2 text-xl">📚</span>
                            Académico
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('admin.grade-sections.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Secciones de Grado
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subjects.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Gestión de Materias
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schedules.visual') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Horarios
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schedules.copy-form') }}" class="flex items-center text-green-600 hover:text-green-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Copiar Horarios de Ciclo
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schedules.generate-form') }}" class="flex items-center text-purple-600 hover:text-purple-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Generar Horario Automático
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.grades.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Calificaciones Generales
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 📢 Anuncios Recientes (Derecha) -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-indigo-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="flex items-center text-lg font-semibold text-gray-900">
                                <span class="mr-2 text-xl">📢</span>
                                Anuncios Recientes
                            </h3>
                            @if($totalValidAnnouncements > 0)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-800">
                                    {{ $totalValidAnnouncements }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentAnnouncements->count() > 0)
                            <div id="announcement-container" class="space-y-3" style="min-height: 300px;">
                                <!-- Anuncios se cargarán aquí con JavaScript -->
                            </div>

                            <!-- Navegación de Anuncios -->
                            <div class="mt-4 flex items-center justify-between border-t pt-3">
                                <button
                                    id="prev-btn"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Anterior
                                </button>

                                <span id="announcement-counter" class="text-sm font-medium text-gray-700">
                                    <!-- Contador se actualiza con JavaScript -->
                                </span>

                                <button
                                    id="next-btn"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Siguiente
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Botón Ver todos -->
                            @if($totalValidAnnouncements > 5)
                                <div class="mt-3 border-t pt-3">
                                    <a href="{{ route('announcements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                        Ver todos los {{ $totalValidAnnouncements }} anuncios
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif

                            <script>
                                const announcements = @json($recentAnnouncements->values());
                                let currentIndex = 0;

                                function renderAnnouncement(index) {
                                    if (announcements.length === 0) return;

                                    const announcement = announcements[index];
                                    const container = document.getElementById('announcement-container');
                                    const counter = document.getElementById('announcement-counter');
                                    const prevBtn = document.getElementById('prev-btn');
                                    const nextBtn = document.getElementById('next-btn');

                                    // Renderizar anuncio actual
                                    const validityText = announcement.valid_from && announcement.valid_until
                                        ? `${announcement.valid_from.split('-').reverse().join('/')} - ${announcement.valid_until.split('-').reverse().join('/')}`
                                        : announcement.valid_from
                                            ? `Desde ${announcement.valid_from.split('-').reverse().join('/')}`
                                            : announcement.valid_until
                                                ? `Hasta ${announcement.valid_until.split('-').reverse().join('/')}`
                                                : '';

                                    const createdDate = new Date(announcement.created_at);
                                    const now = new Date();
                                    const diffTime = Math.abs(now - createdDate);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                    let timeAgo = 'hace ' + diffDays + ' días';
                                    if (diffDays === 0) timeAgo = 'hoy';
                                    if (diffDays === 1) timeAgo = 'hace 1 día';

                                    let html = `
                                        <a href="/teacher/announcements/${announcement.id}" class="block group p-3 rounded-lg hover:bg-indigo-50 transition border border-gray-100">
                                    `;

                                    if (announcement.image_path) {
                                        html += `
                                            <div class="h-20 w-full mb-2 rounded overflow-hidden bg-gray-100">
                                                <img
                                                    src="/storage/${announcement.image_path}"
                                                    alt="${announcement.title}"
                                                    class="h-full w-full object-cover group-hover:scale-105 transition"
                                                />
                                            </div>
                                        `;
                                    }

                                    html += `
                                            <h4 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-indigo-600">
                                                ${announcement.title}
                                            </h4>
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-1">
                                                ${announcement.content}
                                            </p>
                                            <div class="flex items-center justify-between mt-2 text-xs">
                                                <span class="text-gray-500">${announcement.teacher.name}</span>
                                                <span class="text-gray-400">${timeAgo}</span>
                                            </div>
                                    `;

                                    if (validityText) {
                                        html += `
                                            <div class="mt-1 text-xs text-amber-600 flex items-center">
                                                <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 8h16v9a2 2 0 01-2 2H6a2 2 0 01-2-2V8z" clip-rule="evenodd"></path>
                                                </svg>
                                                ${validityText}
                                            </div>
                                        `;
                                    }

                                    html += `</a>`;

                                    container.innerHTML = html;
                                    counter.textContent = `${index + 1} de ${announcements.length}`;

                                    // Actualizar estado de botones
                                    prevBtn.disabled = index === 0;
                                    nextBtn.disabled = index === announcements.length - 1;

                                    currentIndex = index;
                                }

                                // Event listeners
                                document.getElementById('prev-btn').addEventListener('click', () => {
                                    if (currentIndex > 0) renderAnnouncement(currentIndex - 1);
                                });

                                document.getElementById('next-btn').addEventListener('click', () => {
                                    if (currentIndex < announcements.length - 1) renderAnnouncement(currentIndex + 1);
                                });

                                // Inicializar
                                renderAnnouncement(0);
                            </script>
                        @else
                            <div class="py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                                <p class="mt-4 text-gray-600">No hay anuncios vigentes en este momento</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Secciones Principales - Estudiantes, Finanzas, Comunicación -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- 👥 Estudiantes -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-green-50 px-6 py-4">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <span class="mr-2 text-xl">👥</span>
                            Estudiantes
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('admin.students.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Inscribir Estudiante
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.students.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Lista de Estudiantes
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.students.transfer') }}" class="flex items-center text-purple-600 hover:text-purple-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Transferir entre Grupos
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 💰 Finanzas -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-purple-50 px-6 py-4">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <span class="mr-2 text-xl">💰</span>
                            Finanzas
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('finance.payment-receipts.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Comprobantes de Pago
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.school-years.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Colegiaturas Mensuales
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('finance.student-tuitions.discount-report') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Reportes Financieros
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 📢 Anuncios -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-indigo-50 px-6 py-4">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <span class="mr-2 text-xl">📢</span>
                            Anuncios
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('teacher.announcements.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Nuevo Anuncio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('teacher.announcements.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Ver Todos los Anuncios
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ⚙️ Administración -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-red-50 px-6 py-4">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <span class="mr-2 text-xl">⚙️</span>
                            Administración
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('admin.users.create') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Usuario
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Lista de Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.school-years.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Ciclos Escolares / $ asignación de colegiaturas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medical-justifications.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Justificantes Médicos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.settings.edit') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Configuración General
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
