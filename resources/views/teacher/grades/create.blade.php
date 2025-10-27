<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                ➕ Registrar Calificaciones
            </h2>
            <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Ver Calificaciones
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <!-- Selector de Modo -->
            <div class="mb-6 grid gap-4 md:grid-cols-2">
                <!-- Modo Individual -->
                <div class="overflow-hidden rounded-lg border-2 border-blue-200 bg-blue-50 shadow-sm">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">📝 Calificación Individual</h3>
                        <p class="mt-2 text-sm text-gray-600">Registra una calificación para un estudiante específico</p>
                        <button type="button" id="btnIndividual" class="mt-4 inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Usar este modo
                        </button>
                    </div>
                </div>

                <!-- Modo Masivo -->
                <div class="overflow-hidden rounded-lg border-2 border-green-200 bg-green-50 shadow-sm">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">📊 Calificación en Lote</h3>
                        <p class="mt-2 text-sm text-gray-600">Asigna calificaciones a todos los estudiantes de un grupo a la vez</p>
                        <select id="subjectSelect" class="mt-4 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                            <option value="">Selecciona una materia...</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->gradeSection?->name ?? $subject->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Formulario Individual -->
            <div id="individualForm" class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Formulario de Calificación Individual</h3>
                </div>

                <div class="p-6">
                    <form action="{{ route('teacher.grades.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Materia (debe seleccionarse primero) -->
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">
                                    Materia <span class="text-red-600">*</span>
                                </label>
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('subject_id') border-red-500 @enderror">
                                    <option value="">Seleccionar materia...</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                            {{ $subject->name }} ({{ $subject->gradeSection?->name ?? $subject->grade_level }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estudiante (se carga dinámicamente según la materia) -->
                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700">
                                    Estudiante <span class="text-red-600">*</span>
                                </label>
                                <select id="student_id" name="student_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('student_id') border-red-500 @enderror" @if(!old('subject_id')) disabled @endif>
                                    <option value="">Seleccionar materia primero...</option>
                                </select>
                                @error('student_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Los estudiantes se mostrarán después de seleccionar una materia</p>
                            </div>

                            <!-- Período -->
                            <div>
                                <label for="period" class="block text-sm font-medium text-gray-700">
                                    Período <span class="text-red-600">*</span>
                                </label>
                                <select id="period" name="period" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('period') border-red-500 @enderror">
                                    <option value="">Seleccionar período...</option>
                                    <option value="Primer Trimestre" @selected(old('period') == 'Primer Trimestre')>Primer Trimestre</option>
                                    <option value="Segundo Trimestre" @selected(old('period') == 'Segundo Trimestre')>Segundo Trimestre</option>
                                    <option value="Tercer Trimestre" @selected(old('period') == 'Tercer Trimestre')>Tercer Trimestre</option>
                                    <option value="Cuarto Trimestre" @selected(old('period') == 'Cuarto Trimestre')>Cuarto Trimestre</option>
                                    <option value="Primer Semestre" @selected(old('period') == 'Primer Semestre')>Primer Semestre</option>
                                    <option value="Segundo Semestre" @selected(old('period') == 'Segundo Semestre')>Segundo Semestre</option>
                                    <option value="Anual" @selected(old('period') == 'Anual')>Anual</option>
                                </select>
                                @error('period')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Calificación -->
                            <div>
                                <label for="grade" class="block text-sm font-medium text-gray-700">
                                    Calificación (0-100) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" id="grade" name="grade" value="{{ old('grade') }}" required placeholder="85" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('grade') border-red-500 @enderror">
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">
                                    80-100: Excelente | 60-79: Bueno | Menor a 60: Bajo
                                </p>
                            </div>

                            <!-- Comentarios -->
                            <div>
                                <label for="comments" class="block text-sm font-medium text-gray-700">
                                    Comentarios (Opcional)
                                </label>
                                <textarea id="comments" name="comments" rows="4" placeholder="Observaciones sobre el desempeño del estudiante..." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('comments') border-red-500 @enderror">{{ old('comments') }}</textarea>
                                @error('comments')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 pt-4">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Registrar Calificación
                                </button>
                                <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnIndividual = document.getElementById('btnIndividual');
            const subjectSelect = document.getElementById('subjectSelect');
            const subjectSelectForm = document.getElementById('subject_id');
            const studentSelect = document.getElementById('student_id');
            const individualForm = document.getElementById('individualForm');

            // Botón para modo individual
            btnIndividual.addEventListener('click', function() {
                individualForm.scrollIntoView({ behavior: 'smooth' });
                setTimeout(() => {
                    subjectSelectForm.focus();
                }, 300);
            });

            // Selector de materia para modo masivo
            subjectSelect.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = `/teacher/grades/bulk/${this.value}`;
                }
            });

            // Cargar estudiantes cuando se selecciona una materia (modo individual)
            subjectSelectForm.addEventListener('change', async function() {
                const subjectId = this.value;

                // Limpiar select de estudiantes
                studentSelect.innerHTML = '<option value="">Cargando estudiantes...</option>';
                studentSelect.disabled = true;

                if (!subjectId) {
                    studentSelect.innerHTML = '<option value="">Seleccionar materia primero...</option>';
                    studentSelect.disabled = true;
                    return;
                }

                try {
                    const response = await fetch(`/teacher/grades/api/students/${subjectId}`);

                    if (!response.ok) {
                        throw new Error('Error al cargar estudiantes');
                    }

                    const students = await response.json();

                    // Reconstruir el select con los estudiantes
                    studentSelect.innerHTML = '<option value="">Seleccionar estudiante...</option>';

                    if (students.length === 0) {
                        studentSelect.innerHTML = '<option value="">No hay estudiantes en este grupo</option>';
                        studentSelect.disabled = true;
                        return;
                    }

                    students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = `${student.full_name} (${student.grade_level})`;
                        studentSelect.appendChild(option);
                    });

                    studentSelect.disabled = false;

                    // Si hay un valor guardado en old(), intentar seleccionarlo
                    const oldStudentId = '{{ old("student_id") }}';
                    if (oldStudentId) {
                        studentSelect.value = oldStudentId;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    studentSelect.innerHTML = '<option value="">Error al cargar estudiantes</option>';
                    studentSelect.disabled = true;
                }
            });

            // Si hay una materia pre-seleccionada (después de validación), cargar sus estudiantes
            if (subjectSelectForm.value) {
                subjectSelectForm.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
