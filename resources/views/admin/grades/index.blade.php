<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Calificaciones Generales
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Estadísticas -->
            <div class="mb-6 grid gap-6 md:grid-cols-2">
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
                                    <dt class="truncate text-sm font-medium text-gray-500">Total de Estudiantes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $totalStudents }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">Promedio General</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($averageGrade, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="mb-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.grades.index') }}" method="GET" class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select name="school_year_id" id="school_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($schoolYears as $schoolYear)
                                    <option value="{{ $schoolYear->id }}" {{ $selectedSchoolYear == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="school_grade_id" class="block text-sm font-medium text-gray-700">Grado/Sección</label>
                            <select name="school_grade_id" id="school_grade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($schoolGrades as $grade)
                                    <option value="{{ $grade->id }}" {{ $selectedSchoolGrade == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }} - Sección {{ $grade->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Materia</label>
                            <select name="subject_id" id="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <a href="{{ route('admin.grades.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Calificaciones -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Calificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Comentarios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($grades as $grade)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $grade->student->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $grade->student->enrollment_number }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $grade->subject->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $grade->grade >= 7 ? 'bg-green-100 text-green-800' : ($grade->grade >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ number_format($grade->grade, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $grade->comments ? Str::limit($grade->comments, 50) : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $grade->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay calificaciones registradas con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $grades->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
