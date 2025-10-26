<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Maestros / Materias
            </h2>
            <a href="{{ route('admin.subjects.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Nueva Materia
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <!-- Resumen de Maestros -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <button onclick="document.getElementById('teachers-summary').classList.toggle('hidden')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Maestros Asignados</h3>
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ count($teacherHours) }} maestros</span>
                    </div>
                    <svg class="h-5 w-5 text-gray-600 transform transition-transform" id="teachers-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <div id="teachers-summary" class="hidden border-t border-gray-200 bg-gray-50 p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Maestro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Horas Asignadas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Progreso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($teacherHours as $teacherId => $hours)
                                    @if($hours['hours'] > 0)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $hours['name'] }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $hours['hours'] }} de {{ $hours['max_hours'] }} horas</td>
                                        <td class="px-6 py-4">
                                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                                @php
                                                    $percentage = min(($hours['hours'] / $hours['max_hours']) * 100, 100);
                                                    $barColor = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                                                @endphp
                                                <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay maestros con materias asignadas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h3>
                    <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex flex-wrap gap-3">
                        <input type="text" name="subject_name" placeholder="Nombre de materia" value="{{ request('subject_name') }}"
                            class="rounded-md border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <select name="grade_level" class="rounded-md border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los grados</option>
                            @foreach($gradeLevels as $level)
                                <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>Grado {{ $level }}</option>
                            @endforeach
                        </select>

                        <select name="teacher_id" class="rounded-md border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los maestros</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Filtrar</button>
                        <a href="{{ route('admin.subjects.index') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpiar</a>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Horas/Semana</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Maestro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($subjects as $subject)
                                @if($subject->teacher_id && $subject->school_year_id)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                        @if($subject->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->grade_level }}°</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->default_hours_per_week ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->teacher?->full_name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->schoolYear?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta materia?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay materias registradas.</td>
                                </tr>
                            @endforelse

                            <!-- Fila para agregar nueva materia rápidamente -->
                            <tr id="new-subject-row" class="bg-blue-50 hover:bg-blue-100">
                                <td class="px-6 py-4">
                                    @if($subjectList->count() > 0)
                                        <select id="subject-name" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Seleccionar materia</option>
                                            @foreach($subjectList as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                            <option value="add-new">+ Agregar</option>
                                        </select>
                                    @else
                                        <input type="text" id="subject-name" placeholder="Nombre de la materia"
                                            class="w-full rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <select id="subject-grade" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Grado</option>
                                        @foreach($gradeLevels as $level)
                                            <option value="{{ $level }}">{{ $level }}°</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" id="subject-hours" placeholder="Horas/semana" step="0.5" min="0.5" max="40"
                                        class="w-full rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <select id="subject-teacher" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Maestro</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                        @endforeach
                                        <option value="add-new">+ Agregar</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <select id="subject-school-year" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Ciclo Escolar</option>
                                        @foreach($schoolYears as $year)
                                            <option value="{{ $year->id }}" {{ $activeSchoolYear?->id == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button id="add-subject-btn"
                                        class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                                        + Agregar
                                    </button>
                                </td>
                            </tr>

                            <!-- Fila para agregar nueva materia al catálogo -->
                            <tr id="new-subject-form-row" class="hidden bg-indigo-50 hover:bg-indigo-100">
                                <td colspan="6" class="px-6 py-4">
                                    <div class="flex gap-3">
                                        <input type="text" id="new-subject-input" placeholder="Nombre de la nueva materia"
                                            class="flex-1 rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <button id="add-new-subject-btn"
                                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                                            + Crear Materia
                                        </button>
                                        <button type="button" onclick="document.getElementById('new-subject-form-row').classList.add('hidden'); document.getElementById('subject-name').value = ''; document.getElementById('subject-name').focus();"
                                            class="rounded-md bg-gray-400 px-4 py-2 text-sm font-medium text-white hover:bg-gray-500 transition">
                                            Cancelar
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Fila para agregar nuevo maestro -->
                            <tr id="new-teacher-row" class="hidden bg-purple-50 hover:bg-purple-100">
                                <td colspan="5" class="px-6 py-4">
                                    <!-- Nombres (Nombre, Apellido Paterno, Apellido Materno) -->
                                    <div class="grid grid-cols-3 gap-3 mb-3">
                                        <input type="text" id="teacher-name" placeholder="Nombre"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <input type="text" id="teacher-first-last-name" placeholder="Apellido Paterno"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <input type="text" id="teacher-second-last-name" placeholder="Apellido Materno"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <!-- Email y Contraseña -->
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input type="email" id="teacher-email" placeholder="Email"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <input type="text" id="teacher-password" placeholder="Contraseña"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <!-- Horas Máximas -->
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input type="number" id="teacher-max-hours-per-day" placeholder="Máx horas/día" min="1" max="12" step="0.5" value="8"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <input type="number" id="teacher-max-hours-per-week" placeholder="Máx horas/semana" min="1" max="60" step="0.5" value="40"
                                            class="rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                                            @keyup.enter="document.getElementById('add-teacher-btn').click()">
                                    </div>

                                    <div class="flex gap-2">
                                        <button id="add-teacher-btn"
                                            class="rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 transition">
                                            + Crear Docente
                                        </button>
                                        <button id="cancel-teacher-btn"
                                            class="rounded-md bg-gray-400 px-4 py-2 text-sm font-medium text-white hover:bg-gray-500 transition">
                                            Cancelar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $subjects->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Manejar selección de "Agregar+" en el combo de materia (si existe)
        const subjectNameElement = document.getElementById('subject-name');
        if (subjectNameElement && subjectNameElement.tagName === 'SELECT') {
            subjectNameElement.addEventListener('change', function() {
                if (this.value === 'add-new') {
                    document.getElementById('new-subject-form-row').classList.remove('hidden');
                    document.getElementById('new-subject-input').focus();
                }
            });
        }

        // Crear nueva materia en el catálogo
        if (document.getElementById('add-new-subject-btn')) {
            document.getElementById('add-new-subject-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const subjectInput = document.getElementById('new-subject-input').value.trim();

                if (!subjectInput) {
                    alert('Por favor ingresa el nombre de la materia');
                    return;
                }

                const btn = this;
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Creando...';

                fetch('{{ route("admin.subjects.store-catalog") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        name: subjectInput
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error al crear la materia');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const subjectSelect = document.getElementById('subject-name');
                        const newOption = document.createElement('option');
                        newOption.value = data.subject.id;
                        newOption.text = data.subject.name;
                        newOption.selected = true;

                        const addNewOption = subjectSelect.querySelector('[value="add-new"]');
                        subjectSelect.insertBefore(newOption, addNewOption);

                        document.getElementById('new-subject-form-row').classList.add('hidden');
                        document.getElementById('new-subject-input').value = '';

                        showNotification('Materia creada exitosamente', 'success');
                    } else {
                        showNotification(data.message || 'Error al crear la materia', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Error al crear la materia', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
            });
        }

        // Manejar selección de "Agregar+" en el combo de maestro
        document.getElementById('subject-teacher').addEventListener('change', function() {
            if (this.value === 'add-new') {
                document.getElementById('new-teacher-row').classList.remove('hidden');
                document.getElementById('teacher-name').focus();
            }
        });

        // Botón para cancelar creación de maestro
        document.getElementById('cancel-teacher-btn').addEventListener('click', function() {
            document.getElementById('new-teacher-row').classList.add('hidden');
            document.getElementById('subject-teacher').value = '';
            // Limpiar inputs
            document.getElementById('teacher-name').value = '';
            document.getElementById('teacher-first-last-name').value = '';
            document.getElementById('teacher-second-last-name').value = '';
            document.getElementById('teacher-email').value = '';
            document.getElementById('teacher-password').value = '';
            document.getElementById('teacher-max-hours-per-day').value = '8';
            document.getElementById('teacher-max-hours-per-week').value = '40';
        });

        // Botón para crear nuevo maestro
        document.getElementById('add-teacher-btn').addEventListener('click', function(e) {
            e.preventDefault();

            const name = document.getElementById('teacher-name').value.trim();
            const firstLastName = document.getElementById('teacher-first-last-name').value.trim();
            const secondLastName = document.getElementById('teacher-second-last-name').value.trim();
            const email = document.getElementById('teacher-email').value.trim();
            const password = document.getElementById('teacher-password').value.trim();
            const maxHoursPerDay = document.getElementById('teacher-max-hours-per-day').value;
            const maxHoursPerWeek = document.getElementById('teacher-max-hours-per-week').value;

            // Validación básica
            if (!name || !firstLastName || !secondLastName || !email || !password) {
                alert('Por favor completa todos los campos');
                return;
            }

            if (password.length < 8) {
                alert('La contraseña debe tener al menos 8 caracteres');
                return;
            }

            if (!maxHoursPerDay || maxHoursPerDay < 1 || maxHoursPerDay > 12) {
                alert('Horas máximas por día debe ser entre 1 y 12');
                return;
            }

            if (!maxHoursPerWeek || maxHoursPerWeek < 1 || maxHoursPerWeek > 60) {
                alert('Horas máximas por semana debe ser entre 1 y 60');
                return;
            }

            const btn = this;
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Creando...';

            // Enviar via AJAX
            fetch('{{ route("admin.subjects.store-teacher") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    name: name,
                    apellido_paterno: firstLastName,
                    apellido_materno: secondLastName,
                    email: email,
                    password: password,
                    max_hours_per_day: parseFloat(maxHoursPerDay),
                    max_hours_per_week: parseFloat(maxHoursPerWeek)
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error al crear el docente');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Agregar nuevo maestro al combo
                    const teacherSelect = document.getElementById('subject-teacher');
                    const newOption = document.createElement('option');
                    newOption.value = data.teacher.id;
                    newOption.text = data.teacher.name;
                    newOption.selected = true;

                    // Insertar antes de la opción "Agregar+"
                    const addNewOption = teacherSelect.querySelector('[value="add-new"]');
                    teacherSelect.insertBefore(newOption, addNewOption);

                    // Esconder la fila
                    document.getElementById('new-teacher-row').classList.add('hidden');

                    // Limpiar inputs
                    document.getElementById('teacher-name').value = '';
                    document.getElementById('teacher-first-last-name').value = '';
                    document.getElementById('teacher-second-last-name').value = '';
                    document.getElementById('teacher-email').value = '';
                    document.getElementById('teacher-password').value = '';
                    document.getElementById('teacher-max-hours-per-day').value = '8';
                    document.getElementById('teacher-max-hours-per-week').value = '40';

                    showNotification('Docente creado exitosamente', 'success');
                } else {
                    showNotification(data.message || 'Error al crear el docente', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'Error al crear el docente', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });

        function submitSubject(formData) {
            const btn = document.getElementById('add-subject-btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Agregando...';

            fetch('{{ route("admin.subjects.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        if (data.warning) {
                            // Mostrar advertencia de horas
                            const confirmMsg = `⚠️ Advertencia: ${data.message}\n\n` +
                                `Maestro: ${data.teacher_name}\n` +
                                `Promedio diario actual: ${data.current_daily_average}h\n` +
                                `Horas máximas por día: ${data.max_hours_per_day}h\n` +
                                `Total de horas semanales: ${data.total_weekly_hours}h\n\n` +
                                `¿Deseas agregar esta materia de todas formas?`;

                            if (confirm(confirmMsg)) {
                                // Enviar con force = true
                                submitSubject({ ...formData, force: true });
                            }
                            btn.disabled = false;
                            btn.textContent = originalText;
                            return;
                        }
                        throw new Error(data.message || 'Error al agregar la materia');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const nameInput = document.getElementById('subject-name');
                    const grade_level = document.getElementById('subject-grade').value;
                    const hours = document.getElementById('subject-hours').value;

                    // Obtener información del maestro y ciclo escolar
                    const teacherSelect = document.getElementById('subject-teacher');
                    const yearSelect = document.getElementById('subject-school-year');
                    const teacherName = teacherSelect.options[teacherSelect.selectedIndex].text;
                    const yearName = yearSelect.options[yearSelect.selectedIndex].text;
                    const name = formData.name;

                    // Crear nueva fila
                    const newRow = document.createElement('tr');
                    newRow.className = 'animate-fadeIn';
                    newRow.innerHTML = `
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${name}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${grade_level}°</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${hours || '-'}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${teacherName}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${yearName}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ url('admin/subjects') }}/${data.id}/edit" class="text-blue-600 hover:text-blue-900">Editar</a>
                                <form action="{{ url('admin/subjects') }}/${data.id}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta materia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    `;

                    // Insertar antes de la fila de agregar
                    const newSubjectRow = document.getElementById('new-subject-row');
                    newSubjectRow.parentNode.insertBefore(newRow, newSubjectRow);

                    // Limpiar inputs
                    if (nameInput.tagName === 'SELECT') {
                        nameInput.value = '';
                    } else {
                        nameInput.value = '';
                    }
                    document.getElementById('subject-grade').value = '';
                    document.getElementById('subject-hours').value = '';
                    document.getElementById('subject-teacher').value = '';
                    document.getElementById('subject-school-year').value = '{{ $activeSchoolYear?->id }}';

                    // Mostrar notificación de éxito
                    showNotification('Materia agregada exitosamente', 'success');

                    // Enfocar el input de nombre para agregar otra
                    nameInput.focus();

                    btn.disabled = false;
                    btn.textContent = originalText;
                } else {
                    showNotification(data.message || 'Error al agregar la materia', 'error');
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'Error al agregar la materia', 'error');
                btn.disabled = false;
                btn.textContent = originalText;
            });
        }

        document.getElementById('add-subject-btn').addEventListener('click', function(e) {
            e.preventDefault();

            const nameInput = document.getElementById('subject-name');
            let name, subject_id;

            // Determinar si es un combo o un input
            if (nameInput.tagName === 'SELECT') {
                subject_id = nameInput.value;
                name = nameInput.options[nameInput.selectedIndex].text;
            } else {
                name = nameInput.value.trim();
                subject_id = null;
            }

            const grade_level = document.getElementById('subject-grade').value;
            const hours = document.getElementById('subject-hours').value;
            const teacher_id = document.getElementById('subject-teacher').value;
            const school_year_id = document.getElementById('subject-school-year').value;

            // Validación básica
            if (!subject_id && !name) {
                alert('Por favor selecciona o ingresa el nombre de la materia');
                nameInput.focus();
                return;
            }

            if (!grade_level) {
                alert('Por favor selecciona un grado');
                document.getElementById('subject-grade').focus();
                return;
            }

            if (!teacher_id || teacher_id === 'add-new') {
                alert('Por favor selecciona un maestro válido o crea uno nuevo primero');
                document.getElementById('subject-teacher').focus();
                return;
            }

            if (!school_year_id) {
                alert('Por favor selecciona un ciclo escolar');
                document.getElementById('subject-school-year').focus();
                return;
            }

            submitSubject({
                name: name,
                grade_level: grade_level,
                teacher_id: teacher_id,
                school_year_id: school_year_id,
                default_hours_per_week: hours || null,
                description: ''
            });
        });

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 rounded-lg p-4 shadow-lg text-white transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Permitir agregar con Enter en el último campo
        document.getElementById('subject-school-year').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('add-subject-btn').click();
            }
        });

        // Animación del chevron al expandir/contraer maestros
        document.querySelector('button[onclick*="teachers-summary"]').addEventListener('click', function() {
            const chevron = document.getElementById('teachers-chevron');
            chevron.classList.toggle('rotate-180');
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</x-app-layout>
