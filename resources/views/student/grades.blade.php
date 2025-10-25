<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mis Calificaciones
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

            @if($grades->count() > 0)
                @php
                    $totalGrade = $grades->avg('grade');
                @endphp
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500">Promedio General</p>
                            <p class="mt-2 text-5xl font-bold {{ $totalGrade >= 7 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($totalGrade, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Historial de Calificaciones</h3>
                </div>
                <div class="p-6">
                    @if($grades->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo de Evaluación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Calificación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Comentarios</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ $grade->subject->name }}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $grade->grade_type }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $grade->grade >= 7 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ number_format($grade->grade, 2) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $grade->created_at->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4">
                                                <div class="max-w-xs truncate text-sm text-gray-600">
                                                    {{ $grade->comments ?? 'Sin comentarios' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $grades->links() }}
                        </div>
                    @else
                        <div class="rounded-lg bg-yellow-50 p-4 text-center">
                            <p class="text-gray-700">Aún no tienes calificaciones registradas.</p>
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
