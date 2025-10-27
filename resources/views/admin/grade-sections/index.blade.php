<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Secciones de Grado
            </h2>
            <a href="{{ route('admin.grade-sections.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Crear Sección
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

            <!-- Filtro por ciclo escolar -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.grade-sections.index') }}" class="flex gap-4 items-end">
                        <div>
                            <label for="school_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Filtrar por Ciclo Escolar
                            </label>
                            <select name="school_year_id" id="school_year_id" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Todos los ciclos --</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}" {{ request('school_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.grade-sections.index') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sección</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ciclo Escolar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estudiantes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($gradeSections as $gradeSection)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $gradeSection->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->grade_level }}°</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->section }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $gradeSection->schoolYear->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                                {{ $gradeSection->students()->count() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.students.index', ['grade_level' => $gradeSection->grade_level, 'group' => $gradeSection->section, 'school_year_id' => $gradeSection->school_year_id]) }}" class="text-green-600 hover:text-green-900">Ver</a>
                                            <a href="{{ route('admin.grade-sections.edit', $gradeSection) }}" class="ml-3 text-blue-600 hover:text-blue-900">Editar</a>
                                            <button type="button" onclick="handleDelete({{ $gradeSection->id }}, '{{ $gradeSection->name }}')" class="ml-3 text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay secciones de grado registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $gradeSections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para transferir estudiantes -->
    <div id="transferModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Transferir Estudiantes</h3>
                <button type="button" onclick="closeTransferModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Se van a eliminar <span id="studentCount" class="font-semibold"></span> estudiante(s).
                    Por favor, selecciona una sección para transferirlos:
                </p>

                <form id="transferForm" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="target_section_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sección Destino
                        </label>
                        <select name="target_section_id" id="target_section_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Selecciona una sección --</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeTransferModal()" class="flex-1 rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Transferir y Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentSectionId = null;

        async function handleDelete(sectionId, sectionName) {
            currentSectionId = sectionId;

            try {
                const response = await fetch(`/admin/grade-sections/${sectionId}/transfer-options`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                // If there are no students, delete directly
                if (data.studentCount === 0) {
                    if (confirm('¿Estás seguro de que deseas eliminar esta sección?')) {
                        deleteGradeSection(sectionId);
                    }
                    return;
                }

                // If there are students but no available sections
                if (data.availableSections.length === 0) {
                    alert('No se puede eliminar esta sección porque tiene estudiantes inscritos y no hay otras secciones en el mismo nivel a las que transferirlos.');
                    return;
                }

                // Show modal with transfer options
                document.getElementById('studentCount').textContent = data.studentCount;
                document.getElementById('modalTitle').textContent = `Eliminar sección "${sectionName}"`;

                // Populate target sections dropdown
                const select = document.getElementById('target_section_id');
                select.innerHTML = '<option value="">-- Selecciona una sección --</option>';

                data.availableSections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = `${section.name} (${section.students_count || 0} estudiantes)`;
                    select.appendChild(option);
                });

                // Update form action
                document.getElementById('transferForm').action = `/admin/grade-sections/${sectionId}/transfer-and-delete`;

                // Show modal
                document.getElementById('transferModal').classList.remove('hidden');

            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud.');
            }
        }

        function deleteGradeSection(sectionId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/grade-sections/${sectionId}`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }

        function closeTransferModal() {
            document.getElementById('transferModal').classList.add('hidden');
            currentSectionId = null;
        }

        // Close modal when clicking outside
        document.getElementById('transferModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransferModal();
            }
        });
    </script>
</x-app-layout>
