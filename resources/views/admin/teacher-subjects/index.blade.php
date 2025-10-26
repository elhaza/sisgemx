<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Competencias de Docentes por Materia
            </h2>
            <a href="{{ route('admin.teacher-subjects.create') }}"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                + Nueva Competencia
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($teacherSubjects->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">
                                    Docente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">
                                    Materia
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">
                                    Nivel de Competencia
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($teacherSubjects as $teacherSubject)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $teacherSubject->teacher->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $teacherSubject->subject->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                     style="width: {{ ($teacherSubject->proficiency / 10) * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold">{{ $teacherSubject->proficiency }}/10</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('admin.teacher-subjects.edit', $teacherSubject) }}"
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.teacher-subjects.destroy', $teacherSubject) }}"
                                              method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="ml-4 text-red-600 hover:text-red-900">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-gray-200 bg-white px-4 py-4 sm:px-6">
                        {{ $teacherSubjects->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C6.5 6.253 1 10.5 1 16.5S6.5 26.5 12 26.5s11-4.247 11-10.5S17.5 6.253 12 6.253z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin competencias registradas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Comienza registrando las competencias de los docentes.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('admin.teacher-subjects.create') }}"
                               class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                                Nueva Competencia
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
