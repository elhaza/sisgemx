<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Colegiatura de Estudiante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finance.student-tuitions.update', $studentTuition) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Estudiante</label>
                            <input type="text" value="{{ $studentTuition->student->user->name }}" disabled
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <input type="text" value="{{ $studentTuition->schoolYear->name }}" disabled
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label for="monthly_amount" class="block text-sm font-medium text-gray-700">Monto Mensual</label>
                            <input type="number" step="0.01" name="monthly_amount" id="monthly_amount" value="{{ old('monthly_amount', $studentTuition->monthly_amount) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('monthly_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $studentTuition->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('finance.student-tuitions.index', ['school_year_id' => $studentTuition->school_year_id]) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Colegiatura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
