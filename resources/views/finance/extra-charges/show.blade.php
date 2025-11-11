<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $template->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $template->assignedCharges->count() }} estudiantes asignados
                </p>
            </div>
            <a href="{{ route('finance.extra-charges.index') }}"
               class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Detalles del Cargo -->
            <div class="mb-8 grid gap-4 md:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600">Tipo</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            @switch($template->charge_type)
                                @case('inscription')
                                    Inscripción
                                    @break
                                @case('materials')
                                    Materiales
                                    @break
                                @case('exam')
                                    Examen
                                    @break
                                @default
                                    Otro
                            @endswitch
                        </p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600">Monto</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($template->amount, 2) }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600">Vencimiento</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $template->default_due_date->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600">Estado</p>
                        <p class="mt-2 text-2xl font-bold"
                           :class="$template->is_active ? 'text-green-600' : 'text-gray-600'">
                            {{ $template->is_active ? 'Activo' : 'Inactivo' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($template->description)
                <div class="mb-8 rounded-lg bg-blue-50 border border-blue-200 p-6">
                    <h3 class="font-semibold text-blue-900">Descripción</h3>
                    <p class="mt-2 text-blue-800">{{ $template->description }}</p>
                </div>
            @endif

            <!-- Lista de Asignaciones -->
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Estudiantes Asignados</h3>
                </div>

                @if($assignedCharges->isEmpty())
                    <div class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600">No hay estudiantes asignados a este cargo</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estudiante</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Monto</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vencimiento</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estado</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($assignedCharges as $charge)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $charge->student->user->full_name }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $charge->student->schoolGrade->grade_level }}° - {{ $charge->student->schoolGrade->name }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900">${{ number_format($charge->amount, 2) }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900">{{ $charge->due_date->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                                  :class="$charge->is_paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                                {{ $charge->is_paid ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(!$charge->is_paid)
                                                <form action="{{ route('finance.assigned-charges.mark-as-paid', $charge) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                                            onclick="return confirm('¿Marcar como pagado?')">
                                                        Marcar Pagado
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-sm text-gray-600">Completado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                        {{ $assignedCharges->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
