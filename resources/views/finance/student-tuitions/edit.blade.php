<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Colegiaturas del Período
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finance.student-tuitions.update', $studentTuition) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estudiante</label>
                                <input type="text" value="{{ $studentTuition->student->user->full_name }}" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ciclo Escolar</label>
                                <input type="text" value="{{ $studentTuition->schoolYear->name }}" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Colegiaturas Mensuales</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Mes</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Monto Mensual</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Descuento</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Total a Pagar</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Razón del Descuento</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($allTuitions as $tuition)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $tuition->month_name }} {{ $tuition->year }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm">
                                                    <input type="number" step="0.01" name="tuitions[{{ $tuition->id }}][monthly_amount]"
                                                        value="{{ old("tuitions.{$tuition->id}.monthly_amount", $tuition->monthly_amount) }}"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm">
                                                    <input type="number" step="0.01" name="tuitions[{{ $tuition->id }}][discount_amount]"
                                                        value="{{ old("tuitions.{$tuition->id}.discount_amount", $tuition->discount_amount) }}"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                                    <span data-total-{{ $tuition->id }}>
                                                        ${{ number_format($tuition->monthly_amount - $tuition->discount_amount, 2) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <textarea name="tuitions[{{ $tuition->id }}][discount_reason]"
                                                        placeholder="Explicar razón del descuento..."
                                                        rows="1"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old("tuitions.{$tuition->id}.discount_reason", $tuition->discount_reason) }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('finance.student-tuitions.index', ['school_year_id' => $studentTuition->school_year_id]) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Actualizar Período Completo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate total when discount changes
        document.querySelectorAll('input[name*="[discount_amount]"]').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const tuitionId = this.name.match(/\[(\d+)\]/)[1];
                const monthlyInput = row.querySelector(`input[name="tuitions[${tuitionId}][monthly_amount]"]`);
                const discountInput = this;
                const totalSpan = document.querySelector(`[data-total-${tuitionId}]`);

                const monthly = parseFloat(monthlyInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                const total = monthly - discount;

                totalSpan.textContent = '$' + total.toFixed(2);
            });
        });
    </script>
</x-app-layout>
