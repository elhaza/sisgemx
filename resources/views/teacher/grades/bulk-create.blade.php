<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📊 Asignar Calificaciones - {{ $subject->name }} ({{ $subject->grade_level }})
            </h2>
            <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Selector de Período -->
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Seleccionar Período</h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-4 md:grid-cols-4">
                        <button type="button" data-period="Primer Trimestre" class="period-btn rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:border-blue-500 hover:bg-blue-50">
                            📅 Primer Trimestre
                        </button>
                        <button type="button" data-period="Segundo Trimestre" class="period-btn rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:border-blue-500 hover:bg-blue-50">
                            📅 Segundo Trimestre
                        </button>
                        <button type="button" data-period="Tercer Trimestre" class="period-btn rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:border-blue-500 hover:bg-blue-50">
                            📅 Tercer Trimestre
                        </button>
                        <button type="button" data-period="Cuarto Trimestre" class="period-btn rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:border-blue-500 hover:bg-blue-50">
                            📅 Cuarto Trimestre
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Calificaciones -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Estudiantes de {{ $subject->grade_level }}
                        <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                            {{ $students->count() }} alumnos
                        </span>
                    </h3>
                </div>

                <form id="gradesForm" action="{{ route('teacher.grades.bulk.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <input type="hidden" id="periodInput" name="period" value="">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Estudiante
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Calificación Anterior
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Nueva Calificación
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Comentarios
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($students as $item)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                                    <span class="text-sm font-medium text-blue-700">
                                                        {{ substr($item['student']->user->full_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $item['student']->user->full_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        ID: {{ $item['student']->id }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($item['grade'])
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                                    @if ($item['grade']->grade >= 80)
                                                        bg-green-100 text-green-800
                                                    @elseif ($item['grade']->grade >= 60)
                                                        bg-yellow-100 text-yellow-800
                                                    @else
                                                        bg-red-100 text-red-800
                                                    @endif
                                                ">
                                                    {{ $item['grade']->grade }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Sin calificación</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <input
                                                type="number"
                                                name="grades[{{ $item['student']->id }}][student_id]"
                                                value="{{ $item['student']->id }}"
                                                class="hidden"
                                            >
                                            <input
                                                type="number"
                                                name="grades[{{ $item['student']->id }}][grade]"
                                                value="{{ $item['grade']?->grade }}"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                placeholder="0-100"
                                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            >
                                        </td>
                                        <td class="px-6 py-4">
                                            <input
                                                type="text"
                                                name="grades[{{ $item['student']->id }}][comments]"
                                                value="{{ $item['grade']?->comments }}"
                                                placeholder="Comentarios (opcional)"
                                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div id="periodSelected" class="text-sm font-medium text-gray-700">
                                ⚠️ Selecciona un período para continuar
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </a>
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    disabled
                                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Guardar Calificaciones
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Leyenda de Colores -->
            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Escala de Calificaciones</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="flex items-center">
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                            80-100
                        </span>
                        <span class="ml-2 text-sm text-gray-600">Excelente</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                            60-79
                        </span>
                        <span class="ml-2 text-sm text-gray-600">Bueno</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                            &lt;60
                        </span>
                        <span class="ml-2 text-sm text-gray-600">Bajo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodBtns = document.querySelectorAll('.period-btn');
            const periodInput = document.getElementById('periodInput');
            const periodSelected = document.getElementById('periodSelected');
            const submitBtn = document.getElementById('submitBtn');

            periodBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover estado activo de todos
                    periodBtns.forEach(b => {
                        b.classList.remove('border-blue-500', 'bg-blue-50', 'border-2');
                        b.classList.add('border-gray-200', 'bg-white', 'border-2');
                    });

                    // Añadir estado activo al actual
                    this.classList.remove('border-gray-200', 'bg-white');
                    this.classList.add('border-blue-500', 'bg-blue-50');

                    // Guardar el período
                    const period = this.getAttribute('data-period');
                    periodInput.value = period;

                    // Actualizar UI
                    periodSelected.textContent = `✓ Período seleccionado: ${period}`;
                    periodSelected.classList.remove('text-yellow-700');
                    periodSelected.classList.add('text-green-700');

                    // Habilitar botón de envío
                    submitBtn.disabled = false;
                });
            });

            // Validar antes de enviar
            document.getElementById('gradesForm').addEventListener('submit', function(e) {
                if (!periodInput.value) {
                    e.preventDefault();
                    alert('Por favor, selecciona un período');
                    return false;
                }

                const gradesInputs = document.querySelectorAll('input[name^="grades"]');
                let hasGrades = false;

                gradesInputs.forEach(input => {
                    if (input.name.includes('[grade]') && input.value) {
                        hasGrades = true;
                    }
                });

                if (!hasGrades) {
                    e.preventDefault();
                    alert('Por favor, ingresa al menos una calificación');
                    return false;
                }
            });
        });
    </script>
</x-app-layout>
