<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            📢 Anuncios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if($announcements->count() > 0)
                <div class="space-y-4">
                    @foreach($announcements as $announcement)
                        <a href="{{ route('teacher.announcements.show', $announcement) }}" class="block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <div class="flex flex-col gap-4 p-6 md:flex-row">
                                @if($announcement->image_path)
                                    <div class="flex-shrink-0 md:w-64">
                                        <img
                                            src="{{ Storage::url($announcement->image_path) }}"
                                            alt="{{ $announcement->title }}"
                                            class="h-48 w-full rounded-md object-cover md:h-56"
                                        />
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <div class="mb-2 flex items-start justify-between">
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 hover:text-blue-600">
                                                {{ $announcement->title }}
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Por: <span class="font-semibold">{{ $announcement->teacher->name }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="prose prose-sm max-w-none">
                                        <p class="whitespace-pre-wrap text-gray-700">
                                            {{ $announcement->content }}
                                        </p>
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-3">
                                        @if($announcement->target_audience && count($announcement->target_audience) > 0)
                                            <div class="flex gap-2">
                                                @foreach($announcement->target_audience as $audience)
                                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                                        @switch($audience)
                                                            @case('students')
                                                                👨‍🎓 Estudiantes
                                                                @break
                                                            @case('parents')
                                                                👨‍👩‍👧 Padres
                                                                @break
                                                            @case('teachers')
                                                                👨‍🏫 Maestros
                                                                @break
                                                        @endswitch
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($announcement->valid_from || $announcement->valid_until)
                                            <span class="inline-flex items-center text-sm text-amber-700">
                                                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 8h16v9a2 2 0 01-2 2H6a2 2 0 01-2-2V8z" clip-rule="evenodd"></path>
                                                </svg>
                                                @if($announcement->valid_from && $announcement->valid_until)
                                                    Válido: {{ $announcement->valid_from->format('d/m/Y') }} - {{ $announcement->valid_until->format('d/m/Y') }}
                                                @elseif($announcement->valid_from)
                                                    Válido desde: {{ $announcement->valid_from->format('d/m/Y') }}
                                                @else
                                                    Válido hasta: {{ $announcement->valid_until->format('d/m/Y') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-3 text-xs text-gray-500">
                                        Publicado: {{ $announcement->created_at->format('d/m/Y H:i') }}
                                        @if($announcement->created_at != $announcement->updated_at)
                                            | Actualizado: {{ $announcement->updated_at->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $announcements->links() }}
                </div>
            @else
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600">No hay anuncios vigentes en este momento</p>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
