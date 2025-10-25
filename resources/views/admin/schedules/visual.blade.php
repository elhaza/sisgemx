<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Creador Visual de Horarios
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.schedules.copy-form') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copiar de Ciclo Anterior
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Ver Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
            <!-- Selectores -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 grid gap-6 md:grid-cols-3">
                    <div>
                        <label for="school_grade_id" class="block text-sm font-medium text-gray-700">Grado Escolar</label>
                        <select id="school_grade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione un grado</option>
                            @foreach($schoolGrades as $schoolGrade)
                                <option value="{{ $schoolGrade->id }}">
                                    {{ $schoolGrade->name }} - {{ $schoolGrade->section }} ({{ $schoolGrade->schoolYear->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="time_interval" class="block text-sm font-medium text-gray-700">Intervalo de Tiempo</label>
                        <select id="time_interval" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="30">30 minutos</option>
                            <option value="40">40 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="50">50 minutos</option>
                            <option value="60" selected>1 hora (60 min)</option>
                            <option value="120">2 horas (120 min)</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <button id="save-schedule" class="inline-flex items-center rounded-md bg-green-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Horario Completo
                    </button>
                </div>
            </div>

            <div class="flex gap-6">
                <!-- Panel de Materias -->
                <div class="w-1/4">
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Materias Disponibles</h3>
                        <div id="subjects-list" class="space-y-2">
                            @php
                                $colors = [
                                    ['bg' => 'bg-blue-100', 'border' => 'border-blue-400', 'hover' => 'hover:bg-blue-200', 'text' => 'text-blue-900', 'schedule' => 'bg-blue-500'],
                                    ['bg' => 'bg-green-100', 'border' => 'border-green-400', 'hover' => 'hover:bg-green-200', 'text' => 'text-green-900', 'schedule' => 'bg-green-500'],
                                    ['bg' => 'bg-purple-100', 'border' => 'border-purple-400', 'hover' => 'hover:bg-purple-200', 'text' => 'text-purple-900', 'schedule' => 'bg-purple-500'],
                                    ['bg' => 'bg-pink-100', 'border' => 'border-pink-400', 'hover' => 'hover:bg-pink-200', 'text' => 'text-pink-900', 'schedule' => 'bg-pink-500'],
                                    ['bg' => 'bg-orange-100', 'border' => 'border-orange-400', 'hover' => 'hover:bg-orange-200', 'text' => 'text-orange-900', 'schedule' => 'bg-orange-500'],
                                    ['bg' => 'bg-red-100', 'border' => 'border-red-400', 'hover' => 'hover:bg-red-200', 'text' => 'text-red-900', 'schedule' => 'bg-red-500'],
                                    ['bg' => 'bg-indigo-100', 'border' => 'border-indigo-400', 'hover' => 'hover:bg-indigo-200', 'text' => 'text-indigo-900', 'schedule' => 'bg-indigo-500'],
                                    ['bg' => 'bg-teal-100', 'border' => 'border-teal-400', 'hover' => 'hover:bg-teal-200', 'text' => 'text-teal-900', 'schedule' => 'bg-teal-500'],
                                    ['bg' => 'bg-cyan-100', 'border' => 'border-cyan-400', 'hover' => 'hover:bg-cyan-200', 'text' => 'text-cyan-900', 'schedule' => 'bg-cyan-500'],
                                    ['bg' => 'bg-amber-100', 'border' => 'border-amber-400', 'hover' => 'hover:bg-amber-200', 'text' => 'text-amber-900', 'schedule' => 'bg-amber-500'],
                                ];
                            @endphp
                            @foreach($groupedSubjects as $groupIndex => $group)
                                @php
                                    $color = $colors[$groupIndex % count($colors)];
                                @endphp
                                <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <!-- Materia (Header) -->
                                    <button type="button" class="toggle-subject w-full text-left" data-subject-name="{{ $group['name'] }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $group['name'] }}</h4>
                                                <p class="text-xs text-gray-500">{{ count($group['teachers']) }} maestro(s)</p>
                                            </div>
                                            <svg class="toggle-icon h-5 w-5 transform transition-transform text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- Maestros (Expandible) -->
                                    <div class="teachers-list mt-3 hidden space-y-2">
                                        @foreach($group['teachers'] as $teacher)
                                            <div class="teacher-item cursor-move rounded-lg border-2 border-dashed {{ $color['border'] }} {{ $color['bg'] }} p-2 {{ $color['hover'] }} transition"
                                                 draggable="true"
                                                 data-subject-id="{{ $teacher['subject_id'] }}"
                                                 data-subject-name="{{ $group['name'] }}"
                                                 data-teacher-id="{{ $teacher['id'] }}"
                                                 data-teacher-name="{{ $teacher['name'] }}"
                                                 data-subject-color="{{ $color['schedule'] }}">
                                                <div class="font-medium {{ $color['text'] }} text-sm">{{ $teacher['name'] }}</div>
                                                <div class="text-xs {{ $color['text'] }} opacity-75">
                                                    📚 {{ $teacher['hours_assigned'] }} hora(s)
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="mt-4 rounded-lg bg-white p-4 shadow-sm">
                        <h4 class="mb-2 text-sm font-semibold text-gray-900">Instrucciones</h4>
                        <ul class="space-y-1 text-xs text-gray-600">
                            <li>• Arrastra materias al horario</li>
                            <li>• Redimensiona arrastrando el borde inferior</li>
                            <li>• Clic derecho para eliminar</li>
                            <li>• Selecciona un grado escolar para empezar</li>
                        </ul>
                    </div>
                </div>

                <!-- Calendario/Horario -->
                <div class="flex-1">
                    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                        <div id="schedule-grid" class="min-w-max">
                            <table id="schedule-table" class="w-full border-collapse">
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
                                <tbody id="schedule-body">
                                    <!-- Generated dynamically by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Show Students Button -->
                        <div class="mt-6 flex justify-center">
                            <button
                                id="show-students-btn"
                                type="button"
                                disabled
                                class="inline-flex items-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6M9 20H3" />
                                </svg>
                                Mostrar alumnos de este grupo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Alumnos -->
        <div id="students-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Alumnos del Grupo</h3>
                    <button type="button" onclick="closeStudentsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="students-list" class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <!-- Students will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSchedules = [];
        let draggedSubject = null;
        let draggedScheduleItem = null;
        let selectedSchoolGradeId = '';
        let timeInterval = 60; // Default 60 minutes
        let subjectColorMap = {}; // Map subject_id to color class

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            generateScheduleGrid();
            setupSelectors();
            setupSaveButton();
            setupToggleSubjects();
            setupDragAndDrop();
            buildSubjectColorMap();
        });

        function buildSubjectColorMap() {
            // Get all teacher items and map their subject_id to color
            document.querySelectorAll('.teacher-item').forEach(item => {
                const subjectId = item.dataset.subjectId;
                const color = item.dataset.subjectColor;
                if (subjectId && color) {
                    subjectColorMap[subjectId] = color;
                }
            });
        }

        function setupToggleSubjects() {
            document.querySelectorAll('.toggle-subject').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('div');
                    const teachersList = container.querySelector('.teachers-list');
                    const icon = this.querySelector('.toggle-icon');

                    if (!teachersList) return;

                    if (teachersList.classList.contains('hidden')) {
                        teachersList.classList.remove('hidden');
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        teachersList.classList.add('hidden');
                        icon.style.transform = 'rotate(0deg)';
                    }
                });
            });
        }

        function setupSelectors() {
            document.getElementById('school_grade_id').addEventListener('change', function() {
                selectedSchoolGradeId = this.value;
                loadSchedule();

                // Enable/disable students button
                const showStudentsBtn = document.getElementById('show-students-btn');
                if (selectedSchoolGradeId) {
                    showStudentsBtn.disabled = false;
                    showStudentsBtn.addEventListener('click', showStudentsModal);
                } else {
                    showStudentsBtn.disabled = true;
                }
            });

            document.getElementById('time_interval').addEventListener('change', function() {
                timeInterval = parseInt(this.value);
                generateScheduleGrid();
                loadSchedule();
            });
        }

        function generateScheduleGrid() {
            const tbody = document.getElementById('schedule-body');
            tbody.innerHTML = '';

            const startHour = 7;
            const endHour = 17;
            const intervals = 60 / timeInterval;

            for (let hour = startHour; hour < endHour; hour++) {
                for (let interval = 0; interval < intervals; interval++) {
                    const minute = interval * timeInterval;
                    const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;

                    const tr = document.createElement('tr');
                    tr.dataset.time = timeStr;

                    // Time column
                    const timeTd = document.createElement('td');
                    timeTd.className = 'border border-gray-300 bg-gray-50 p-2 text-center text-xs font-medium';
                    timeTd.textContent = timeStr;
                    tr.appendChild(timeTd);

                    // Day columns
                    ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(day => {
                        const td = document.createElement('td');
                        td.className = 'schedule-cell relative h-16 border border-gray-300 bg-white p-1';
                        td.dataset.day = day;
                        td.dataset.time = timeStr;
                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                }
            }

            setupDragAndDrop();
        }

        function setupSaveButton() {
            document.getElementById('save-schedule').addEventListener('click', function() {
                if (!selectedSchoolGradeId) {
                    alert('Por favor seleccione un grado escolar');
                    return;
                }

                const gradeSelect = document.getElementById('school_grade_id');
                const selectedText = gradeSelect.options[gradeSelect.selectedIndex].text;

                if (confirm(`¿Confirmar el horario para ${selectedText}?`)) {
                    showNotification('Horario guardado exitosamente. Todas las clases están registradas.', 'success');
                }
            });
        }

        function loadSchedule() {
            if (!selectedSchoolGradeId) {
                clearScheduleGrid();
                return;
            }

            fetch(`{{ route('admin.schedules.get-group-schedule') }}?school_grade_id=${selectedSchoolGradeId}`)
                .then(response => response.json())
                .then(schedules => {
                    currentSchedules = schedules;
                    renderSchedules(schedules);
                    setupDragAndDrop();
                    buildSubjectColorMap();
                })
                .catch(error => {
                    console.error('Error loading schedule:', error);
                    showNotification('Error al cargar el horario', 'error');
                });
        }

        function clearScheduleGrid() {
            document.querySelectorAll('.schedule-cell').forEach(cell => {
                cell.innerHTML = '';
            });
        }

        function renderSchedules(schedules) {
            clearScheduleGrid();

            schedules.forEach(schedule => {
                renderScheduleItem(schedule);
            });
        }

        function renderScheduleItem(schedule) {
            const startParts = schedule.start_time.split(':');
            const startHour = parseInt(startParts[0]);
            const startMinute = parseInt(startParts[1]);
            const endParts = schedule.end_time.split(':');
            const endHour = parseInt(endParts[0]);
            const endMinute = parseInt(endParts[1]);

            const durationMinutes = ((endHour * 60 + endMinute) - (startHour * 60 + startMinute));
            const cellsToSpan = Math.ceil(durationMinutes / timeInterval);

            const startTimeStr = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
            const cell = document.querySelector(`.schedule-cell[data-day="${schedule.day_of_week}"][data-time="${startTimeStr}"]`);

            if (!cell) return;

            // Get color from color map, or use default
            const subjectColor = subjectColorMap[schedule.subject_id] || 'bg-blue-500';

            const scheduleDiv = document.createElement('div');
            scheduleDiv.className = `schedule-item absolute inset-x-1 top-1 rounded ${subjectColor} p-2 text-white shadow-lg cursor-move hover:opacity-90 transition overflow-hidden border-2 border-white`;
            scheduleDiv.draggable = true;
            scheduleDiv.style.height = `calc(${cellsToSpan * 4}rem - 0.5rem)`;
            scheduleDiv.style.zIndex = '10';
            scheduleDiv.dataset.scheduleId = schedule.id;
            scheduleDiv.dataset.subjectId = schedule.subject_id;
            scheduleDiv.dataset.duration = durationMinutes;
            scheduleDiv.dataset.classroom = schedule.classroom || '';
            scheduleDiv.innerHTML = `
                <div class="flex items-start justify-between mb-1">
                    <div class="text-xs font-semibold truncate flex-1">${schedule.subject.name}</div>
                    <button onclick="deleteScheduleItem(${schedule.id})" class="ml-1 text-white hover:text-red-200 focus:outline-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="text-xs opacity-90 truncate">${schedule.subject.teacher ? schedule.subject.teacher.name : 'Sin maestro'}</div>
                <div class="text-xs opacity-75">${schedule.start_time.substring(0, 5)} - ${schedule.end_time.substring(0, 5)}</div>
                ${schedule.classroom ? `<div class="text-xs opacity-75">Aula: ${schedule.classroom}</div>` : ''}
            `;

            // Make schedule item draggable
            scheduleDiv.ondragstart = function(e) {
                e.stopPropagation();
                draggedScheduleItem = {
                    scheduleId: this.dataset.scheduleId,
                    subjectId: this.dataset.subjectId,
                    duration: parseInt(this.dataset.duration),
                    classroom: this.dataset.classroom,
                    element: this
                };
                this.style.opacity = '0.5';
                e.dataTransfer.effectAllowed = 'move';
            };

            scheduleDiv.ondragend = function(e) {
                this.style.opacity = '1';
            };

            cell.style.position = 'relative';
            cell.appendChild(scheduleDiv);
        }

        // Global function for delete button
        window.deleteScheduleItem = function(scheduleId) {
            if (confirm('¿Eliminar esta clase del horario?')) {
                deleteSchedule(scheduleId);
            }
        };

        function setupDragAndDrop() {
            // Teacher items dragging
            const teacherItems = document.querySelectorAll('.teacher-item');
            teacherItems.forEach(item => {
                item.ondragstart = function(e) {
                    draggedSubject = {
                        id: this.dataset.subjectId,
                        name: this.dataset.subjectName,
                        teacherId: this.dataset.teacherId,
                        teacherName: this.dataset.teacherName,
                        color: this.dataset.subjectColor
                    };
                    e.dataTransfer.effectAllowed = 'copy';
                    this.style.opacity = '0.5';
                };

                item.ondragend = function(e) {
                    this.style.opacity = '1';
                };
            });

            // Schedule cells dropping
            const scheduleCells = document.querySelectorAll('.schedule-cell');
            scheduleCells.forEach(cell => {
                cell.ondragover = function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'copy';
                    this.classList.add('bg-blue-100');
                    return false;
                };

                cell.ondragleave = function(e) {
                    this.classList.remove('bg-blue-100');
                };

                cell.ondrop = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('bg-blue-100');

                    if (!selectedSchoolGradeId) {
                        showNotification('Por favor seleccione un grado escolar primero', 'error');
                        return false;
                    }

                    const day = this.dataset.day;
                    const time = this.dataset.time;

                    // Check if we're moving an existing schedule item
                    if (draggedScheduleItem) {
                        // Moving existing schedule
                        const endTime = calculateEndTimeFromDuration(time, draggedScheduleItem.duration);
                        moveSchedule(draggedScheduleItem.scheduleId, day, time, endTime, draggedScheduleItem.classroom);
                        draggedScheduleItem = null;
                        return false;
                    }

                    // Creating new schedule from subject
                    if (!draggedSubject) return false;

                    // Prompt for duration and classroom
                    const durationOptions = {
                        '30': '30 minutos',
                        '40': '40 minutos',
                        '45': '45 minutos',
                        '50': '50 minutos',
                        '60': '1 hora',
                        '120': '2 horas',
                        '180': '3 horas',
                        '240': '4 horas',
                        '300': '5 horas',
                        '360': '6 horas'
                    };

                    let durationSelect = '<select id="duration-select" class="w-full p-2 border rounded">';
                    for (let [minutes, label] of Object.entries(durationOptions)) {
                        const isSelected = parseInt(minutes) === timeInterval ? 'selected' : '';
                        durationSelect += `<option value="${minutes}" ${isSelected}>${label}</option>`;
                    }
                    durationSelect += '</select>';

                    const modalHtml = `
                        <div id="schedule-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-6 w-96">
                                <h3 class="text-lg font-semibold mb-4">Configurar Clase</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-2">Materia: ${draggedSubject.name}</label>
                                    <label class="block text-sm font-medium mb-2">Hora de inicio: ${time}</label>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-2">Duración:</label>
                                    ${durationSelect}
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-2">Aula (opcional):</label>
                                    <input type="text" id="classroom-input" class="w-full p-2 border rounded" placeholder="Ej: Aula 101">
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button id="cancel-btn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                                    <button id="confirm-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Crear</button>
                                </div>
                            </div>
                        </div>
                    `;

                    document.body.insertAdjacentHTML('beforeend', modalHtml);

                    document.getElementById('cancel-btn').onclick = function() {
                        document.getElementById('schedule-modal').remove();
                    };

                    document.getElementById('confirm-btn').onclick = function() {
                        const durationMinutes = parseInt(document.getElementById('duration-select').value);
                        const classroom = document.getElementById('classroom-input').value;
                        const endTime = calculateEndTimeFromDuration(time, durationMinutes);

                        document.getElementById('schedule-modal').remove();
                        createSchedule(draggedSubject.id, day, time, endTime, classroom, draggedSubject.teacherId || null);
                    };

                    return false;
                };
            });
        }

        function calculateEndTime(startTime) {
            const [hour, minute] = startTime.split(':').map(Number);
            const endMinute = minute + timeInterval;
            const endHour = hour + Math.floor(endMinute / 60);
            const finalMinute = endMinute % 60;
            return `${String(endHour).padStart(2, '0')}:${String(finalMinute).padStart(2, '0')}`;
        }

        function calculateEndTimeFromDuration(startTime, durationMinutes) {
            const [hour, minute] = startTime.split(':').map(Number);
            const totalMinutes = hour * 60 + minute + durationMinutes;
            const endHour = Math.floor(totalMinutes / 60);
            const endMinute = totalMinutes % 60;
            return `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;
        }

        function createSchedule(subjectId, day, startTime, endTime, classroom, teacherId = null) {
            const data = {
                school_grade_id: selectedSchoolGradeId,
                subject_id: subjectId,
                day_of_week: day,
                start_time: startTime,
                end_time: endTime,
                classroom: classroom,
                _token: '{{ csrf_token() }}'
            };

            // If teacherId is provided, override with that teacher
            if (teacherId) {
                data.teacher_id = teacherId;
            }

            fetch('{{ route("admin.schedules.store-visual") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Add color to map if we have it from draggedSubject
                    if (draggedSubject && draggedSubject.color) {
                        subjectColorMap[subjectId] = draggedSubject.color;
                    }
                    loadSchedule();
                    showNotification('Horario creado exitosamente', 'success');
                } else {
                    showNotification(result.message || 'Error al crear el horario', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al crear el horario', 'error');
            });
        }

        function moveSchedule(scheduleId, day, startTime, endTime, classroom) {
            const data = {
                day_of_week: day,
                start_time: startTime,
                end_time: endTime,
                classroom: classroom || '',
                _token: '{{ csrf_token() }}'
            };

            fetch(`{{ url('admin/schedules-visual') }}/${scheduleId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSchedule();
                    showNotification('Horario movido exitosamente', 'success');
                } else {
                    showNotification(result.message || 'Error al mover el horario', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al mover el horario', 'error');
            });
        }

        function deleteSchedule(scheduleId) {
            fetch(`{{ url('admin/schedules-visual') }}/${scheduleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSchedule();
                    showNotification('Horario eliminado exitosamente', 'success');
                } else {
                    showNotification(result.message || 'Error al eliminar el horario', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al eliminar el horario', 'error');
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

        async function showStudentsModal() {
            if (!selectedSchoolGradeId) {
                showNotification('Por favor selecciona un grado escolar', 'error');
                return;
            }

            try {
                const response = await fetch(`{{ route('admin.schedules.get-group-students') }}?school_grade_id=${selectedSchoolGradeId}`);
                const data = await response.json();

                if (!response.ok) {
                    showNotification('Error al cargar los alumnos', 'error');
                    return;
                }

                const studentsList = document.getElementById('students-list');
                const gradeSelect = document.getElementById('school_grade_id');
                const gradeName = gradeSelect.options[gradeSelect.selectedIndex].text;

                if (data.students && data.students.length > 0) {
                    studentsList.innerHTML = `
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 font-semibold mb-3">Grupo: ${gradeName}</p>
                            <div class="space-y-2">
                                ${data.students.map(student => `
                                    <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:bg-blue-50 transition">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">${student.full_name}</p>
                                            <p class="text-xs text-gray-500">${student.email}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                            Activo
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            Total: <strong>${data.students.length}</strong> alumno(s)
                        </div>
                    `;
                } else {
                    studentsList.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6M9 20H3" />
                            </svg>
                            <p class="mt-2 text-gray-600">No hay alumnos en este grupo</p>
                        </div>
                    `;
                }

                document.getElementById('students-modal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al cargar los alumnos', 'error');
            }
        }

        function closeStudentsModal() {
            document.getElementById('students-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('students-modal');
            if (modal && event.target === modal) {
                closeStudentsModal();
            }
        });
    </script>
</x-app-layout>
