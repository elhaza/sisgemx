<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📋 Detalles de la Tarea
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('¿Deseas eliminar esta tarea?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <!-- Main Assignment Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm mb-6">
                <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ $assignment->title }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">Materia:</span> {{ $assignment->subject->name }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Descripción -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Descripción</h4>
                        <div class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700 bg-gray-50 rounded-md p-4">
                            {{ $assignment->description }}
                        </div>
                    </div>

                    <!-- Información de la Tarea -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium text-gray-600 uppercase">Fecha de Vencimiento</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $assignment->due_date->format('d/m/Y') }}
                            </p>
                            @if ($assignment->due_date->isPast())
                                <p class="mt-1 text-xs text-red-600 font-medium">Vencida</p>
                            @elseif ($assignment->due_date->isToday())
                                <p class="mt-1 text-xs text-yellow-600 font-medium">Hoy</p>
                            @else
                                <p class="mt-1 text-xs text-green-600 font-medium">
                                    Faltan {{ $assignment->due_date->diffInDays() }} días
                                </p>
                            @endif
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium text-gray-600 uppercase">Puntos Máximos</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $assignment->max_points ?? 'Sin especificar' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium text-gray-600 uppercase">Estado</p>
                            <p class="mt-2">
                                @if ($assignment->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                        ✓ Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                        ✕ Inactiva
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Archivo Adjunto -->
                    @if ($assignment->attachment_path)
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start">
                                    @if ($assignment->attachment_type === 'image')
                                        <svg class="h-6 w-6 text-blue-600 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path>
                                        </svg>
                                    @elseif ($assignment->attachment_type === 'document')
                                        <svg class="h-6 w-6 text-red-600 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7H4v2h11V7zM4 5h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path>
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-gray-600 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7H4v2h11V7zM4 5h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path>
                                        </svg>
                                    @endif
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Archivo Adjunto</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ basename($assignment->attachment_path) }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('teacher.assignments.download', $assignment) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Información de Creación -->
                    <div class="border-t pt-4">
                        <p class="text-xs text-gray-500">
                            Creada: {{ $assignment->created_at->format('d/m/Y \a \l\a\s H:i') }}
                            @if ($assignment->updated_at->ne($assignment->created_at))
                                <br>Última actualización: {{ $assignment->updated_at->format('d/m/Y \a \l\a\s H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back to Assignments -->
            <div>
                <a href="{{ route('teacher.assignments.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Tareas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
