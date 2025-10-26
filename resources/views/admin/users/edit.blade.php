<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('apellido_paterno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                            <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno', $user->apellido_materno) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('apellido_materno')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700" id="email-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
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
                                @foreach($roles as $role)
                                    <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Teacher-specific fields -->
                        <div id="teacher-fields" class="hidden space-y-4 rounded-lg bg-blue-50 p-4 mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Configuración de Horas (Maestro)</h3>

                            <div>
                                <label for="max_hours_per_day" class="block text-sm font-medium text-gray-700">Horas Máximas por Día</label>
                                <input type="number" name="max_hours_per_day" id="max_hours_per_day" min="1" max="12" step="0.5" value="{{ old('max_hours_per_day', $user->max_hours_per_day ?? 8) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-600">Máximo de horas que puede trabajar en un día</p>
                                @error('max_hours_per_day')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_hours_per_week" class="block text-sm font-medium text-gray-700">Horas Máximas por Semana</label>
                                <input type="number" name="max_hours_per_week" id="max_hours_per_week" min="1" max="60" step="0.5" value="{{ old('max_hours_per_week', $user->max_hours_per_week ?? 40) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-600">Máximo de horas que puede trabajar en una semana</p>
                                @error('max_hours_per_week')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Availability Section -->
                            <div class="border-t border-blue-200 pt-4 mt-4">
                                <button type="button" id="toggle-availability" class="flex items-center justify-between w-full text-sm font-semibold text-gray-800 mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Disponibilidad Semanal
                                    </span>
                                    <svg id="availability-chevron" class="h-5 w-5 transform transition-transform text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>

                                <div id="availability-section" class="hidden space-y-3 bg-white rounded p-3 border border-blue-200">
                                    <div id="availabilities-list" class="space-y-2">
                                        @forelse($availabilities as $availability)
                                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded text-sm">
                                                <span>{{ $dayNames[$availability->day_of_week] }}: {{ $availability->start_time }} - {{ $availability->end_time }}</span>
                                                <button type="button" class="text-red-600 hover:text-red-900 delete-availability" data-availability-id="{{ $availability->id }}">Eliminar</button>
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-500 italic">Sin disponibilidades configuradas. El maestro estará disponible en cualquier momento.</p>
                                        @endforelse
                                    </div>

                                    <button type="button" id="add-availability-btn" class="text-sm text-blue-600 hover:text-blue-800 font-medium">+ Agregar Disponibilidad</button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding availability -->
    <div id="availability-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Agregar Disponibilidad</h3>

            <div class="space-y-4">
                <div>
                    <label for="availability-day" class="block text-sm font-medium text-gray-700">Día de la Semana</label>
                    <select id="availability-day" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar día</option>
                        <option value="monday">Lunes</option>
                        <option value="tuesday">Martes</option>
                        <option value="wednesday">Miércoles</option>
                        <option value="thursday">Jueves</option>
                        <option value="friday">Viernes</option>
                        <option value="saturday">Sábado</option>
                        <option value="sunday">Domingo</option>
                    </select>
                </div>

                <div>
                    <label for="availability-start" class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                    <input type="time" id="availability-start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="availability-end" class="block text-sm font-medium text-gray-700">Hora de Fin</label>
                    <input type="time" id="availability-end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div id="availability-error" class="text-sm text-red-600 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" id="modal-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="button" id="modal-save" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                    Guardar
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = {{ $user->id }};
            const roleSelect = document.getElementById('role');
            const emailLabel = document.getElementById('email-label');
            const emailInput = document.getElementById('email');
            const emailHelp = document.getElementById('email-help');
            const teacherFields = document.getElementById('teacher-fields');
            const toggleAvailabilityBtn = document.getElementById('toggle-availability');
            const availabilitySection = document.getElementById('availability-section');
            const availabilityChevron = document.getElementById('availability-chevron');
            const addAvailabilityBtn = document.getElementById('add-availability-btn');
            const availabilityModal = document.getElementById('availability-modal');
            const modalCancel = document.getElementById('modal-cancel');
            const modalSave = document.getElementById('modal-save');
            const availabilityDay = document.getElementById('availability-day');
            const availabilityStart = document.getElementById('availability-start');
            const availabilityEnd = document.getElementById('availability-end');
            const availabilityError = document.getElementById('availability-error');

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

            // Toggle availability section
            toggleAvailabilityBtn.addEventListener('click', function() {
                availabilitySection.classList.toggle('hidden');
                availabilityChevron.classList.toggle('rotate-180');
            });

            // Open add availability modal
            addAvailabilityBtn.addEventListener('click', function() {
                availabilityDay.value = '';
                availabilityStart.value = '';
                availabilityEnd.value = '';
                availabilityError.classList.add('hidden');
                availabilityModal.classList.remove('hidden');
            });

            // Close modal
            modalCancel.addEventListener('click', function() {
                availabilityModal.classList.add('hidden');
            });

            // Close modal when clicking outside
            availabilityModal.addEventListener('click', function(e) {
                if (e.target === availabilityModal) {
                    availabilityModal.classList.add('hidden');
                }
            });

            // Save availability
            modalSave.addEventListener('click', async function() {
                availabilityError.classList.add('hidden');

                // Validate inputs
                if (!availabilityDay.value || !availabilityStart.value || !availabilityEnd.value) {
                    availabilityError.textContent = 'Por favor completa todos los campos';
                    availabilityError.classList.remove('hidden');
                    return;
                }

                if (availabilityEnd.value <= availabilityStart.value) {
                    availabilityError.textContent = 'La hora de fin debe ser posterior a la hora de inicio';
                    availabilityError.classList.remove('hidden');
                    return;
                }

                try {
                    const response = await fetch(`/admin/users/${userId}/availabilities`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify({
                            day_of_week: availabilityDay.value,
                            start_time: availabilityStart.value,
                            end_time: availabilityEnd.value,
                        }),
                    });

                    if (!response.ok) {
                        const data = await response.json();
                        availabilityError.textContent = data.message || 'Error al guardar la disponibilidad';
                        availabilityError.classList.remove('hidden');
                        return;
                    }

                    // Close modal and reload availabilities
                    availabilityModal.classList.add('hidden');
                    location.reload();
                } catch (error) {
                    availabilityError.textContent = 'Error al guardar la disponibilidad';
                    availabilityError.classList.remove('hidden');
                }
            });

            // Delete availability
            document.addEventListener('click', async function(e) {
                if (e.target.classList.contains('delete-availability')) {
                    const availabilityId = e.target.getAttribute('data-availability-id');

                    if (!confirm('¿Estás seguro de que deseas eliminar esta disponibilidad?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/users/${userId}/availabilities/${availabilityId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                        });

                        if (!response.ok) {
                            alert('Error al eliminar la disponibilidad');
                            return;
                        }

                        // Remove from DOM
                        e.target.closest('.flex').remove();

                        // Show empty message if no availabilities left
                        const list = document.getElementById('availabilities-list');
                        if (list.children.length === 0) {
                            list.innerHTML = '<p class="text-xs text-gray-500 italic">Sin disponibilidades configuradas. El maestro estará disponible en cualquier momento.</p>';
                        }
                    } catch (error) {
                        alert('Error al eliminar la disponibilidad');
                    }
                }
            });

            roleSelect.addEventListener('change', updateFormFields);

            // Initialize on load
            updateFormFields();
        });
    </script>
</x-app-layout>
