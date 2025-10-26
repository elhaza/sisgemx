<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        @if(request('return_to'))
                            <input type="hidden" name="return_to" value="{{ request('return_to') }}">
                        @endif
                        @if(request('field'))
                            <input type="hidden" name="field" value="{{ request('field') }}">
                        @endif

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('apellido_paterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('apellido_materno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700" id="email-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="ejemplo@correo.com o nombre_usuario">
                            <p id="email-help" class="mt-1 text-xs text-gray-500"></p>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="role" id="role" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->value }}" {{ (old('role') ?: request('role')) === $role->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Teacher-specific fields -->
                        <div id="teacher-fields" class="hidden space-y-4 rounded-lg bg-blue-50 p-4 mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Configuración de Horas (Maestro)</h3>

                            <div>
                                <label for="max_hours_per_day" class="block text-sm font-medium text-gray-700">Horas Máximas por Día</label>
                                <input type="number" name="max_hours_per_day" id="max_hours_per_day" min="1" max="12" step="0.5" value="{{ old('max_hours_per_day', 8) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-600">Máximo de horas que puede trabajar en un día (default: 8)</p>
                                @error('max_hours_per_day')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_hours_per_week" class="block text-sm font-medium text-gray-700">Horas Máximas por Semana</label>
                                <input type="number" name="max_hours_per_week" id="max_hours_per_week" min="1" max="60" step="0.5" value="{{ old('max_hours_per_week', 40) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-600">Máximo de horas que puede trabajar en una semana (default: 40)</p>
                                @error('max_hours_per_week')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const emailLabel = document.getElementById('email-label');
            const emailInput = document.getElementById('email');
            const emailHelp = document.getElementById('email-help');
            const teacherFields = document.getElementById('teacher-fields');

            function updateFormFields() {
                if (roleSelect.value === 'student') {
                    emailLabel.textContent = 'Email/Username';
                    emailInput.type = 'text';
                    emailInput.placeholder = 'nombre_usuario o ejemplo@correo.com';
                    emailHelp.textContent = 'Para estudiantes: puedes usar un nombre único de usuario sin necesidad de formato de email.';
                    teacherFields.classList.add('hidden');
                } else {
                    emailLabel.textContent = 'Email';
                    emailInput.type = 'email';
                    emailInput.placeholder = 'ejemplo@correo.com';
                    emailHelp.textContent = '';
                }

                // Show teacher fields only if role is teacher
                if (roleSelect.value === 'teacher') {
                    teacherFields.classList.remove('hidden');
                } else {
                    teacherFields.classList.add('hidden');
                }
            }

            roleSelect.addEventListener('change', updateFormFields);

            // Initialize on load if role is pre-selected
            if (roleSelect.value) {
                updateFormFields();
            }
        });
    </script>
</x-app-layout>
