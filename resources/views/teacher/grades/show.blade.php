<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📊 Detalles de Calificación
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('teacher.grades.edit', $grade) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <form action="{{ route('teacher.grades.destroy', $grade) }}" method="POST" class="inline" onsubmit="return confirm('¿Deseas eliminar esta calificación?')">
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
            <!-- Main Grade Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm mb-6">
                <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ $grade->student->user->full_name }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">Materia:</span> {{ $grade->subject->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium">Grado:</span> {{ $grade->student->schoolGrade->grade_level ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Calificación Destacada -->
                    <div class="rounded-lg bg-gradient-to-r
                        @if ($grade->grade >= 80)
                            from-green-50 to-green-100
                        @elseif ($grade->grade >= 60)
                            from-yellow-50 to-yellow-100
                        @else
                            from-red-50 to-red-100
                        @endif
                        p-6 text-center">
                        <p class="text-sm font-medium
                            @if ($grade->grade >= 80)
                                text-green-700
                            @elseif ($grade->grade >= 60)
                                text-yellow-700
                            @else
                                text-red-700
                            @endif
                        ">Calificación</p>
                        <p class="text-5xl font-bold
                            @if ($grade->grade >= 80)
                                text-green-900
                            @elseif ($grade->grade >= 60)
                                text-yellow-900
                            @else
                                text-red-900
                            @endif
                            mt-2">
                            {{ $grade->grade }}
                        </p>
                        <p class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                            @if ($grade->grade >= 80)
                                bg-green-200 text-green-800
                            @elseif ($grade->grade >= 60)
                                bg-yellow-200 text-yellow-800
                            @else
                                bg-red-200 text-red-800
                            @endif
                        ">
                            @if ($grade->grade >= 80)
                                Excelente
                            @elseif ($grade->grade >= 60)
                                Bueno
                            @else
                                Bajo
                            @endif
                        </p>
                    </div>

                    <!-- Información de la Calificación -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium text-gray-600 uppercase">Período</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $grade->period }}</p>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-medium text-gray-600 uppercase">Profesor</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $grade->teacher->full_name }}</p>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    @if ($grade->comments)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Comentarios</h4>
                            <div class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700 bg-gray-50 rounded-md p-4">
                                {{ $grade->comments }}
                            </div>
                        </div>
                    @endif

                    <!-- Información de Creación -->
                    <div class="border-t pt-4">
                        <p class="text-xs text-gray-500">
                            Registrada: {{ $grade->created_at->format('d/m/Y \a \l\a\s H:i') }}
                            @if ($grade->updated_at->ne($grade->created_at))
                                <br>Última actualización: {{ $grade->updated_at->format('d/m/Y \a \l\a\s H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back to Grades -->
            <div>
                <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Calificaciones
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
