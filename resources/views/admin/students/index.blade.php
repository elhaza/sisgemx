<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Lista de Estudiantes
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.students.transfer') }}" class="rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">
                    Transferir entre Grupos
                </a>
                <a href="{{ route('admin.students.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Inscribir Estudiante
                </a>
            </div>
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

            <!-- Filtros -->
            <div class="mb-4 overflow-hidden bg-white shadow-sm sm:rounded-lg" style="max-height: 200px;">
                <div class="p-3">
                    <form method="GET" action="{{ route('admin.students.index') }}" id="students-filter-form">
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text" name="enrollment_number" value="{{ request('enrollment_number') }}" placeholder="Matrícula" class="filter-input w-32 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                            <input type="text" name="name" value="{{ request('name') }}" placeholder="Nombre" class="filter-input w-36 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                            <select name="grade_level" class="filter-select w-24 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Grado</option>
                                @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>

                            <select name="group" class="filter-select w-20 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Grupo</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                                @endforeach
                            </select>

                            <select name="school_year_id" class="filter-select w-40 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Ciclo Escolar</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" {{ (request('school_year_id') == $year->id) || (!request('school_year_id') && $year->is_active) ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="has_discount" class="filter-select w-32 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Descuento</option>
                                <option value="yes" {{ request('has_discount') === 'yes' ? 'selected' : '' }}>Con descuento</option>
                                <option value="no" {{ request('has_discount') === 'no' ? 'selected' : '' }}>Sin descuento</option>
                            </select>

                            <select name="gender" class="filter-select w-28 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sexo</option>
                                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Femenino</option>
                                <option value="unspecified" {{ request('gender') === 'unspecified' ? 'selected' : '' }}>No especificado</option>
                            </select>

                            <button type="submit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Filtrar</button>

                            <a href="{{ route('admin.students.index') }}" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Matrícula</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sexo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grupo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($students as $student)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $student->enrollment_number }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $student->user->apellido_paterno }} {{ $student->user->apellido_materno }} {{ $student->user->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if($student->gender)
                                                @if($student->gender->value === 'male')
                                                    <span class="text-blue-600">Masculino</span>
                                                @elseif($student->gender->value === 'female')
                                                    <span class="text-pink-600">Femenino</span>
                                                @else
                                                    <span class="text-gray-500">No especificado</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $student->schoolGrade->level }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $student->schoolGrade->section }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $student->schoolYear->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @php
                                                $discount = $student->tuitions->first()?->discount_percentage ?? 0;
                                            @endphp
                                            @if($discount > 0)
                                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold leading-5 text-green-800">
                                                    {{ number_format($discount, 0) }}% descuento
                                                </span>
                                            @else
                                                <span class="text-gray-500">Sin descuento</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.students.edit', $student) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay estudiantes inscritos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('students-filter-form');
            const filterInputs = document.querySelectorAll('.filter-input');
            const filterSelects = document.querySelectorAll('.filter-select');
            let debounceTimer;

            // Función para enviar el formulario con debounce
            function submitFormWithDebounce() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    form.submit();
                }, 500); // Espera 500ms después de que el usuario deje de escribir
            }

            // Agregar listeners a los inputs de texto
            filterInputs.forEach(input => {
                input.addEventListener('input', submitFormWithDebounce);
            });

            // Agregar listeners a los selects (sin debounce, envío inmediato)
            filterSelects.forEach(select => {
                select.addEventListener('change', () => {
                    clearTimeout(debounceTimer);
                    form.submit();
                });
            });
        })();
    </script>
</x-app-layout>
