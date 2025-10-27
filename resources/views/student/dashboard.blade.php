<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Bienvenido, {{ auth()->user()->name }}
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
                        <div class="ml-3">
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

            <!-- Status Cards Grid -->
            <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Current/Next Class Card -->
                @if($currentClass || $nextClass)
                    <div class="overflow-hidden rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                        <div class="p-6 text-white">
                            @if($currentClass)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium opacity-90">Clase en curso</p>
                                        <h3 class="mt-2 text-xl font-bold">{{ $currentClass->subject->name }}</h3>
                                        <p class="mt-1 text-sm opacity-75">
                                            {{ $currentClass->start_time }} - {{ $currentClass->end_time }}
                                        </p>
                                        <p class="mt-2 text-sm">
                                            👨‍🏫 {{ $currentClass->subject->teacher->user->name }}
                                        </p>
                                    </div>
                                    <div class="text-4xl">🎓</div>
                                </div>
                            @elseif($nextClass)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium opacity-90">Próxima clase</p>
                                        <h3 class="mt-2 text-xl font-bold">{{ $nextClass->subject->name }}</h3>
                                        <p class="mt-1 text-sm opacity-75">
                                            {{ $nextClass->start_time }} - {{ $nextClass->end_time }}
                                        </p>
                                        <p class="mt-2 text-sm">
                                            👨‍🏫 {{ $nextClass->subject->teacher->user->name }}
                                        </p>
                                    </div>
                                    <div class="text-4xl">⏰</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Pending Assignments Card -->
                <div class="overflow-hidden rounded-lg bg-gradient-to-br from-yellow-500 to-orange-600 shadow-lg">
                    <div class="p-6 text-white">
                        <p class="text-sm font-medium opacity-90">Tareas Pendientes</p>
                        <h3 class="mt-2 text-4xl font-bold">{{ $pendingAssignments }}</h3>
                        <p class="mt-2 text-sm opacity-75">por entregar próximamente</p>
                        <a href="{{ route('student.assignments') }}" class="mt-4 inline-flex text-sm font-medium hover:opacity-80">
                            Ver tareas →
                        </a>
                    </div>
                </div>

                <!-- Overdue Assignments Card -->
                <div class="overflow-hidden rounded-lg bg-gradient-to-br from-red-500 to-pink-600 shadow-lg">
                    <div class="p-6 text-white">
                        <p class="text-sm font-medium opacity-90">Tareas Vencidas</p>
                        <h3 class="mt-2 text-4xl font-bold">{{ $overdueAssignments }}</h3>
                        <p class="mt-2 text-sm opacity-75">que debes entregar ya</p>
                        <a href="{{ route('student.assignments') }}" class="mt-4 inline-flex text-sm font-medium hover:opacity-80">
                            Ver tareas →
                        </a>
                    </div>
                </div>

                <!-- Grades Card -->
                <div class="overflow-hidden rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg">
                    <div class="p-6 text-white">
                        <p class="text-sm font-medium opacity-90">Calificaciones</p>
                        <h3 class="mt-2 text-4xl font-bold">{{ $totalGrades }}</h3>
                        <p class="mt-2 text-sm opacity-75">registradas</p>
                        <a href="{{ route('student.grades') }}" class="mt-4 inline-flex text-sm font-medium hover:opacity-80">
                            Ver calificaciones →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column - Schedule and Assignments -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Today's Schedule -->
                    @if($todaySchedules->count() > 0)
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                            <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4">
                                <h3 class="text-lg font-bold text-gray-900">📅 Horario de Hoy</h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                @foreach($todaySchedules as $schedule)
                                    @php
                                        $isCurrentClass = $currentClass && $currentClass->id === $schedule->id;
                                        $isNextClass = $nextClass && $nextClass->id === $schedule->id;
                                    @endphp
                                    <div class="p-4 transition-colors {{ $isCurrentClass ? 'bg-blue-50' : ($isNextClass ? 'bg-amber-50' : 'hover:bg-gray-50') }}">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0 text-2xl">
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
                                                <p class="mt-1 text-sm text-gray-500">
                                                    🕐 {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                    @if($schedule->classroom)
                                                        • Salón {{ $schedule->classroom }}
                                                    @endif
                                                </p>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    👨‍🏫 {{ $schedule->subject->teacher->user->name }}
                                                </p>
                                                @if($isCurrentClass)
                                                    <span class="mt-2 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                                        En curso
                                                    </span>
                                                @elseif($isNextClass)
                                                    <span class="mt-2 inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                        Próxima
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="bg-gray-50 px-6 py-3">
                                <a href="{{ route('student.schedule') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                    Ver horario completo →
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Upcoming Assignments -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                        <div class="border-b border-gray-200 bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4">
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
                                    <div class="p-4 hover:bg-gray-50 transition-colors {{ $isUrgent ? 'bg-red-50' : ($isDueSoon ? 'bg-amber-50' : '') }}">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Materia: <span class="font-medium">{{ $assignment->subject->name }}</span>
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 line-clamp-2">
                                                    {{ $assignment->description }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                @if($isUrgent)
                                                    <span class="inline-block rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                        Urgente
                                                    </span>
                                                @elseif($isDueSoon)
                                                    <span class="inline-block rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">
                                                        Pronto
                                                    </span>
                                                @endif
                                                <p class="mt-2 text-sm font-medium text-gray-900">
                                                    {{ $assignment->due_date->format('d/m') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $assignment->due_date->format('H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="bg-gray-50 px-6 py-3">
                                <a href="{{ route('student.assignments') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                    Ver todas las tareas →
                                </a>
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <p class="text-gray-500">¡No tienes tareas pendientes! 🎉</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Student Info Card -->
                    @if($student)
                        <div class="overflow-hidden rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg text-white">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 text-4xl">👤</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium opacity-75">Mi Información</p>
                                        <h3 class="mt-1 text-lg font-bold truncate">{{ auth()->user()->full_name }}</h3>
                                        <p class="mt-1 text-sm opacity-75">
                                            Matrícula: {{ $student->enrollment_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Announcements -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                        <div class="border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4">
                            <h3 class="text-lg font-bold text-gray-900">📢 Anuncios</h3>
                        </div>
                        @if($recentAnnouncements->count() > 0)
                            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                @foreach($recentAnnouncements->take(5) as $announcement)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h4>
                                        <p class="mt-2 text-xs text-gray-600 line-clamp-2">{{ $announcement->content }}</p>
                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ $announcement->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-gray-500 text-sm">
                                No hay anuncios recientes
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h3 class="text-lg font-bold text-gray-900">⚡ Accesos Rápidos</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <a href="{{ route('messages.inbox') }}" class="block p-4 hover:bg-blue-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">✉️</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Mensajes</p>
                                        <p class="text-xs text-gray-500">
                                            @if($unreadMessageCount > 0)
                                                {{ $unreadMessageCount }} sin leer
                                            @else
                                                Todo actualizado
                                            @endif
                                        </p>
                                    </div>
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
                                                {{ $pendingAssignments }} pendientes
                                            @else
                                                Todas entregadas
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
                                        <p class="text-xs text-gray-500">Ver semana completa</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block p-4 hover:bg-indigo-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">⚙️</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Perfil</p>
                                        <p class="text-xs text-gray-500">Editar información</p>
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
