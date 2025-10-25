<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Subir Comprobante de Pago
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="mb-6 text-sm text-gray-600">
                        Por favor complete el formulario con los datos de su transferencia. El comprobante será revisado por el área de finanzas.
                    </p>

                    <form action="{{ route('parent.payment-receipts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Estudiante</label>
                            <select name="student_id" id="student_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar estudiante</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }} - {{ $student->enrollment_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
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
                                placeholder="Ej: 5000.00"
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
                                <option value="transfer" {{ old('payment_method', 'transfer') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
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

                        <div class="mb-4 rounded-lg bg-yellow-50 p-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Nota:</strong> Su comprobante quedará en estado "Pendiente de Validación" hasta que sea revisado por el área de finanzas.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('parent.payment-receipts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Subir Comprobante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
