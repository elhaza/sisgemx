<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $announcement->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Publicado: {{ $announcement->created_at->format('d/m/Y H:i') }}
                                @if($announcement->created_at != $announcement->updated_at)
                                    | Editado: {{ $announcement->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Imagen -->
                    @if($announcement->image_path)
                        <div class="mb-8">
                            <img
                                src="{{ Storage::url($announcement->image_path) }}"
                                alt="{{ $announcement->title }}"
                                class="h-auto w-full rounded-lg object-cover shadow-sm"
                            />
                        </div>
                    @endif

                    <!-- Contenido -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-sm font-semibold text-gray-700">Contenido</h4>
                        <div class="rounded-lg bg-gray-50 p-4 text-gray-800">
                            <p class="whitespace-pre-wrap">{{ $announcement->content }}</p>
                        </div>
                    </div>

                    <!-- Destinatarios -->
                    @if($announcement->target_audience && count($announcement->target_audience) > 0)
                        <div class="mb-8">
                            <h4 class="mb-3 text-sm font-semibold text-gray-700">Destinatarios</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($announcement->target_audience as $audience)
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                        @switch($audience)
                                            @case('students')
                                                👨‍🎓 Estudiantes
                                                @break
                                            @case('parents')
                                                👨‍👩‍👧 Padres de Familia
                                                @break
                                            @case('teachers')
                                                👨‍🏫 Maestros
                                                @break
                                            @default
                                                {{ ucfirst($audience) }}
                                        @endswitch
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Autor -->
                    <div class="mb-8 border-t border-gray-200 pt-6">
                        <h4 class="mb-3 text-sm font-semibold text-gray-700">Información del Autor</h4>
                        <div class="flex items-center">
                            <x-user-avatar :user="$announcement->teacher" size="lg" />
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $announcement->teacher->name }}</p>
                                <p class="text-sm text-gray-600">{{ $announcement->teacher->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex gap-3 border-t border-gray-200 pt-6">
                        @can('update', $announcement)
                            <a
                                href="{{ route('teacher.announcements.edit', $announcement) }}"
                                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </a>
                        @endcan

                        @can('delete', $announcement)
                            <form action="{{ route('teacher.announcements.destroy', $announcement) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este anuncio?')"
                                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                >
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @endcan

                        <a
                            href="{{ route('teacher.announcements.index') }}"
                            class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
