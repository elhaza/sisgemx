<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Asignar Estudiantes a {{ $schoolYear->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 overflow-hidden rounded-lg bg-blue-50 p-4">
                <h3 class="font-semibold text-blue-900">Información</h3>
                <p class="mt-1 text-sm text-blue-800">
                    Los estudiantes del ciclo <strong>{{ $previousSchoolYear->name }}</strong> serán asignados automáticamente al nivel superior en <strong>{{ $schoolYear->name }}</strong>.
                    Los descuentos NO se copiarán y deberán asignarse manualmente después.
                </p>
            </div>

            <form action="{{ route('admin.school-years.store-student-assignments', $schoolYear) }}" method="POST" id="assignment-form">
                @csrf

                <!-- Estudiantes que pueden avanzar -->
                @if($studentsToAdvance->count() > 0)
                    <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Estudiantes a Avanzar de Nivel ({{ $studentsToAdvance->count() }})
                                </h3>
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all-advance" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Seleccionar todos</span>
                                </label>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Incluir
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Matrícula
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Nombre
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Nivel Actual
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Grupo Actual
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Asignar a Grupo
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($studentsToAdvance as $student)
                                            <tr class="student-row advance-row">
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    <input type="checkbox"
                                                        name="students[]"
                                                        value="{{ $student->id }}"
                                                        checked
                                                        class="student-checkbox advance-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $student->enrollment_number }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                    {{ $student->user->full_name }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {{ $student->schoolGrade->level }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {{ $student->schoolGrade->section }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                    <select name="assignments[{{ $student->id }}]"
                                                        class="assignment-select rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        required>
                                                        @foreach($targetGrades as $grade)
                                                            <option value="{{ $grade->id }}"
                                                                {{ ($studentAssignments[$student->id] ?? null) == $grade->id ? 'selected' : '' }}>
                                                                {{ $grade->name }} - {{ $grade->section }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Estudiantes que terminaron todos los niveles -->
                @if($studentsCompleted->count() > 0)
                    <div class="mb-6 overflow-hidden rounded-lg border-2 border-yellow-400 bg-white shadow-sm">
                        <div class="border-b border-yellow-400 bg-yellow-50 px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-lg font-semibold text-yellow-900">
                                        Estudiantes que Completaron Todos los Niveles ({{ $studentsCompleted->count() }})
                                    </h3>
                                    <p class="mt-1 text-sm text-yellow-800">
                                        Los siguientes estudiantes están en el nivel más alto y no existe un nivel superior para avanzar.
                                        <strong>Seleccione los estudiantes que desea marcar como graduados.</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-end">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all-graduated" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <span class="ml-2 text-sm font-medium text-yellow-700">Seleccionar todos para graduar</span>
                                </label>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Marcar como Graduado
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Matrícula
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Nombre
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Nivel Actual
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Grupo Actual
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($studentsCompleted as $student)
                                            <tr class="graduated-row hover:bg-yellow-50">
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    <input type="checkbox"
                                                        name="graduated_students[]"
                                                        value="{{ $student->id }}"
                                                        class="graduated-checkbox rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $student->enrollment_number }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                    {{ $student->user->full_name }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {{ $student->schoolGrade->level }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {{ $student->schoolGrade->section }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.school-years.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Cancelar
                        </a>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Guardar Asignaciones
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Select/Deselect all advancing students
        document.getElementById('select-all-advance')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.advance-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                toggleRowDisabled(checkbox);
            });
        });

        // Select/Deselect all graduated students
        document.getElementById('select-all-graduated')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.graduated-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Individual checkbox handler for advancing students
        document.querySelectorAll('.advance-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleRowDisabled(this);
                updateSelectAllAdvanceState();
            });
        });

        // Individual checkbox handler for graduated students
        document.querySelectorAll('.graduated-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllGraduatedState();
            });
        });

        function toggleRowDisabled(checkbox) {
            const row = checkbox.closest('.student-row');
            const select = row.querySelector('.assignment-select');

            if (checkbox.checked) {
                row.classList.remove('opacity-50');
                select.disabled = false;
                select.required = true;
            } else {
                row.classList.add('opacity-50');
                select.disabled = true;
                select.required = false;
            }
        }

        function updateSelectAllAdvanceState() {
            const checkboxes = document.querySelectorAll('.advance-checkbox');
            const checkedCount = document.querySelectorAll('.advance-checkbox:checked').length;
            const selectAll = document.getElementById('select-all-advance');

            if (selectAll) {
                selectAll.checked = checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }
        }

        function updateSelectAllGraduatedState() {
            const checkboxes = document.querySelectorAll('.graduated-checkbox');
            const checkedCount = document.querySelectorAll('.graduated-checkbox:checked').length;
            const selectAll = document.getElementById('select-all-graduated');

            if (selectAll) {
                selectAll.checked = checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }
        }

        // Validate form submission
        document.getElementById('assignment-form').addEventListener('submit', function(e) {
            const advanceChecked = document.querySelectorAll('.advance-checkbox:checked').length;
            const graduatedChecked = document.querySelectorAll('.graduated-checkbox:checked').length;

            if (advanceChecked === 0 && graduatedChecked === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un estudiante para avanzar o marcar como graduado.');
                return false;
            }

            if (graduatedChecked > 0) {
                const confirmGraduation = confirm(
                    `Está a punto de marcar ${graduatedChecked} estudiante(s) como graduado(s). Esta acción cambiará su estatus permanentemente. ¿Desea continuar?`
                );

                if (!confirmGraduation) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</x-app-layout>
