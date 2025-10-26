<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Secciones de Grado
            </h2>
            <a href="{{ route('admin.grade-sections.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Crear Sección
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filtro por ciclo escolar -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.grade-sections.index') }}" class="flex gap-4 items-end">
                        <div>
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Filtrar por Ciclo Escolar
                            </label>
                            <select name="school_year_id" id="school_year_id" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Todos los ciclos --</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" {{ request('school_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.grade-sections.index') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sección</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiantes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($gradeSections as $gradeSection)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $gradeSection->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->grade_level }}°</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->section }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->schoolYear->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                                {{ $gradeSection->students()->count() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.students.index', ['grade_level' => $gradeSection->grade_level, 'group' => $gradeSection->section, 'school_year_id' => $gradeSection->school_year_id]) }}" class="text-green-600 hover:text-green-900">Ver</a>
                                            <a href="{{ route('admin.grade-sections.edit', $gradeSection) }}" class="ml-3 text-blue-600 hover:text-blue-900">Editar</a>
                                            <form action="{{ route('admin.grade-sections.destroy', $gradeSection) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay secciones de grado registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $gradeSections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
