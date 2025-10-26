<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Materias
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

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Maestro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($subjects as $subject)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                        @if($subject->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->grade_level }}°</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->teacher->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $subject->schoolYear->name }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay materias registradas.</td>
                                </tr>
                            @endforelse

                            <!-- Fila para agregar nueva materia rápidamente -->
                            <tr id="new-subject-row" class="bg-blue-50 hover:bg-blue-100">
                                <td class="px-6 py-4">
                                    <input type="text" id="subject-name" placeholder="Nombre de la materia"
                                        class="w-full rounded-md border-gray-300 px-2 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                                        @keyup.enter="document.getElementById('add-subject-btn').click()">
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
                                    <select id="subject-teacher" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Maestro</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
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
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $subjects->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-subject-btn').addEventListener('click', function(e) {
            e.preventDefault();

            const name = document.getElementById('subject-name').value.trim();
            const grade_level = document.getElementById('subject-grade').value;
            const teacher_id = document.getElementById('subject-teacher').value;
            const school_year_id = document.getElementById('subject-school-year').value;

            // Validación básica
            if (!name) {
                alert('Por favor ingresa el nombre de la materia');
                document.getElementById('subject-name').focus();
                return;
            }

            if (!grade_level) {
                alert('Por favor selecciona un grado');
                document.getElementById('subject-grade').focus();
                return;
            }

            if (!teacher_id) {
                alert('Por favor selecciona un maestro');
                document.getElementById('subject-teacher').focus();
                return;
            }

            if (!school_year_id) {
                alert('Por favor selecciona un ciclo escolar');
                document.getElementById('subject-school-year').focus();
                return;
            }

            const btn = this;
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Agregando...';

            // Enviar via AJAX
            fetch('{{ route("admin.subjects.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    grade_level: grade_level,
                    teacher_id: teacher_id,
                    school_year_id: school_year_id,
                    description: ''
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error al agregar la materia');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Obtener información del maestro y ciclo escolar
                    const teacherSelect = document.getElementById('subject-teacher');
                    const yearSelect = document.getElementById('subject-school-year');
                    const teacherName = teacherSelect.options[teacherSelect.selectedIndex].text;
                    const yearName = yearSelect.options[yearSelect.selectedIndex].text;

                    // Crear nueva fila
                    const newRow = document.createElement('tr');
                    newRow.className = 'animate-fadeIn';
                    newRow.innerHTML = `
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${name}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${grade_level}°</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${teacherName}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">${yearName}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.subjects.edit', '') }}/${data.id}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                <form action="{{ route('admin.subjects.destroy', '') }}/${data.id}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta materia?')">
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
                    document.getElementById('subject-name').value = '';
                    document.getElementById('subject-grade').value = '';
                    document.getElementById('subject-teacher').value = '';
                    document.getElementById('subject-school-year').value = '{{ $activeSchoolYear?->id }}';

                    // Mostrar notificación de éxito
                    showNotification('Materia agregada exitosamente', 'success');

                    // Enfocar el input de nombre para agregar otra
                    document.getElementById('subject-name').focus();
                } else {
                    showNotification(data.message || 'Error al agregar la materia', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(error.message || 'Error al agregar la materia', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
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
