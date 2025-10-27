<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    ¡Hola, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ \Carbon\Carbon::now()->format('l, d \\d\\e F \\d\\e Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Unread Messages Alert -->
            @if($unreadMessageCount > 0)
                <div class="mb-6 rounded-lg bg-blue-50 p-4 border-l-4 border-blue-500">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-blue-800">
                                Tienes <span class="font-bold">{{ $unreadMessageCount }}</span> mensaje(s) sin leer
                            </p>
                            <a href="{{ route('messages.inbox') }}" class="mt-2 inline-block text-sm font-medium text-blue-600 hover:text-blue-500">
                                Ver mensajes →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Grid Layout -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column - Main Content (2 columns on desktop) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Summary Stats - Only show if there are meaningful data -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-white p-4 shadow-md border-l-4 border-yellow-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-600">Tareas Pendientes</p>
                                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $pendingAssignments }}</p>
                                </div>
                                <span class="text-3xl">📋</span>
                            </div>
                            @if($pendingAssignments > 0)
                                <a href="{{ route('student.assignments') }}" class="mt-3 inline-block text-xs font-medium text-yellow-600 hover:text-yellow-700">
                                    Ver tareas →
                                </a>
                            @endif
                        </div>

                        <div class="rounded-lg bg-white p-4 shadow-md border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-600">Tareas Vencidas</p>
                                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $overdueAssignments }}</p>
                                </div>
                                <span class="text-3xl">⚠️</span>
                            </div>
                            @if($overdueAssignments > 0)
                                <a href="{{ route('student.assignments') }}" class="mt-3 inline-block text-xs font-medium text-red-600 hover:text-red-700">
                                    Atender ya →
                                </a>
                            @endif
                        </div>

                        <div class="rounded-lg bg-white p-4 shadow-md border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-600">Calificaciones</p>
                                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalGrades }}</p>
                                </div>
                                <span class="text-3xl">📊</span>
                            </div>
                            @if($totalGrades > 0)
                                <a href="{{ route('student.grades') }}" class="mt-3 inline-block text-xs font-medium text-green-600 hover:text-green-700">
                                    Ver calificaciones →
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Today's Schedule (only if there are classes today) -->
                    @if($todaySchedules->count() > 0)
                        <div class="rounded-lg bg-white shadow-md overflow-hidden">
                            <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                                <h3 class="text-lg font-bold text-gray-900">📅 Clases de Hoy</h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                @foreach($todaySchedules as $schedule)
                                    @php
                                        $isCurrentClass = $currentClass && $currentClass->id === $schedule->id;
                                        $isNextClass = $nextClass && $nextClass->id === $schedule->id;
                                    @endphp
                                    <div class="p-4 {{ $isCurrentClass ? 'bg-blue-50 border-l-4 border-blue-500' : ($isNextClass ? 'bg-amber-50 border-l-4 border-amber-500' : '') }}">
                                        <div class="flex items-start gap-4">
                                            <div class="text-2xl flex-shrink-0">
                                                @if($isCurrentClass)
                                                    🔴
                                                @elseif($isNextClass)
                                                    🟡
                                                @else
                                                    ⚪
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $schedule->subject->name }}</h4>
                                                <p class="mt-1 text-xs text-gray-600">
                                                    🕐 {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                    @if($schedule->classroom)
                                                        • Salón {{ $schedule->classroom }}
                                                    @endif
                                                </p>
                                                <p class="mt-1 text-xs text-gray-600">
                                                    👨‍🏫 {{ $schedule->subject->teacher->user->name }}
                                                </p>
                                                @if($isCurrentClass)
                                                    <span class="mt-2 inline-block rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                                                        En curso
                                                    </span>
                                                @elseif($isNextClass)
                                                    <span class="mt-2 inline-block rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                                        Próxima
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg bg-gradient-to-br from-purple-50 to-indigo-50 p-8 text-center">
                            <p class="text-lg font-semibold text-gray-800">📭 No hay clases hoy</p>
                            <p class="mt-2 text-sm text-gray-600">
                                <a href="{{ route('student.schedule') }}" class="font-medium text-indigo-600 hover:text-indigo-700">
                                    Ver tu horario completo →
                                </a>
                            </p>
                        </div>
                    @endif

                    <!-- Upcoming Assignments -->
                    @if($upcomingAssignments->count() > 0 || $pendingAssignments > 0)
                        <div class="rounded-lg bg-white shadow-md overflow-hidden">
                            <div class="border-b border-gray-200 bg-orange-50 px-6 py-4">
                                <h3 class="text-lg font-bold text-gray-900">📋 Próximas Tareas</h3>
                            </div>
                            @if($upcomingAssignments->count() > 0)
                                <div class="divide-y divide-gray-200">
                                    @foreach($upcomingAssignments as $assignment)
                                        @php
                                            $daysUntilDue = $assignment->due_date->diffInDays(now(), false);
                                            $isUrgent = $daysUntilDue <= 2;
                                            $isDueSoon = $daysUntilDue <= 7;
                                        @endphp
                                        <div class="p-4 hover:bg-gray-50 transition-colors {{ $isUrgent ? 'bg-red-50 border-l-4 border-red-500' : ($isDueSoon ? 'bg-amber-50 border-l-4 border-amber-500' : '') }}">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                                    <p class="mt-1 text-xs text-gray-600">
                                                        📚 {{ $assignment->subject->name }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-gray-500 line-clamp-2">
                                                        {{ Str::limit($assignment->description, 80) }}
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0 text-right">
                                                    @if($isUrgent)
                                                        <span class="inline-block rounded-full bg-red-100 px-2 py-1 text-xs font-bold text-red-700 mb-2">
                                                            🔴 Urgente
                                                        </span>
                                                    @elseif($isDueSoon)
                                                        <span class="inline-block rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-700 mb-2">
                                                            🟡 Pronto
                                                        </span>
                                                    @endif
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $assignment->due_date->format('d/m') }}
                                                    </div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ $assignment->due_date->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                                    <a href="{{ route('student.assignments') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                                        Ver todas las tareas →
                                    </a>
                                </div>
                            @else
                                <div class="p-8 text-center">
                                    <p class="text-gray-700 font-medium">¡Excelente! No tienes tareas pendientes 🎉</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Student Info Card -->
                    @if($student)
                        <div class="rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg text-white p-6">
                            <p class="text-sm font-medium opacity-80">Mi Información</p>
                            <h3 class="mt-2 text-lg font-bold">{{ auth()->user()->full_name }}</h3>
                            <p class="mt-1 text-sm opacity-80">
                                📋 {{ $student->enrollment_number }}
                            </p>
                        </div>
                    @endif

                    <!-- Recent Announcements -->
                    <div class="rounded-lg bg-white shadow-md overflow-hidden">
                        <div class="border-b border-gray-200 bg-purple-50 px-6 py-4">
                            <h3 class="text-lg font-bold text-gray-900">📢 Anuncios</h3>
                        </div>
                        @if($recentAnnouncements->count() > 0)
                            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                                @foreach($recentAnnouncements->take(5) as $announcement)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h4>
                                        <p class="mt-1 text-xs text-gray-600 line-clamp-2">{{ $announcement->content }}</p>
                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ $announcement->created_at->format('d/m H:i') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center text-gray-500">
                                <p class="text-sm">No hay anuncios recientes</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div class="rounded-lg bg-white shadow-md overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h3 class="font-bold text-gray-900">⚡ Accesos Rápidos</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <a href="{{ route('messages.inbox') }}" class="block p-4 hover:bg-blue-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">✉️</span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Mensajes</p>
                                            <p class="text-xs text-gray-500">
                                                @if($unreadMessageCount > 0)
                                                    {{ $unreadMessageCount }} sin leer
                                                @else
                                                    Actualizado
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($unreadMessageCount > 0)
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-500 text-xs font-bold text-white">
                                            {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ route('student.grades') }}" class="block p-4 hover:bg-green-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">📊</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Calificaciones</p>
                                        <p class="text-xs text-gray-500">{{ $totalGrades }} registradas</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('student.assignments') }}" class="block p-4 hover:bg-orange-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">📝</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Tareas</p>
                                        <p class="text-xs text-gray-500">
                                            @if($pendingAssignments > 0)
                                                {{ $pendingAssignments }} por hacer
                                            @else
                                                Completas
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('student.schedule') }}" class="block p-4 hover:bg-purple-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">📅</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Horario</p>
                                        <p class="text-xs text-gray-500">Ver semana</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block p-4 hover:bg-indigo-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">⚙️</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Perfil</p>
                                        <p class="text-xs text-gray-500">Editar datos</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
