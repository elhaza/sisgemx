<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Visualización de Horarios
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.schedules.visual') }}" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Creador Visual
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Nuevo Horario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex gap-6">
                        <button onclick="switchTab('visual')" id="tab-visual" class="tab-button border-b-2 border-blue-600 px-1 pb-4 text-sm font-medium text-blue-600">
                            Vista Visual
                        </button>
                        <button onclick="switchTab('list')" id="tab-list" class="tab-button border-b-2 border-transparent px-1 pb-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            Vista de Lista
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Vista Visual -->
            <div id="visual-view">
                <!-- Filtros -->
                <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="filter_school_year" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select id="filter_school_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los ciclos</option>
                                @foreach(\App\Models\SchoolYear::all() as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Activo)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="filter_school_grade" class="block text-sm font-medium text-gray-700">Grado Escolar</label>
                            <select id="filter_school_grade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los grados escolares</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Horario Visual -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                    <div class="min-w-max p-6">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="w-24 border border-gray-300 p-2 text-sm font-semibold">Hora</th>
                                    <th class="border border-gray-300 p-2 text-sm font-semibold">Lunes</th>
                                    <th class="border border-gray-300 p-2 text-sm font-semibold">Martes</th>
                                    <th class="border border-gray-300 p-2 text-sm font-semibold">Miércoles</th>
                                    <th class="border border-gray-300 p-2 text-sm font-semibold">Jueves</th>
                                    <th class="border border-gray-300 p-2 text-sm font-semibold">Viernes</th>
                                </tr>
                            </thead>
                            <tbody id="visual-schedule-body">
                                @for($hour = 7; $hour <= 16; $hour++)
                                    <tr>
                                        <td class="border border-gray-300 bg-gray-50 p-2 text-center text-xs font-medium">
                                            {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                                        </td>
                                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                            <td class="visual-cell relative h-16 border border-gray-300 bg-white p-1"
                                                data-day="{{ $day }}"
                                                data-time="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Vista de Lista -->
            <div id="list-view" class="hidden overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Materia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado/Grupo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Día</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Horario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aula</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Maestro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule->subject->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->schoolGrade->name }} - {{ $schedule->schoolGrade->section }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        @php
                                            $days = [
                                                'monday' => 'Lunes',
                                                'tuesday' => 'Martes',
                                                'wednesday' => 'Miércoles',
                                                'thursday' => 'Jueves',
                                                'friday' => 'Viernes'
                                            ];
                                        @endphp
                                        {{ $days[$schedule->day_of_week->value] ?? $schedule->day_of_week->value }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->classroom }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $schedule->subject->teacher->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este horario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay horarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $schedules->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allSchedules = [];
        const colors = [
            'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-orange-500',
            'bg-red-500', 'bg-indigo-500', 'bg-teal-500', 'bg-cyan-500', 'bg-amber-500'
        ];
        const subjectColors = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadSchoolGrades();
            loadAllSchedules();
            setupFilters();
        });

        function switchTab(tab) {
            if (tab === 'visual') {
                document.getElementById('visual-view').classList.remove('hidden');
                document.getElementById('list-view').classList.add('hidden');
                document.getElementById('tab-visual').classList.add('border-blue-600', 'text-blue-600');
                document.getElementById('tab-visual').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-list').classList.remove('border-blue-600', 'text-blue-600');
                document.getElementById('tab-list').classList.add('border-transparent', 'text-gray-500');
            } else {
                document.getElementById('visual-view').classList.add('hidden');
                document.getElementById('list-view').classList.remove('hidden');
                document.getElementById('tab-list').classList.add('border-blue-600', 'text-blue-600');
                document.getElementById('tab-list').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-visual').classList.remove('border-blue-600', 'text-blue-600');
                document.getElementById('tab-visual').classList.add('border-transparent', 'text-gray-500');
            }
        }

        function setupFilters() {
            document.getElementById('filter_school_year').addEventListener('change', function() {
                loadSchoolGrades();
                loadAllSchedules();
            });
            document.getElementById('filter_school_grade').addEventListener('change', applyFilters);
        }

        function loadSchoolGrades() {
            const schoolYearId = document.getElementById('filter_school_year').value;
            const schoolGradeSelect = document.getElementById('filter_school_grade');

            // Clear current options except the first one
            schoolGradeSelect.innerHTML = '<option value="">Todos los grados escolares</option>';

            if (!schoolYearId) {
                return;
            }

            fetch(`/api/school-years/${schoolYearId}/school-grades`)
                .then(response => response.json())
                .then(grades => {
                    grades.forEach(grade => {
                        const option = document.createElement('option');
                        option.value = grade.id;
                        option.textContent = `${grade.name} - ${grade.section}`;
                        schoolGradeSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading school grades:', error);
                });
        }

        function loadAllSchedules() {
            const schoolYearId = document.getElementById('filter_school_year').value;
            const schoolGradeId = document.getElementById('filter_school_grade').value;

            let url = `{{ route('admin.schedules.get-group-schedule') }}?`;

            if (schoolGradeId) {
                url += `school_grade_id=${schoolGradeId}`;
            } else if (schoolYearId) {
                url += `school_year_id=${schoolYearId}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(schedules => {
                    allSchedules = schedules;
                    applyFilters();
                })
                .catch(error => {
                    console.error('Error loading schedules:', error);
                });
        }

        function applyFilters() {
            const schoolGradeId = document.getElementById('filter_school_grade').value;

            // If school grade changed, reload schedules
            if (schoolGradeId) {
                loadAllSchedules();
                return;
            }

            // Clear grid
            document.querySelectorAll('.visual-cell').forEach(cell => {
                cell.innerHTML = '';
            });

            // Use all schedules since filtering is done server-side
            let filteredSchedules = allSchedules;

            // Assign colors to subjects
            let colorIndex = 0;
            filteredSchedules.forEach(schedule => {
                if (!subjectColors[schedule.subject_id]) {
                    subjectColors[schedule.subject_id] = colors[colorIndex % colors.length];
                    colorIndex++;
                }
            });

            // Render schedules
            filteredSchedules.forEach(schedule => {
                renderScheduleInGrid(schedule);
            });
        }

        function renderScheduleInGrid(schedule) {
            const startHour = parseInt(schedule.start_time.split(':')[0]);
            const startMinute = parseInt(schedule.start_time.split(':')[1]);
            const endHour = parseInt(schedule.end_time.split(':')[0]);
            const endMinute = parseInt(schedule.end_time.split(':')[1]);

            const durationHours = ((endHour * 60 + endMinute) - (startHour * 60 + startMinute)) / 60;
            const startTimeStr = `${String(startHour).padStart(2, '0')}:00`;

            const cell = document.querySelector(`.visual-cell[data-day="${schedule.day_of_week}"][data-time="${startTimeStr}"]`);
            if (!cell) return;

            const color = subjectColors[schedule.subject_id] || 'bg-blue-500';

            const scheduleDiv = document.createElement('div');
            scheduleDiv.className = `absolute inset-x-1 top-1 rounded ${color} p-2 text-white shadow-md overflow-hidden border-2 border-white`;
            scheduleDiv.style.height = `calc(${durationHours * 4}rem - 0.5rem)`;
            scheduleDiv.innerHTML = `
                <div class="text-xs font-semibold truncate">${schedule.subject.name}</div>
                <div class="text-xs opacity-90 truncate">${schedule.subject.teacher ? schedule.subject.teacher.name : ''}</div>
                <div class="text-xs opacity-75">${schedule.start_time.substring(0, 5)} - ${schedule.end_time.substring(0, 5)}</div>
                <div class="text-xs opacity-75">${schedule.school_grade.name} - ${schedule.school_grade.section}</div>
                ${schedule.classroom ? `<div class="text-xs opacity-75">Aula: ${schedule.classroom}</div>` : ''}
            `;

            cell.style.position = 'relative';
            cell.appendChild(scheduleDiv);
        }
    </script>
</x-app-layout>
