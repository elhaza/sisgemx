<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Mensajes
                </h2>
                @if($unreadCount > 0)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Tienes <span class="font-semibold text-blue-600">{{ $unreadCount }}</span> mensaje(s) no leído(s)
                    </p>
                @endif
            </div>
            <a href="{{ route('messages.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Mensaje
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                @if($conversations->count() > 0)
                    <!-- Search Bar -->
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-700/50">
                        <div class="relative">
                            <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <form method="GET" action="{{ route('messages.inbox') }}" class="flex gap-2">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Buscar por asunto, contenido o remitente..."
                                    value="{{ $searchQuery }}"
                                    class="w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                                >
                                @if($searchQuery)
                                    <a href="{{ route('messages.inbox') }}" class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                        Limpiar
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Action Toolbar -->
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-700/50">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id="select-all"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                    onchange="toggleAllCheckboxes(this)"
                                >
                                <span class="text-sm text-gray-600 dark:text-gray-400">Seleccionar todo</span>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    id="mark-read-btn"
                                    class="inline-flex items-center gap-1 rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                    disabled
                                    onclick="bulkAction('mark-read')"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Marcar como leído
                                </button>
                                <button
                                    type="button"
                                    id="mark-unread-btn"
                                    class="inline-flex items-center gap-1 rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                    disabled
                                    onclick="bulkAction('mark-unread')"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Marcar como no leído
                                </button>
                                <button
                                    type="button"
                                    id="delete-btn"
                                    class="inline-flex items-center gap-1 rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                    disabled
                                    onclick="bulkAction('delete')"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($conversations as $conversation)
                            @php
                                $messageRecipient = $conversation->recipients->first();
                                $isRead = $messageRecipient && $messageRecipient->isRead();
                                $otherUser = $conversation->sender_id === auth()->id()
                                    ? ($conversation->recipients->first()?->recipient)
                                    : $conversation->sender;
                            @endphp

                            <div class="message-row group flex items-start gap-3 bg-white px-4 py-3 transition hover:bg-blue-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 {{ !$isRead && $conversation->sender_id !== auth()->id() ? 'bg-blue-50 dark:bg-gray-700/30' : '' }}">
                                <!-- Checkbox -->
                                <div class="flex-shrink-0 pt-1">
                                    <input
                                        type="checkbox"
                                        class="message-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                        value="{{ $conversation->id }}"
                                        onchange="updateActionButtons()"
                                    >
                                </div>

                                <!-- Unread Indicator -->
                                <div class="flex-shrink-0">
                                    @if(!$isRead && $conversation->sender_id !== auth()->id())
                                        <div class="h-3 w-3 rounded-full bg-blue-600"></div>
                                    @else
                                        <div class="h-3 w-3 rounded-full bg-transparent"></div>
                                    @endif
                                </div>

                                <!-- Message Content (Clickable) -->
                                <a href="{{ route('messages.show', $conversation) }}" class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm {{ !$isRead && $conversation->sender_id !== auth()->id() ? 'font-bold' : 'font-medium' }} text-gray-900 dark:text-gray-100 truncate">
                                            {{ $otherUser->name ?? 'Desconocido' }}
                                        </p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $conversation->created_at->format('d M') }}
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm {{ !$isRead && $conversation->sender_id !== auth()->id() ? 'font-semibold' : 'font-normal' }} text-gray-900 dark:text-gray-100 truncate">
                                        {{ $conversation->subject }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ Str::limit(strip_tags($conversation->body), 100) }}
                                    </p>
                                </a>

                                <!-- Status Badge and Actions -->
                                <div class="flex flex-shrink-0 items-center gap-2">
                                    @if(!$isRead && $conversation->sender_id !== auth()->id())
                                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                                            Nuevo
                                        </span>
                                    @endif

                                    <!-- Action Buttons (appear on hover) -->
                                    <div class="hidden gap-1 group-hover:flex">
                                        @if(!$isRead && $conversation->sender_id !== auth()->id())
                                            <form action="{{ route('messages.mark-as-read', $conversation) }}" method="POST" class="inline" onsubmit="return true;">
                                                @csrf
                                                <button type="submit" title="Marcar como leído" class="rounded p-1 text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('messages.mark-as-unread', $conversation) }}" method="POST" class="inline" onsubmit="return true;">
                                                @csrf
                                                <button type="submit" title="Marcar como no leído" class="rounded p-1 text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('messages.delete', $conversation) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este mensaje?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar" class="rounded p-1 text-red-500 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900/30">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            @if($searchQuery)
                                No hay mensajes que coincidan con tu búsqueda
                            @else
                                No hay mensajes
                            @endif
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Comienza por <a href="{{ route('messages.create') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">enviar un nuevo mensaje</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleAllCheckboxes(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.message-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateActionButtons();
        }

        function updateActionButtons() {
            const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const hasSelection = checkedBoxes.length > 0;

            document.getElementById('mark-read-btn').disabled = !hasSelection;
            document.getElementById('mark-unread-btn').disabled = !hasSelection;
            document.getElementById('delete-btn').disabled = !hasSelection;

            // Update select-all checkbox state
            const selectAllCheckbox = document.getElementById('select-all');
            const allCheckboxes = document.querySelectorAll('.message-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && allCheckboxes.length === checkedBoxes.length;
        }

        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            if (action === 'delete' && !confirm('¿Estás seguro de que deseas eliminar los mensajes seleccionados?')) {
                return;
            }

            // Submit each action individually
            checkedBoxes.forEach(checkbox => {
                const messageRow = checkbox.closest('.message-row');
                let form;

                if (action === 'mark-read') {
                    form = messageRow.querySelector('form[action*="mark-as-read"]');
                } else if (action === 'mark-unread') {
                    form = messageRow.querySelector('form[action*="mark-as-unread"]');
                } else if (action === 'delete') {
                    form = messageRow.querySelector('form[method="POST"][action*="messages"]');
                }

                if (form) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
