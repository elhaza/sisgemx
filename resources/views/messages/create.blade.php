<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Redactar Mensaje
        </h2>
    </x-slot>

    <script>
        function messageForm() {
            return {
                selectedRole: '',
                selectedFilter: '',
                selectedSecondaryFilter: '',
                searchQuery: '',
                availableFilters: [],
                secondaryFilterOptions: [],
                searchResults: [],
                showSearchResults: false,
                selectedRecipients: [],
                needsSecondaryFilter: false,
                secondaryFilterLabel: '',
                loadingRecipients: false,

                init() {
                    this.$watch('selectedFilter', () => {
                        console.log('selectedFilter cambió a:', this.selectedFilter);
                    });
                },

                onRoleChange() {
                    this.selectedFilter = '';
                    this.selectedSecondaryFilter = '';
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.selectedRecipients = [];
                    this.updateRecipientInput();
                    this.fetchFilterOptions();
                },

                async fetchFilterOptions() {
                    if (!this.selectedRole) return;

                    try {
                        const response = await fetch(`/api/messages/filter-options?role=${this.selectedRole}`);
                        const data = await response.json();
                        this.availableFilters = data.filters || [];
                    } catch (error) {
                        console.error('Error fetching filter options:', error);
                    }
                },

                onFilterChange() {
                    console.log('onFilterChange llamado con selectedFilter:', this.selectedFilter);
                    this.selectedSecondaryFilter = '';
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.secondaryFilterOptions = [];

                    const secondaryContainer = document.getElementById('secondaryFilterContainer');

                    if (!this.selectedFilter) {
                        this.needsSecondaryFilter = false;
                        if (secondaryContainer) secondaryContainer.style.display = 'none';
                        return;
                    }

                    if (this.selectedFilter === 'all') {
                        this.needsSecondaryFilter = false;
                        if (secondaryContainer) secondaryContainer.style.display = 'none';
                        this.loadRecipients();
                        return;
                    }

                    if (this.selectedFilter === 'individual') {
                        this.needsSecondaryFilter = false;
                        if (secondaryContainer) secondaryContainer.style.display = 'none';
                        return;
                    }

                    this.needsSecondaryFilter = true;

                    const filterLabels = {
                        'teacher': {
                            'by_level': 'Por nivel',
                            'by_subject': 'Por materia',
                            'by_school_grade': 'Por grupo'
                        },
                        'parent': {
                            'by_school_grade': 'Por grado',
                            'by_school_grade_group': 'Por grupo'
                        },
                        'student': {
                            'by_school_grade': 'Por grado',
                            'by_school_grade_group': 'Por grupo'
                        }
                    };

                    const labels = filterLabels[this.selectedRole] || {};
                    this.secondaryFilterLabel = labels[this.selectedFilter] || 'Selecciona una opción';

                    if (secondaryContainer) {
                        secondaryContainer.style.display = 'block';
                    }

                    this.fetchSecondaryFilterData();
                },

                async fetchSecondaryFilterData() {
                    if (!this.selectedRole || !this.selectedFilter) {
                        this.secondaryFilterOptions = [];
                        return;
                    }

                    try {
                        const url = `/api/messages/filter-data?role=${this.selectedRole}&filter_type=${this.selectedFilter}`;
                        const response = await fetch(url);
                        if (!response.ok) {
                            console.error('API error:', response.status);
                            this.secondaryFilterOptions = [];
                            return;
                        }
                        const data = await response.json();
                        this.secondaryFilterOptions = Array.isArray(data.items) ? data.items : [];
                    } catch (error) {
                        console.error('Error fetching secondary filter data:', error);
                        this.secondaryFilterOptions = [];
                    }
                },

                onSecondaryFilterChange() {
                    if (this.selectedSecondaryFilter) {
                        this.loadRecipients();
                    }
                },

                async onSearchInput() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    try {
                        const params = new URLSearchParams({
                            role: this.selectedRole,
                            filter_type: this.selectedFilter,
                            search: this.searchQuery
                        });

                        const response = await fetch(`/api/messages/users?${params}`);
                        const data = await response.json();
                        this.searchResults = data || [];
                    } catch (error) {
                        console.error('Error searching users:', error);
                        this.searchResults = [];
                    }
                },

                async loadRecipients() {
                    this.loadingRecipients = true;

                    try {
                        const params = new URLSearchParams({
                            role: this.selectedRole,
                            filter_type: this.selectedFilter
                        });

                        if (this.selectedSecondaryFilter) {
                            params.append('filter_id', this.selectedSecondaryFilter);
                        }

                        const response = await fetch(`/api/messages/users?${params}`);
                        const users = await response.json();

                        users.forEach(user => {
                            if (!this.selectedRecipients.find(r => r.id === user.id)) {
                                this.selectedRecipients.push(user);
                            }
                        });

                        this.updateRecipientInput();
                    } catch (error) {
                        console.error('Error loading recipients:', error);
                    } finally {
                        this.loadingRecipients = false;
                    }
                },

                selectUser(user) {
                    if (!this.selectedRecipients.find(r => r.id === user.id)) {
                        this.selectedRecipients.push(user);
                        this.updateRecipientInput();
                    }
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.showSearchResults = false;
                },

                removeRecipient(id) {
                    this.selectedRecipients = this.selectedRecipients.filter(r => r.id !== id);
                    this.updateRecipientInput();
                },

                updateRecipientInput() {
                    const recipientIds = this.selectedRecipients.map(r => r.id).join(',');
                    document.getElementById('recipientIds').value = recipientIds;
                },

                getRoleName() {
                    const roleNames = {
                        'admin': 'Administrador',
                        'finance_admin': 'Usuario de Finanzas',
                        'teacher': 'Maestro',
                        'parent': 'Padre',
                        'student': 'Estudiante'
                    };
                    return roleNames[this.selectedRole] || '';
                },

                handleSubmit(e) {
                    if (this.selectedRecipients.length === 0) {
                        e.preventDefault();
                        alert('Debes seleccionar al menos un destinatario');
                    }
                }
            };
        }
    </script>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6">
                    <form action="{{ route('messages.store') }}" method="POST" id="messageForm" x-data="messageForm()" @submit="handleSubmit">
                        @csrf

                        <!-- Role Selection -->
                        @if(auth()->user()->isAdmin())
                            <div class="mb-6">
                                <label for="recipientRole" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Enviar a (Rol) *
                                </label>
                                <select
                                    id="recipientRole"
                                    x-model="selectedRole"
                                    @change="onRoleChange()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Selecciona un rol</option>
                                    <option value="admin">Administradores</option>
                                    <option value="finance_admin">Finanzas</option>
                                    <option value="teacher">Maestros</option>
                                    <option value="parent">Padres</option>
                                    <option value="student">Estudiantes</option>
                                </select>
                                @error('recipient_ids')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Filter Selection -->
                            <div class="mb-6" x-show="selectedRole">
                                <label for="filterType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Filtrar por *
                                </label>
                                <select
                                    id="filterType"
                                    x-model="selectedFilter"
                                    @change="onFilterChange()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Selecciona una opción</option>
                                    <template x-for="filter in availableFilters" :key="filter.type">
                                        <option :value="filter.type" x-text="filter.label"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Secondary Filter (Level, Subject, Group, etc.) - Always in DOM but hidden by default -->
                            <div class="mb-6" id="secondaryFilterContainer" style="display: none;">
                                <label for="secondaryFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span x-text="secondaryFilterLabel"></span>
                                </label>
                                <select
                                    id="secondaryFilter"
                                    x-model="selectedSecondaryFilter"
                                    @change="onSecondaryFilterChange()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Selecciona una opción</option>
                                    <template x-for="item in secondaryFilterOptions" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Individual Search (for individual selections) -->
                            <div class="mb-6" x-show="selectedFilter === 'individual'">
                                <label for="individualSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Buscar <span x-text="getRoleName()"></span>
                                </label>
                                <div class="relative mt-3">
                                    <input
                                        type="text"
                                        id="individualSearch"
                                        x-model="searchQuery"
                                        @input="onSearchInput()"
                                        @focus="showSearchResults = true"
                                        @blur="setTimeout(() => showSearchResults = false, 200)"
                                        placeholder="Busca por nombre..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                                        autocomplete="off"
                                    >
                                    <div
                                        x-show="showSearchResults && searchResults.length > 0"
                                        class="absolute top-full left-0 right-0 z-10 mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg max-h-96 overflow-y-auto">
                                        <template x-for="result in searchResults" :key="result.id">
                                            <div
                                                @click="selectUser(result)"
                                                class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="result.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="result.email"></p>
                                                    </div>
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 whitespace-nowrap" x-text="result.role"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Load All for Non-Individual -->
                            <div class="mb-6" x-show="selectedFilter && selectedFilter !== 'individual'" style="display: none;">
                                <button
                                    type="button"
                                    @click="loadRecipients()"
                                    class="inline-flex items-center rounded-md bg-blue-100 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/40">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Agregar todos
                                </button>
                            </div>
                        @else
                            <!-- Non-admin users use the original search -->
                            <div class="mb-6">
                                <label for="recipients" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Para *
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Busca por nombre, email o apellido
                                </p>
                                <div id="recipientsContainer" class="relative mt-3">
                                    <input
                                        type="text"
                                        id="recipientSearch"
                                        placeholder="Buscar destinatarios..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                                        autocomplete="off"
                                    >
                                    <div id="recipientSuggestions" class="hidden absolute top-full left-0 right-0 z-10 mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg max-h-96 overflow-y-auto"></div>
                                </div>
                                <div id="selectedRecipients" class="mt-3 flex flex-wrap gap-2"></div>
                            </div>
                        @endif

                        <!-- Hidden recipient IDs input - Used by both admin and non-admin users -->
                        <input type="hidden" name="recipient_ids" id="recipientIds" value="">

                        <!-- Selected Recipients Display - Only for admin users -->
                        @if(auth()->user()->isAdmin())
                            <div class="mb-6" x-show="selectedRecipients.length > 0" style="display: none;">
                                <div class="flex items-center gap-2 rounded-md bg-blue-50 p-3 dark:bg-blue-900/20">
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900 dark:text-blue-300">
                                        <span x-text="selectedRecipients.length"></span> destinatario(s) seleccionado(s)
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <template x-for="recipient in selectedRecipients" :key="recipient.id">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-sm font-medium dark:bg-blue-900/30 dark:text-blue-300">
                                            <span x-text="recipient.name"></span>
                                            <button
                                                type="button"
                                                @click="removeRecipient(recipient.id)"
                                                class="text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-100">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        @endif

                        <!-- Asunto -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Asunto *
                            </label>
                            <input
                                type="text"
                                name="subject"
                                id="subject"
                                value="{{ old('subject') }}"
                                placeholder="Asunto del mensaje"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                            >
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mensaje -->
                        <div class="mb-6">
                            <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Mensaje *
                            </label>
                            <textarea
                                name="body"
                                id="body"
                                rows="10"
                                placeholder="Escribe tu mensaje aquí..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                            >{{ old('body') }}</textarea>
                            @error('body')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Enviar
                            </button>
                            <a
                                href="{{ route('messages.inbox') }}"
                                class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 dark:focus:ring-offset-gray-800"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // For non-admin users, keep the original search functionality
        @if(!auth()->user()->isAdmin())
        document.addEventListener('DOMContentLoaded', function() {
            let selectedRecipients = new Map();
            const recipientSearch = document.getElementById('recipientSearch');
            const recipientSuggestions = document.getElementById('recipientSuggestions');
            const selectedRecipientsContainer = document.getElementById('selectedRecipients');
            const recipientIds = document.getElementById('recipientIds');

            function renderSelectedRecipients() {
                selectedRecipientsContainer.innerHTML = Array.from(selectedRecipients.entries()).map(([id, name]) => {
                    return `
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-sm font-medium dark:bg-blue-900/30 dark:text-blue-300">
                            ${name}
                            <button type="button" onclick="removeRecipient('${id}')" class="text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    `;
                }).join('');

                recipientIds.value = Array.from(selectedRecipients.keys()).map(id => id.toString()).join(',');
            }

            window.addRecipient = function(id, name) {
                if (!selectedRecipients.has(id)) {
                    selectedRecipients.set(id, name);
                    renderSelectedRecipients();
                    recipientSearch.value = '';
                    recipientSuggestions.classList.add('hidden');
                }
            };

            window.removeRecipient = function(id) {
                selectedRecipients.delete(id);
                renderSelectedRecipients();
            };

            recipientSearch.addEventListener('input', async (e) => {
                const query = e.target.value.trim();

                if (query.length < 2) {
                    recipientSuggestions.classList.add('hidden');
                    return;
                }

                try {
                    const url = `{{ route('api.messages.search') }}?q=${encodeURIComponent(query)}`;
                    const response = await fetch(url);

                    if (!response.ok) {
                        recipientSuggestions.classList.add('hidden');
                        return;
                    }

                    const results = await response.json();

                    if (!results || results.length === 0) {
                        recipientSuggestions.classList.add('hidden');
                        return;
                    }

                    recipientSuggestions.innerHTML = results.map(item => {
                        const fullName = item.full_name || item.name;
                        const encodedFullName = encodeURIComponent(JSON.stringify(fullName));

                        return `
                            <div class="flex items-center justify-between px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition" data-user-id="${item.id}" data-user-name="${encodedFullName}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${item.email || ''}</p>
                                </div>
                                <span class="ml-2 inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 whitespace-nowrap">${item.role}</span>
                            </div>
                        `;
                    }).join('');

                    recipientSuggestions.querySelectorAll('[data-user-id]').forEach(el => {
                        el.addEventListener('click', function() {
                            const id = parseInt(this.dataset.userId);
                            const name = JSON.parse(decodeURIComponent(this.dataset.userName));
                            addRecipient(id, name);
                        });
                    });

                    recipientSuggestions.classList.remove('hidden');
                } catch (error) {
                    console.error('Error searching recipients:', error);
                    recipientSuggestions.classList.add('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#recipientsContainer')) {
                    recipientSuggestions.classList.add('hidden');
                }
            });
        });
        @endif
    </script>
    @endpush
</x-app-layout>
