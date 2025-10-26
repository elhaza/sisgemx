<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Registrar Competencia de Docente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Nueva Competencia de Docente
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Define el nivel de competencia del docente en cada materia. Esto se utiliza para priorizar asignaciones en la generación automática de horarios.
                    </p>
                </div>

                <form action="{{ route('admin.teacher-subjects.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Docente -->
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700">
                                Docente *
                            </label>
                            <select name="teacher_id" id="teacher_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar docente</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Materia -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">
                                Materia *
                            </label>
                            <select name="subject_id" id="subject_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar materia</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nivel de Competencia -->
                        <div>
                            <label for="proficiency" class="block text-sm font-medium text-gray-700">
                                Nivel de Competencia *
                            </label>
                            <div class="mt-2 space-y-3">
                                <input type="range" name="proficiency" id="proficiency" min="1" max="10"
                                    value="{{ old('proficiency', 5) }}"
                                    class="w-full cursor-pointer"
                                    oninput="updateProficiencyLabel(this.value)">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm text-gray-500">Bajo</span>
                                    </div>
                                    <div class="text-center">
                                        <span id="proficiencyLabel" class="text-lg font-bold text-blue-600">5/10</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Alto</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                1 = Principiante, 5 = Intermedio, 10 = Experto
                            </p>
                            @error('proficiency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Registrar Competencia
                            </button>
                            <a href="{{ route('admin.teacher-subjects.index') }}"
                                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateProficiencyLabel(value) {
            document.getElementById('proficiencyLabel').textContent = value + '/10';
        }
    </script>
</x-app-layout>
