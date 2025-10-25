<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Redactar Mensaje
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6">
                    <form action="{{ route('messages.store') }}" method="POST" id="messageForm">
                        @csrf

                        <!-- Para (Destinatarios) -->
                        <div class="mb-6">
                            <label for="recipients" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Para *
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Busca por nombre, email o apellido. También puedes escribir:
                                <br>
                                • <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-gray-600 dark:text-gray-300">all:teachers</code> para todos los maestros
                                <br>
                                • <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-gray-600 dark:text-gray-300">all:parents</code> para todos los padres
                                <br>
                                • <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-gray-600 dark:text-gray-300">group:</code> para ver grupos disponibles
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
                            <input type="hidden" name="recipient_ids" id="recipientIds" value="">

                            @if($errors->has('recipient_ids'))
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    @foreach($errors->get('recipient_ids') as $message)
                                        {{ $message }}<br>
                                    @endforeach
                                </p>
                            @endif
                        </div>

                        <!-- Toggle para mostrar destinatarios seleccionados -->
                        <div class="mb-6 flex items-center gap-2 rounded-md bg-blue-50 p-3 dark:bg-blue-900/20">
                            <input type="checkbox" id="showRecipients" checked class="h-4 w-4 text-blue-600 rounded border-gray-300 dark:border-gray-600">
                            <label for="showRecipients" class="text-sm font-medium text-blue-900 dark:text-blue-300">
                                Mostrar destinatarios
                            </label>
                        </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            let selectedRecipients = new Map();
            const recipientSearch = document.getElementById('recipientSearch');
            const recipientSuggestions = document.getElementById('recipientSuggestions');
            const selectedRecipientsContainer = document.getElementById('selectedRecipients');
            const recipientIds = document.getElementById('recipientIds');
            const showRecipientsCheckbox = document.getElementById('showRecipients');

            // Verify elements exist
            if (!recipientSearch || !recipientSuggestions) {
                console.error('Required elements not found');
                return;
            }

            // Render selected recipients function
            function renderSelectedRecipients() {
                selectedRecipientsContainer.innerHTML = Array.from(selectedRecipients.entries()).map(([id, name]) => {
                    const isGroup = typeof id === 'string';
                    const badgeColor = isGroup ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';

                    return `
                        <span class="inline-flex items-center gap-1 rounded-full ${badgeColor} px-3 py-1 text-sm font-medium">
                            ${isGroup ? '📋 ' : ''}${name}
                            <button type="button" onclick="removeRecipient('${id}')" class="${isGroup ? 'text-green-700 hover:text-green-900 dark:text-green-300 dark:hover:text-green-100' : 'text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-100'}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    `;
                }).join('');

                recipientIds.value = Array.from(selectedRecipients.keys())
                    .map(id => typeof id === 'string' ? id : id.toString())
                    .join(',');
            }

            // Add individual recipient
            window.addRecipient = function(id, name) {
                console.log('Adding recipient:', id, name);
                if (!selectedRecipients.has(id)) {
                    selectedRecipients.set(id, name);
                    renderSelectedRecipients();
                    recipientSearch.value = '';
                    recipientSuggestions.classList.add('hidden');
                }
            };

            // Add group of recipients
            window.addRecipientGroup = function(groupId, groupName) {
                console.log('Adding group:', groupId, groupName);
                if (!selectedRecipients.has(groupId)) {
                    selectedRecipients.set(groupId, groupName);
                    renderSelectedRecipients();
                    recipientSearch.value = '';
                    recipientSuggestions.classList.add('hidden');
                }
            };

            // Remove recipient or group
            window.removeRecipient = function(id) {
                console.log('Removing:', id);
                selectedRecipients.delete(id);
                renderSelectedRecipients();
            };

            // Search for recipients
            recipientSearch.addEventListener('input', async (e) => {
                const query = e.target.value.trim();
                console.log('Searching for:', query);

                if (query.length < 2) {
                    recipientSuggestions.classList.add('hidden');
                    return;
                }

                try {
                    const url = `{{ route('api.messages.search') }}?q=${encodeURIComponent(query)}`;
                    console.log('Fetching from:', url);

                    const response = await fetch(url);

                    if (!response.ok) {
                        console.error('API returned status:', response.status);
                        recipientSuggestions.classList.add('hidden');
                        return;
                    }

                    const results = await response.json();
                    console.log('Search results:', results);

                    if (!results || results.length === 0) {
                        console.log('No results found');
                        recipientSuggestions.classList.add('hidden');
                        return;
                    }

                    recipientSuggestions.innerHTML = results.map(item => {
                        const fullName = item.full_name || item.name;
                        const encodedFullName = encodeURIComponent(JSON.stringify(fullName));

                        if (item.type === 'group') {
                            // Group item styling
                            return `
                                <div class="flex items-center justify-between px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition bg-blue-50/50 dark:bg-blue-900/10" data-group-id="${item.id}" data-group-name="${encodedFullName}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</p>
                                    </div>
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-200 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-700 dark:text-blue-200 whitespace-nowrap">
                                        ${item.role}
                                    </span>
                                </div>
                            `;
                        } else {
                            // Regular user item styling
                            return `
                                <div class="flex items-center justify-between px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition" data-user-id="${item.id}" data-user-name="${encodedFullName}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${fullName}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${item.email || ''}</p>
                                    </div>
                                    <span class="ml-2 inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        ${item.role}
                                    </span>
                                </div>
                            `;
                        }
                    }).join('');

                    // Add event listeners to suggestion items
                    recipientSuggestions.querySelectorAll('[data-user-id]').forEach(el => {
                        el.addEventListener('click', function() {
                            const id = parseInt(this.dataset.userId);
                            const name = JSON.parse(decodeURIComponent(this.dataset.userName));
                            addRecipient(id, name);
                        });
                    });

                    recipientSuggestions.querySelectorAll('[data-group-id]').forEach(el => {
                        el.addEventListener('click', function() {
                            const id = this.dataset.groupId;
                            const name = JSON.parse(decodeURIComponent(this.dataset.groupName));
                            addRecipientGroup(id, name);
                        });
                    });

                    recipientSuggestions.classList.remove('hidden');
                } catch (error) {
                    console.error('Error searching recipients:', error);
                    recipientSuggestions.classList.add('hidden');
                }
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#recipientsContainer')) {
                    recipientSuggestions.classList.add('hidden');
                }
            });

            // Toggle show recipients
            if (showRecipientsCheckbox) {
                showRecipientsCheckbox.addEventListener('change', (e) => {
                    selectedRecipientsContainer.classList.toggle('hidden', !e.target.checked);
                });
            }

            console.log('Recipient search initialized');
        });
    </script>
    @endpush
</x-app-layout>
