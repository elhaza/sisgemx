<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📋 Tareas Activas
            </h2>
            <a href="{{ route('teacher.assignments.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Asignar Nueva Tarea
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($activeAssignments->count() > 0)
                <div class="space-y-4">
                    @foreach ($activeAssignments as $assignment)
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $assignment->title }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600">
                                            Materia: <span class="font-medium">{{ $assignment->subject->name }}</span>
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('¿Deseas eliminar esta tarea?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md border border-red-300 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700">
                                    {{ Str::limit($assignment->description, 200) }}
                                </p>

                                @if ($assignment->attachment_path)
                                    <div class="mt-4">
                                        <div class="flex items-center rounded-lg bg-gray-50 p-3">
                                            @if ($assignment->attachment_type === 'image')
                                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path>
                                                </svg>
                                            @elseif ($assignment->attachment_type === 'document')
                                                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7H4v2h11V7zM4 5h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7H4v2h11V7zM4 5h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path>
                                                </svg>
                                            @endif
                                            <span class="ml-2 text-sm text-gray-600">Archivo adjunto</span>
                                            <a href="{{ route('teacher.assignments.download', $assignment) }}" class="ml-auto text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 flex items-center justify-between border-t pt-4">
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Fecha de vencimiento:</span>
                                        {{ $assignment->due_date->format('d/m/Y') }}
                                    </div>
                                    @if ($assignment->max_points)
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">Puntos máximos:</span>
                                            {{ $assignment->max_points }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $activeAssignments->links() }}
                </div>
            @else
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p class="mt-4 text-gray-600">No hay tareas activas en este momento</p>
                        <a href="{{ route('teacher.assignments.create') }}" class="mt-6 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Asignar Nueva Tarea
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
