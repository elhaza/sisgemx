<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Transferencia de Estudiantes entre Grupos
            </h2>
            <a href="{{ route('admin.students.index') }}" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Volver a Lista de Estudiantes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Información</h3>
                    <div class="rounded-lg bg-blue-50 p-4">
                        <ul class="list-inside list-disc space-y-2 text-sm text-blue-900">
                            <li>Selecciona el grupo origen para ver sus estudiantes</li>
                            <li>Marca los estudiantes que deseas transferir (puedes usar "Seleccionar Todos")</li>
                            <li>Selecciona el ciclo escolar y grado destino</li>
                            <li>Los estudiantes mantendrán su información personal y académica</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Panel Izquierdo: Selección de Origen -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Grupo Origen</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="source_school_grade_id" class="block text-sm font-medium text-gray-700">
                                Seleccionar Grupo
                            </label>
                            <select id="source_school_grade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Seleccione un grupo --</option>
                                @foreach($schoolGrades as $grade)
                                    <option value="{{ $grade->id }}">
                                        {{ $grade->schoolYear->name }} - {{ $grade->name }} {{ $grade->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="students-list" class="hidden">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="select-all" class="ml-2 text-sm font-medium text-gray-700">
                                        Seleccionar Todos
                                    </label>
                                </div>
                                <span id="selected-count" class="text-sm text-gray-600">0 seleccionados</span>
                            </div>

                            <div id="students-container" class="max-h-96 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3">
                                <!-- Students will be loaded here -->
                            </div>
                        </div>

                        <div id="no-students" class="hidden rounded-lg bg-gray-50 p-4 text-center text-sm text-gray-600">
                            No hay estudiantes en este grupo
                        </div>
                    </div>
                </div>

                <!-- Panel Derecho: Selección de Destino -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Grupo Destino</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="target_school_year_id" class="block text-sm font-medium text-gray-700">
                                Ciclo Escolar
                            </label>
                            <select id="target_school_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Seleccione un ciclo --</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="target_school_grade_id" class="block text-sm font-medium text-gray-700">
                                Grado y Grupo
                            </label>
                            <select id="target_school_grade_id" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100">
                                <option value="">-- Primero seleccione un ciclo --</option>
                            </select>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h4 class="mb-2 text-sm font-semibold text-gray-700">Resumen de Transferencia</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p id="summary-students">Estudiantes seleccionados: <span class="font-semibold">0</span></p>
                                <p id="summary-target">Destino: <span class="font-semibold">No seleccionado</span></p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button id="transfer-btn" disabled class="w-full rounded-md bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-400">
                                Transferir Estudiantes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedStudents = [];
        let allStudents = [];
        let targetSchoolGrades = @json($schoolGrades);

        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
        });

        function setupEventListeners() {
            // Source school grade selector
            document.getElementById('source_school_grade_id').addEventListener('change', function() {
                loadStudents(this.value);
            });

            // Select all checkbox
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    if (this.checked) {
                        if (!selectedStudents.includes(parseInt(cb.value))) {
                            selectedStudents.push(parseInt(cb.value));
                        }
                    } else {
                        selectedStudents = [];
                    }
                });
                updateSelectedCount();
                updateTransferButton();
            });

            // Target school year selector
            document.getElementById('target_school_year_id').addEventListener('change', function() {
                const schoolYearId = this.value;
                const targetGradeSelect = document.getElementById('target_school_grade_id');

                if (schoolYearId) {
                    const grades = targetSchoolGrades.filter(g => g.school_year_id == schoolYearId);

                    targetGradeSelect.disabled = false;
                    targetGradeSelect.innerHTML = '<option value="">-- Seleccione un grado --</option>';

                    grades.forEach(grade => {
                        const option = document.createElement('option');
                        option.value = grade.id;
                        option.textContent = `${grade.name} ${grade.section}`;
                        targetGradeSelect.appendChild(option);
                    });
                } else {
                    targetGradeSelect.disabled = true;
                    targetGradeSelect.innerHTML = '<option value="">-- Primero seleccione un ciclo --</option>';
                }

                updateSummary();
                updateTransferButton();
            });

            // Target school grade selector
            document.getElementById('target_school_grade_id').addEventListener('change', function() {
                updateSummary();
                updateTransferButton();
            });

            // Transfer button
            document.getElementById('transfer-btn').addEventListener('click', function() {
                transferStudents();
            });
        }

        function loadStudents(schoolGradeId) {
            if (!schoolGradeId) {
                document.getElementById('students-list').classList.add('hidden');
                document.getElementById('no-students').classList.add('hidden');
                return;
            }

            fetch(`{{ route('admin.students.get-students') }}?school_grade_id=${schoolGradeId}`)
                .then(response => response.json())
                .then(students => {
                    allStudents = students;
                    selectedStudents = [];

                    if (students.length === 0) {
                        document.getElementById('students-list').classList.add('hidden');
                        document.getElementById('no-students').classList.remove('hidden');
                        return;
                    }

                    document.getElementById('students-list').classList.remove('hidden');
                    document.getElementById('no-students').classList.add('hidden');

                    const container = document.getElementById('students-container');
                    container.innerHTML = '';

                    students.forEach(student => {
                        const div = document.createElement('div');
                        div.className = 'flex items-center rounded-lg border border-gray-200 bg-white p-3 hover:bg-gray-50';
                        div.innerHTML = `
                            <input type="checkbox" class="student-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${student.id}">
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">${student.user.name}</p>
                                <p class="text-xs text-gray-500">Matrícula: ${student.enrollment_number}</p>
                            </div>
                        `;

                        const checkbox = div.querySelector('.student-checkbox');
                        checkbox.addEventListener('change', function() {
                            const studentId = parseInt(this.value);
                            if (this.checked) {
                                if (!selectedStudents.includes(studentId)) {
                                    selectedStudents.push(studentId);
                                }
                            } else {
                                selectedStudents = selectedStudents.filter(id => id !== studentId);
                                document.getElementById('select-all').checked = false;
                            }
                            updateSelectedCount();
                            updateTransferButton();
                        });

                        container.appendChild(div);
                    });

                    updateSelectedCount();
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    showNotification('Error al cargar los estudiantes', 'error');
                });
        }

        function updateSelectedCount() {
            const count = selectedStudents.length;
            document.getElementById('selected-count').textContent = `${count} seleccionado${count !== 1 ? 's' : ''}`;
            document.getElementById('summary-students').innerHTML = `Estudiantes seleccionados: <span class="font-semibold">${count}</span>`;
        }

        function updateSummary() {
            const targetYearSelect = document.getElementById('target_school_year_id');
            const targetGradeSelect = document.getElementById('target_school_grade_id');

            let summaryText = 'No seleccionado';

            if (targetYearSelect.value && targetGradeSelect.value) {
                const yearText = targetYearSelect.options[targetYearSelect.selectedIndex].text;
                const gradeText = targetGradeSelect.options[targetGradeSelect.selectedIndex].text;
                summaryText = `${yearText} - ${gradeText}`;
            }

            document.getElementById('summary-target').innerHTML = `Destino: <span class="font-semibold">${summaryText}</span>`;
        }

        function updateTransferButton() {
            const btn = document.getElementById('transfer-btn');
            const targetYearId = document.getElementById('target_school_year_id').value;
            const targetGradeId = document.getElementById('target_school_grade_id').value;

            btn.disabled = !(selectedStudents.length > 0 && targetYearId && targetGradeId);
        }

        function transferStudents() {
            const targetSchoolYearId = document.getElementById('target_school_year_id').value;
            const targetSchoolGradeId = document.getElementById('target_school_grade_id').value;

            if (selectedStudents.length === 0 || !targetSchoolYearId || !targetSchoolGradeId) {
                showNotification('Por favor complete todos los campos requeridos', 'error');
                return;
            }

            if (!confirm(`¿Está seguro de transferir ${selectedStudents.length} estudiante(s)?`)) {
                return;
            }

            const btn = document.getElementById('transfer-btn');
            btn.disabled = true;
            btn.textContent = 'Transfiriendo...';

            fetch('{{ route("admin.students.transfer-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_ids: selectedStudents,
                    target_school_year_id: targetSchoolYearId,
                    target_school_grade_id: targetSchoolGradeId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification(result.message, 'success');

                    // Reset form
                    selectedStudents = [];
                    document.getElementById('source_school_grade_id').value = '';
                    document.getElementById('target_school_year_id').value = '';
                    document.getElementById('target_school_grade_id').value = '';
                    document.getElementById('target_school_grade_id').disabled = true;
                    document.getElementById('students-list').classList.add('hidden');
                    updateSelectedCount();
                    updateSummary();
                } else {
                    showNotification(result.message || 'Error al transferir estudiantes', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al transferir estudiantes', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Transferir Estudiantes';
            });
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 rounded-lg p-4 shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>
