<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Reporte de Padres/Tutores con Mora
            </h2>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900">
                ← Volver al Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-4 rounded-lg bg-blue-100 p-4 text-blue-700">
                <p class="font-semibold">Total de padres/tutores con mora: {{ $totalParents }}</p>
            </div>

            @if($parentReport->isEmpty())
                <div class="rounded-lg bg-green-100 p-4 text-green-700">
                    <p>¡Excelente! No hay padres/tutores con pagos en mora.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($parentReport as $parent)
                        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="border-b border-gray-200 bg-gray-50 p-6">
                                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    <!-- Padre/Tutor 1 -->
                                    <div class="col-span-1">
                                        <h3 class="text-sm font-semibold text-gray-700">Padre/Tutor 1</h3>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $parent['tutor_1_name'] }}</p>
                                        @if($parent['tutor_1_email'])
                                            <a href="mailto:{{ $parent['tutor_1_email'] }}" class="text-sm text-blue-600 hover:text-blue-900">
                                                {{ $parent['tutor_1_email'] }}
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Padre/Tutor 2 -->
                                    @if($parent['tutor_2_name'])
                                        <div class="col-span-1">
                                            <h3 class="text-sm font-semibold text-gray-700">Padre/Tutor 2</h3>
                                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $parent['tutor_2_name'] }}</p>
                                            @if($parent['tutor_2_email'])
                                                <a href="mailto:{{ $parent['tutor_2_email'] }}" class="text-sm text-blue-600 hover:text-blue-900">
                                                    {{ $parent['tutor_2_email'] }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Resumen de Mora -->
                                    <div class="col-span-1 md:col-span-1 lg:col-span-1">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <h3 class="text-sm font-semibold text-gray-700">Pagos con Mora</h3>
                                                <p class="mt-1 text-lg font-bold text-red-600">{{ $parent['total_overdue_tuitions'] }}</p>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-semibold text-gray-700">Días Máximo</h3>
                                                <p class="mt-1 text-lg font-bold text-red-600">{{ $parent['max_days_late'] }} días</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <h4 class="mb-4 font-semibold text-gray-900">Estudiantes Asociados</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200 bg-gray-50">
                                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nombre del Estudiante</th>
                                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Pagos con Mora</th>
                                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Días de Mora</th>
                                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parent['students'] as $student)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="px-4 py-3">
                                                        <a href="{{ route('admin.students.show', $student['id']) }}" class="text-blue-600 hover:text-blue-900 hover:underline font-medium">
                                                            {{ $student['name'] }}
                                                        </a>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-red-700 font-semibold">
                                                            {{ $student['overdue_count'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-block rounded-full bg-orange-100 px-3 py-1 text-orange-700 font-semibold">
                                                            {{ $student['max_days_late'] }} días
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($student['phone_number'])
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student['phone_number']) }}?text=Estimado%20padre%2C%20su%20hijo%2Fa%20{{ urlencode($student['name']) }}%20tiene%20{{ $student['overdue_count'] }}%20pago(s)%20en%20mora.%20Por%20favor%20contactarse%20con%20la%20escuela." target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-md bg-green-100 px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-200 transition">
                                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-5.031 1.378c-3.055 2.116-4.797 5.864-4.797 9.88 0 1.052.215 2.076.625 3.031L2.792 22l3.265-.995c.896.554 1.962.866 3.083.866 5.514 0 10-4.486 10-10S13.081 2.5 7.566 2.5c-1.102 0-2.153.287-3.053.798"/>
                                                                </svg>
                                                                WhatsApp
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400 text-xs">Sin teléfono</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
