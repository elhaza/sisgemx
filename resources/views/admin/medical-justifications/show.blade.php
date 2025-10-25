<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detalles del Justificante Médico
            </h2>
            <a href="{{ route('admin.medical-justifications.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Información del Justificante</h3>
                        @if($medicalJustification->status === 'pending')
                            <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">
                                Pendiente
                            </span>
                        @elseif($medicalJustification->status === 'approved')
                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                                Aprobado
                            </span>
                        @elseif($medicalJustification->status === 'rejected')
                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                                Rechazado
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estudiante</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="font-semibold">{{ $medicalJustification->student->user->full_name }}</div>
                                <div class="text-gray-600">{{ $medicalJustification->student->schoolGrade->level }}</div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Padre/Tutor</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="font-semibold">{{ $medicalJustification->parent->name }}</div>
                                <div class="text-gray-600">{{ $medicalJustification->parent->email }}</div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Ausencia</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $medicalJustification->absence_date->format('d/m/Y') }}
                                <span class="text-gray-600">({{ $medicalJustification->absence_date->diffForHumans() }})</span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Registro</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $medicalJustification->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Razón de la Ausencia</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $medicalJustification->reason }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Documento Adjunto</dt>
                            <dd class="mt-2">
                                @if($medicalJustification->document_file_path)
                                    <div class="flex items-center gap-4">
                                        <a href="{{ Storage::url($medicalJustification->document_file_path) }}" target="_blank" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver Documento
                                        </a>
                                        <a href="{{ Storage::url($medicalJustification->document_file_path) }}" download class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Descargar
                                        </a>
                                    </div>

                                    @if(Str::endsWith($medicalJustification->document_file_path, ['.jpg', '.jpeg', '.png', '.gif']))
                                        <div class="mt-4">
                                            <img src="{{ Storage::url($medicalJustification->document_file_path) }}" alt="Documento" class="max-h-96 rounded-lg border border-gray-300 shadow-sm">
                                        </div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">No se adjuntó ningún documento</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Información de Aprobación/Rechazo -->
            @if($medicalJustification->status !== 'pending')
                <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            @if($medicalJustification->status === 'approved')
                                Información de Aprobación
                            @else
                                Información de Rechazo
                            @endif
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    @if($medicalJustification->status === 'approved')
                                        Aprobado por
                                    @else
                                        Rechazado por
                                    @endif
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($medicalJustification->reviewedBy)
                                        {{ $medicalJustification->reviewedBy->name }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Revisión</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($medicalJustification->reviewed_at)
                                        {{ $medicalJustification->reviewed_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </dd>
                            </div>

                            @if($medicalJustification->status === 'rejected' && $medicalJustification->rejection_reason)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Motivo del Rechazo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <div class="rounded-md bg-red-50 p-4">
                                            <div class="flex">
                                                <div class="shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-red-700">{{ $medicalJustification->rejection_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Información Adicional del Estudiante -->
            <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Adicional del Estudiante</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Grado</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $medicalJustification->student->schoolGrade->level }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email del Estudiante</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $medicalJustification->student->user->email }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Acciones de Aprobación/Rechazo -->
            @if($medicalJustification->status === 'pending')
                <div class="mt-6 flex gap-4">
                    <form action="{{ route('admin.medical-justifications.approve', $medicalJustification) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center rounded-md bg-green-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aprobar Justificante
                        </button>
                    </form>

                    <button
                        type="button"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'reject-justification')"
                        class="flex flex-1 items-center justify-center rounded-md bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Rechazar Justificante
                    </button>
                </div>

                <!-- Modal de Rechazo -->
                <x-modal name="reject-justification" focusable>
                    <form method="POST" action="{{ route('admin.medical-justifications.reject', $medicalJustification) }}" class="p-6">
                        @csrf

                        <h2 class="text-lg font-medium text-gray-900">
                            Rechazar Justificante Médico
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Por favor, proporciona un motivo para rechazar este justificante médico. Esta información será compartida con el padre/tutor.
                        </p>

                        <div class="mt-6">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                                Motivo del Rechazo <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="rejection_reason"
                                name="rejection_reason"
                                rows="4"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="Explica por qué se rechaza este justificante médico..."
                            ></textarea>
                            @error('rejection_reason')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'reject-justification')"
                                class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
        </div>
    </div>
</x-app-layout>
