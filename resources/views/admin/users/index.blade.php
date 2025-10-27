<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('admin.users.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Crear Usuario
            </a>
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

            <!-- Filtros -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm" class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-3">
                            <!-- Búsqueda por nombre/email -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Buscar por nombre, email o apellidos
                                </label>
                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    value="{{ request('search') }}"
                                    placeholder="Escribe para filtrar..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                />
                            </div>

                            <!-- Filtro por rol -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Filtrar por rol
                                </label>
                                <select
                                    name="role"
                                    id="role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                >
                                    <option value="">Todos los roles</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="finance_admin" {{ request('role') === 'finance_admin' ? 'selected' : '' }}>Administrador de Finanzas</option>
                                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Maestro</option>
                                    <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Padre de Familia</option>
                                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Estudiante</option>
                                </select>
                            </div>

                            <!-- Por página -->
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Por página
                                </label>
                                <select
                                    name="per_page"
                                    id="per_page"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                    onchange="document.getElementById('filterForm').submit();"
                                >
                                    <option value="15" {{ request('per_page') === '15' || !request('per_page') ? 'selected' : '' }}>15 por página</option>
                                    <option value="30" {{ request('per_page') === '30' ? 'selected' : '' }}>30 por página</option>
                                    <option value="50" {{ request('per_page') === '50' ? 'selected' : '' }}>50 por página</option>
                                    <option value="100" {{ request('per_page') === '100' ? 'selected' : '' }}>100 por página</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            <a
                                href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 dark:focus:ring-offset-gray-900"
                            >
                                Limpiar filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Información de resultados -->
                    @if(request()->filled('search') || request()->filled('role'))
                        <div class="mb-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                            Mostrando <strong>{{ $users->count() }}</strong> resultado(s)
                            @if(request('search'))
                                que coinciden con "<strong>{{ request('search') }}</strong>"
                            @endif
                            @if(request('role'))
                                del rol <strong>{{ ucfirst(str_replace('_', ' ', request('role'))) }}</strong>
                            @endif
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rol</th>
                                    @if(request('role') === 'teacher')
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Horas/Semana</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Horas/Día</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($users as $user)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $user->name }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                                {{ ucfirst($user->role->value) }}
                                            </span>
                                        </td>
                                        @if(request('role') === 'teacher')
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                                    {{ $user->max_hours_per_week ?? 40 }} hrs
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                                    {{ $user->max_hours_per_day ?? 8 }} hrs
                                                </span>
                                            </td>
                                        @endif
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ request('role') === 'teacher' ? 6 : 4 }}" class="px-6 py-4 text-center text-gray-500">No hay usuarios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
