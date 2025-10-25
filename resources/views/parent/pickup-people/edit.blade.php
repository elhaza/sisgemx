<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Persona Autorizada - {{ $pickupPerson->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('parent.pickup-people.update', [$student, $pickupPerson]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $pickupPerson->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="relationship" class="block text-sm font-medium text-gray-700">Parentesco *</label>
                            <select name="relationship" id="relationship" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                @foreach($relationships as $relationship)
                                    <option value="{{ $relationship->value }}" {{ old('relationship', $pickupPerson->relationship->value) == $relationship->value ? 'selected' : '' }}>
                                        {{ $relationship->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('relationship')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="face_photo" class="block text-sm font-medium text-gray-700">Foto de Rostro</label>
                            @if($pickupPerson->face_photo)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($pickupPerson->face_photo) }}" alt="{{ $pickupPerson->name }}" class="h-24 w-24 rounded-full object-cover">
                                    <p class="mt-1 text-xs text-gray-500">Foto actual. Sube una nueva si deseas cambiarla.</p>
                                </div>
                            @endif
                            <input type="file" name="face_photo" id="face_photo" accept="image/jpeg,image/jpg,image/png"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Formato: JPG, JPEG o PNG. Máximo 2MB.</p>
                            @error('face_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="id_photo" class="block text-sm font-medium text-gray-700">Foto de Identificación (Opcional)</label>
                            @if($pickupPerson->id_photo)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($pickupPerson->id_photo) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                        Ver identificación actual
                                    </a>
                                    <p class="mt-1 text-xs text-gray-500">Sube una nueva si deseas cambiarla.</p>
                                </div>
                            @endif
                            <input type="file" name="id_photo" id="id_photo" accept="image/jpeg,image/jpg,image/png,application/pdf"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Formato: JPG, JPEG, PNG o PDF. Máximo 2MB.</p>
                            @error('id_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $pickupPerson->notes) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Información adicional sobre esta persona (opcional).</p>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('parent.pickup-people.index', $student) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
