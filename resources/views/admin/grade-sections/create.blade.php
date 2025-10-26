<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Sección de Grado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.grade-sections.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grado</label>
                            <select name="grade_level" id="grade_level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar grado</option>
                                @foreach($gradeLevels as $level)
                                    <option value="{{ $level }}" {{ old('grade_level') == $level ? 'selected' : '' }}>
                                        {{ $level }}° Grado
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="section" class="block text-sm font-medium text-gray-700">Sección</label>
                            <select name="section" id="section" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar sección</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}" {{ old('section') == $sec ? 'selected' : '' }}>
                                        Sección {{ $sec }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select name="school_year_id" id="school_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar ciclo escolar</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" {{ old('school_year_id', $activeSchoolYear?->id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.grade-sections.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Crear Sección
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
