<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Panel de Administración
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Estadísticas Generales -->
            <div class="mb-6 grid gap-6 md:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Total Estudiantes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ \App\Models\Student::count() }}</dd>
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
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Comprobantes Pendientes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ \App\Models\PaymentReceipt::where('status', 'pending')->count() }}</dd>
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
                                    <dt class="truncate text-sm font-medium text-gray-500">Pagos del Mes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">${{ number_format(\App\Models\Payment::where('is_paid', true)->whereMonth('paid_at', now()->month)->sum('amount'), 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones Principales -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Gestión de Usuarios -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Gestión de Usuarios</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Usuario
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Lista de Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Gestión de Roles
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Ciclos Escolares -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ciclos Escolares</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nuevo Ciclo Escolar
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Gestionar Ciclos
                                </a>
                            </li>
                            <li class="text-sm text-gray-600">
                                Ciclo Activo: <span class="font-semibold text-gray-900">{{ \App\Models\SchoolYear::where('is_active', true)->first()?->name ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Estudiantes -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Estudiantes</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Inscribir Estudiante
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Lista de Estudiantes
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Reportes
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Finanzas</h3>
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
                                <a href="{{ route('finance.tuition-configs.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Config. Colegiaturas
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Reportes Financieros
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Académico -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Académico</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Gestión de Materias
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Horarios
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Calificaciones Generales
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Configuración</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Configuración General
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Notificaciones
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Logs del Sistema
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
