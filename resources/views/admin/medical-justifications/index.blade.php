<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Justificantes Médicos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="mb-4 overflow-hidden rounded-lg bg-white shadow-sm" style="max-height: 200px;">
                <div class="p-3">
                    <form method="GET" action="{{ route('admin.medical-justifications.index') }}">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="status" class="w-32 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Estado</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobados</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazados</option>
                            </select>

                            <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="Desde" class="w-40 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                            <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="Hasta" class="w-40 rounded-md border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                            <button type="submit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Filtrar</button>

                            <a href="{{ route('admin.medical-justifications.index') }}" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Justificantes -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Lista de Justificantes Médicos ({{ $medicalJustifications->total() }})
                    </h3>
                </div>
                <div class="p-6">
                    @if($medicalJustifications->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiante</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Padre/Tutor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha de Ausencia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Razón</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha de Registro</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($medicalJustifications as $justification)
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $justification->student->user->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $justification->student->schoolGrade->level }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                {{ $justification->parent->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                {{ $justification->absence_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate">{{ $justification->reason }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                @if($justification->status === 'pending')
                                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                        Pendiente
                                                    </span>
                                                @elseif($justification->status === 'approved')
                                                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                        Aprobado
                                                    </span>
                                                @elseif($justification->status === 'rejected')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                                        Rechazado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                @if($justification->document_file_path)
                                                    <a href="{{ Storage::url($justification->document_file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                        <svg class="inline h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Ver
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ $justification->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                                <a href="{{ route('admin.medical-justifications.show', $justification) }}" class="text-blue-600 hover:text-blue-800">
                                                    Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $medicalJustifications->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay justificantes médicos</h3>
                            <p class="mt-1 text-sm text-gray-500">No se encontraron justificantes médicos con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
