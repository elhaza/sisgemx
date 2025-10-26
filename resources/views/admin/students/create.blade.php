<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Inscribir Estudiante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario (Estudiante) *</label>
                            <div class="mt-1 flex gap-2">
                                <select name="user_id" id="user_id" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar usuario</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('user_id') ?: (session('selected_field') === 'user_id' ? session('selected_user_id') : null)) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('admin.users.create', ['role' => 'student', 'return_to' => route('admin.students.create'), 'field' => 'user_id']) }}" class="flex items-center gap-1 whitespace-nowrap rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Solo se muestran usuarios con rol de estudiante sin inscripción.</p>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="enrollment_number" class="block text-sm font-medium text-gray-700">Número de Matrícula</label>
                            <input type="text" name="enrollment_number" id="enrollment_number" value="{{ old('enrollment_number') }}"
                                placeholder="Ej: PRI-2025-ESC001-001 (se genera automáticamente si se deja vacío)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Si no se proporciona, se generará automáticamente.</p>
                            @error('enrollment_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700">Ciclo Escolar *</label>
                            <select name="school_year_id" id="school_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar ciclo escolar</option>
                                @foreach($schoolYears as $schoolYear)
                                    <option value="{{ $schoolYear->id }}" {{ (old('school_year_id') ?? $activeSchoolYear?->id) == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->name }} @if($schoolYear->is_active) (Activo) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_grade_id" class="block text-sm font-medium text-gray-700">Grado y Sección *</label>
                            <select name="school_grade_id" id="school_grade_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar grado y sección</option>
                                @foreach($schoolGrades as $grade)
                                    <option value="{{ $grade->id }}" {{ old('school_grade_id') == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }} - Sección {{ $grade->section }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Solo se muestran grados del ciclo escolar activo.</p>
                            @error('school_grade_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Padres/Tutores Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Padres/Tutores</h3>

                            <div class="mb-4">
                                <label for="tutor_1_id" class="block text-sm font-medium text-gray-700">Tutor 1 *</label>
                                <div class="mt-1 flex gap-2">
                                    <select name="tutor_1_id" id="tutor_1_id" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccionar tutor</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ (old('tutor_1_id') ?: (session('selected_field') === 'tutor_1_id' ? session('selected_user_id') : null)) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }} ({{ $parent->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('admin.users.create', ['role' => 'parent', 'return_to' => route('admin.students.create'), 'field' => 'tutor_1_id']) }}" class="flex items-center gap-1 whitespace-nowrap rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Agregar
                                    </a>
                                </div>
                                @error('tutor_1_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="tutor_2_id" class="block text-sm font-medium text-gray-700">Tutor 2 (Opcional)</label>
                                <div class="mt-1 flex gap-2">
                                    <select name="tutor_2_id" id="tutor_2_id"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccionar tutor</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ (old('tutor_2_id') ?: (session('selected_field') === 'tutor_2_id' ? session('selected_user_id') : null)) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }} ({{ $parent->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('admin.users.create', ['role' => 'parent', 'return_to' => route('admin.students.create'), 'field' => 'tutor_2_id']) }}" class="flex items-center gap-1 whitespace-nowrap rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Agregar
                                    </a>
                                </div>
                                @error('tutor_2_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Personal Information Section -->
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

                        <!-- Birth Place Section -->
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

                        <!-- Contact Information Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Información de Contacto</h3>

                            <div class="mb-4">
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">Whatsapp de Contacto</label>
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

                        <!-- Discount Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Descuento en Colegiatura</h3>

                            <div class="mb-4">
                                <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Descuento (%)</label>
                                <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', 0) }}"
                                    min="0" max="100" step="0.01"
                                    placeholder="0.00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Este descuento se aplicará a las colegiaturas mensuales del ciclo escolar seleccionado. Por defecto: 0%</p>
                                @error('discount_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Billing Section -->
                        <div class="mb-6 border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Facturación</h3>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="requires_invoice" id="requires_invoice" value="1" {{ old('requires_invoice') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        onchange="document.getElementById('billing_fields').style.display = this.checked ? 'block' : 'none'">
                                    <span class="ml-2 text-sm text-gray-700">¿Requiere factura?</span>
                                </label>
                            </div>

                            <div id="billing_fields" style="display: {{ old('requires_invoice') ? 'block' : 'none' }}">
                                <div class="mb-4">
                                    <label for="billing_name" class="block text-sm font-medium text-gray-700">Nombre, Denominación o Razón Social</label>
                                    <input type="text" name="billing_name" id="billing_name" value="{{ old('billing_name') }}"
                                        placeholder="Ej: Juan Pérez García"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('billing_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="billing_zip_code" class="block text-sm font-medium text-gray-700">Código Postal</label>
                                        <input type="text" name="billing_zip_code" id="billing_zip_code" value="{{ old('billing_zip_code') }}" maxlength="10"
                                            placeholder="Ej: 44100"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('billing_zip_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="billing_rfc" class="block text-sm font-medium text-gray-700">RFC</label>
                                        <input type="text" name="billing_rfc" id="billing_rfc" value="{{ old('billing_rfc') }}" maxlength="13"
                                            placeholder="Ej: XAXX010101000"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('billing_rfc')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="billing_tax_regime" class="block text-sm font-medium text-gray-700">Régimen Fiscal</label>
                                    <select name="billing_tax_regime" id="billing_tax_regime"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccionar régimen</option>
                                        @foreach(\App\RegimenFiscal::cases() as $regimen)
                                            <option value="{{ $regimen->value }}" {{ old('billing_tax_regime') == $regimen->value ? 'selected' : '' }}>
                                                {{ $regimen->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_tax_regime')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="billing_cfdi_use" class="block text-sm font-medium text-gray-700">Uso CFDI</label>
                                    <select name="billing_cfdi_use" id="billing_cfdi_use"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccionar uso</option>
                                        @foreach(\App\UsoCFDI::cases() as $uso)
                                            <option value="{{ $uso->value }}" {{ old('billing_cfdi_use') == $uso->value ? 'selected' : '' }}>
                                                {{ $uso->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_cfdi_use')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="tax_certificate_file" class="block text-sm font-medium text-gray-700">Constancia de Situación Fiscal (Opcional)</label>
                                    <input type="file" name="tax_certificate_file" id="tax_certificate_file" accept="image/*,.pdf"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, PDF. Tamaño máximo: 2MB</p>
                                    @error('tax_certificate_file')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
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
