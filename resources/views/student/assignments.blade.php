<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mis Tareas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if($student)
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Información del Estudiante</h3>
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

            @php
                $pending = $assignments->filter(fn($a) => $a->due_date >= now());
                $overdue = $assignments->filter(fn($a) => $a->due_date < now());
            @endphp

            @if($pending->count() > 0 || $overdue->count() > 0)
                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Tareas Pendientes</dt>
                                        <dd class="text-3xl font-semibold text-gray-900">{{ $pending->count() }}</dd>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-gray-500">Tareas Vencidas</dt>
                                        <dd class="text-3xl font-semibold text-gray-900">{{ $overdue->count() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Lista de Tareas</h3>
                </div>
                <div class="p-6">
                    @if($assignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($assignments as $assignment)
                                @php
                                    $isOverdue = $assignment->due_date < now();
                                    $daysUntilDue = now()->diffInDays($assignment->due_date, false);
                                @endphp
                                <div class="rounded-lg border-2 {{ $isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-200' }} p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $assignment->title }}</h4>
                                                @if($isOverdue)
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                                        Vencida
                                                    </span>
                                                @elseif($daysUntilDue <= 2)
                                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">
                                                        Próxima a vencer
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">
                                                <span class="font-medium">Materia:</span> {{ $assignment->subject->name }}
                                            </p>
                                            <p class="mt-2 text-gray-700">{{ $assignment->description }}</p>
                                            <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Fecha de entrega: {{ $assignment->due_date->format('d/m/Y H:i') }}
                                                </span>
                                                @if($isOverdue)
                                                    <span class="font-semibold text-red-600">
                                                        ({{ abs($daysUntilDue) }} {{ abs($daysUntilDue) == 1 ? 'día' : 'días' }} de retraso)
                                                    </span>
                                                @else
                                                    <span class="text-green-600">
                                                        (Faltan {{ $daysUntilDue }} {{ $daysUntilDue == 1 ? 'día' : 'días' }})
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $assignments->links() }}
                        </div>
                    @else
                        <div class="rounded-lg bg-yellow-50 p-4 text-center">
                            <p class="text-gray-700">No tienes tareas asignadas en este momento.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('student.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
