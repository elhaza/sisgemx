<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Inscribir Estudiante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                            <select name="user_id" id="user_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Solo se muestran usuarios con rol de estudiante sin inscripción.</p>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="enrollment_number" class="block text-sm font-medium text-gray-700">Número de Matrícula</label>
                            <input type="text" name="enrollment_number" id="enrollment_number" value="{{ old('enrollment_number') }}" required
                                placeholder="Ej: EST-2024-001"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('enrollment_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                            <select name="school_year_id" id="school_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar ciclo escolar</option>
                                @foreach($schoolYears as $schoolYear)
                                    <option value="{{ $schoolYear->id }}" {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->name }} @if($schoolYear->is_active) (Activo) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_grade_id" class="block text-sm font-medium text-gray-700">Grado y Sección</label>
                            <select name="school_grade_id" id="school_grade_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar grado y sección</option>
                                @foreach($schoolGrades as $grade)
                                    <option value="{{ $grade->id }}" {{ old('school_grade_id') == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->schoolYear->name }} - {{ $grade->name }} - Sección {{ $grade->section }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_grade_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Información Personal</h3>

                            <div class="mb-4">
                                <label for="curp" class="block text-sm font-medium text-gray-700">CURP</label>
                                <input type="text" name="curp" id="curp" value="{{ old('curp') }}" maxlength="18"
                                    placeholder="Ej: AAAA990101HDFRRL00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">18 caracteres</p>
                                @error('curp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="gender" class="block text-sm font-medium text-gray-700">Sexo</label>
                                <select name="gender" id="gender"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Hombre</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Mujer</option>
                                    <option value="unspecified" {{ old('gender') == 'unspecified' ? 'selected' : '' }}>No especificar</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Lugar de Nacimiento</h3>

                            <div class="mb-4">
                                <label for="birth_country" class="block text-sm font-medium text-gray-700">País</label>
                                <input type="text" name="birth_country" id="birth_country" value="{{ old('birth_country', 'México') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('birth_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="birth_state" class="block text-sm font-medium text-gray-700">Estado</label>
                                    <input type="text" name="birth_state" id="birth_state" value="{{ old('birth_state') }}"
                                        placeholder="Ej: Jalisco"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('birth_state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="birth_city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                    <input type="text" name="birth_city" id="birth_city" value="{{ old('birth_city') }}"
                                        placeholder="Ej: Guadalajara"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('birth_city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Información de Contacto</h3>

                            <div class="mb-4">
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">Número Telefónico de Contacto</label>
                                <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                    placeholder="Ej: 3312345678"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">Domicilio</label>
                                <textarea name="address" id="address" rows="3"
                                    placeholder="Calle, número, colonia, código postal"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="parent_email" class="block text-sm font-medium text-gray-700">Correo de Padres</label>
                                <input type="email" name="parent_email" id="parent_email" value="{{ old('parent_email') }}"
                                    placeholder="correo@ejemplo.com"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('parent_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Inscribir Estudiante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
