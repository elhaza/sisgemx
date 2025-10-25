<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Personas Autorizadas para Recoger - {{ $student->user->full_name }}
            </h2>
            @if($canAddMore)
                <a href="{{ route('parent.pickup-people.create', $student) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Agregar Persona
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4 rounded-lg bg-blue-50 p-4">
                <p class="text-sm text-gray-700">
                    <strong>Límite de personas autorizadas:</strong> {{ $pickupPeople->count() }} / {{ $limit }}
                </p>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($pickupPeople->isEmpty())
                        <div class="rounded-lg bg-gray-50 p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay personas autorizadas</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza agregando a las personas que pueden recoger a {{ $student->user->full_name }}.</p>
                            @if($canAddMore)
                                <div class="mt-6">
                                    <a href="{{ route('parent.pickup-people.create', $student) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                        Agregar Persona
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($pickupPeople as $person)
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                                    <div class="p-6">
                                        @if($person->face_photo)
                                            <div class="mb-4 flex justify-center">
                                                <img src="{{ Storage::url($person->face_photo) }}" alt="{{ $person->name }}" class="h-32 w-32 rounded-full object-cover">
                                            </div>
                                        @else
                                            <div class="mb-4 flex justify-center">
                                                <div class="flex h-32 w-32 items-center justify-center rounded-full bg-gray-200">
                                                    <svg class="h-16 w-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $person->name }}</h3>
                                            <p class="mt-1 text-sm text-gray-600">{{ $person->relationship->label() }}</p>

                                            @if($person->notes)
                                                <p class="mt-2 text-sm text-gray-500">{{ $person->notes }}</p>
                                            @endif

                                            @if($person->id_photo)
                                                <div class="mt-3">
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                        <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        ID verificada
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="mt-4 flex justify-center gap-2">
                                                <a href="{{ route('parent.pickup-people.edit', [$student, $person]) }}" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                                                    Editar
                                                </a>
                                                <form action="{{ route('parent.pickup-people.destroy', [$student, $person]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar a {{ $person->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
