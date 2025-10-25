<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Registrar Comprobante de Pago
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finance.payment-receipts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Estudiante</label>
                            <select name="student_id" id="student_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar estudiante</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->full_name }} - {{ $student->enrollment_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">Padre/Tutor</label>
                            <select name="parent_id" id="parent_id" required disabled
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Primero selecciona un estudiante</option>
                            </select>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700">Monto Pagado</label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('amount_paid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="reference" class="block text-sm font-medium text-gray-700">Referencia / No. de Transacción</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" required
                                placeholder="Ej: TRANSF-20240903-1234"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="account_holder_name" class="block text-sm font-medium text-gray-700">Nombre del Titular</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name') }}" required
                                placeholder="Nombre completo de quien realizó el pago"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('account_holder_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="issuing_bank" class="block text-sm font-medium text-gray-700">Banco Emisor</label>
                            <input type="text" name="issuing_bank" id="issuing_bank" value="{{ old('issuing_bank') }}" required
                                placeholder="Ej: Banco Nacional"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('issuing_bank')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                            <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="receipt_image" class="block text-sm font-medium text-gray-700">Comprobante (Imagen)</label>
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</p>
                            @error('receipt_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado Inicial</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendiente de Validación</option>
                                <option value="validated" {{ old('status') == 'validated' ? 'selected' : '' }}>Validado</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('finance.payment-receipts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Registrar Comprobante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('student_id').addEventListener('change', function() {
            const studentId = this.value;
            const parentSelect = document.getElementById('parent_id');

            if (!studentId) {
                parentSelect.disabled = true;
                parentSelect.innerHTML = '<option value="">Primero selecciona un estudiante</option>';
                return;
            }

            // Fetch parents for selected student
            fetch(`{{ url('finance/payment-receipts/student') }}/${studentId}/parents`)
                .then(response => response.json())
                .then(parents => {
                    parentSelect.disabled = false;
                    parentSelect.innerHTML = '<option value="">Seleccionar padre/tutor</option>';

                    if (parents.length === 0) {
                        parentSelect.innerHTML = '<option value="">No hay tutores registrados para este estudiante</option>';
                        parentSelect.disabled = true;
                        return;
                    }

                    parents.forEach(parent => {
                        const option = document.createElement('option');
                        option.value = parent.id;
                        option.textContent = `${parent.name} - ${parent.email}`;

                        // Preserve old value if exists
                        if ('{{ old('parent_id') }}' == parent.id) {
                            option.selected = true;
                        }

                        parentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching parents:', error);
                    parentSelect.disabled = true;
                    parentSelect.innerHTML = '<option value="">Error al cargar tutores</option>';
                });
        });

        // Trigger change event on page load if student is selected (for validation errors)
        if (document.getElementById('student_id').value) {
            document.getElementById('student_id').dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>
