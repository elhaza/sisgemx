<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard de Estudiante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 grid gap-6 md:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <svg class="size-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Calificaciones Registradas</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalGrades }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <svg class="size-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Tareas Pendientes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $pendingAssignments }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <svg class="size-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Tareas Vencidas</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $overdueAssignments }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($student)
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Información</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre</p>
                                <p class="mt-1 text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Matrícula</p>
                                <p class="mt-1 text-gray-900">{{ $student->enrollment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Grado y Grupo</p>
                                <p class="mt-1 text-gray-900">{{ $student->grade_level }} - Grupo {{ $student->group }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Horario</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Ver Horario Completo
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Clases de Hoy
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Mis Materias
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Calificaciones</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Ver Todas Mis Calificaciones
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Calificaciones por Materia
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                    Mi Promedio General
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Tareas</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Ver Tareas Pendientes
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tareas Vencidas
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tareas Completadas
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Anuncios</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                    Ver Anuncios Recientes
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    Notificaciones
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Archivo de Anuncios
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Asistencia</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Ver Mi Asistencia
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Justificantes Médicos
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Estadísticas de Asistencia
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Perfil</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Editar Perfil
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Mis Documentos
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Contactar a mis Maestros
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if($recentAnnouncements->count() > 0)
                <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Anuncios Recientes</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($recentAnnouncements as $announcement)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <h4 class="font-semibold text-gray-900">{{ $announcement->title }}</h4>
                                    <p class="mt-2 text-gray-600">{{ $announcement->content }}</p>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Por: {{ $announcement->teacher->name }} - {{ $announcement->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($schedules->count() > 0)
                <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Horario de Clases</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-left text-gray-500">Día</th>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-left text-gray-500">Materia</th>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-left text-gray-500">Hora Inicio</th>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-left text-gray-500">Hora Fin</th>
                                        <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-left text-gray-500">Salón</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4">{{ ucfirst($schedule->day_of_week->value) }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $schedule->subject->name }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $schedule->start_time }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $schedule->end_time }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $schedule->classroom }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
