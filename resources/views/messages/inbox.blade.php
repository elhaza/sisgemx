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
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($conversations as $conversation)
                            @php
                                $messageRecipient = $conversation->recipients->first();
                                $isRead = $messageRecipient && $messageRecipient->isRead();
                                $otherUser = $conversation->sender_id === auth()->id()
                                    ? ($conversation->recipients->first()?->recipient)
                                    : $conversation->sender;
                            @endphp

                            <a href="{{ route('messages.show', $conversation) }}" class="block transition hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-start gap-4 p-4">
                                    <div class="flex-shrink-0">
                                        @if(!$isRead && $conversation->sender_id !== auth()->id())
                                            <div class="h-3 w-3 rounded-full bg-blue-600"></div>
                                        @else
                                            <div class="h-3 w-3 rounded-full bg-transparent"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium {{ !$isRead && $conversation->sender_id !== auth()->id() ? 'font-bold' : '' }} text-gray-900 dark:text-gray-100 truncate">
                                                {{ $otherUser->name ?? 'Desconocido' }}
                                            </p>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $conversation->created_at->format('d M') }}
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm {{ !$isRead && $conversation->sender_id !== auth()->id() ? 'font-semibold' : 'font-normal' }} text-gray-700 dark:text-gray-300 truncate">
                                            {{ $conversation->subject }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                            {{ Str::limit(strip_tags($conversation->body), 100) }}
                                        </p>
                                    </div>

                                    @if(!$isRead && $conversation->sender_id !== auth()->id())
                                        <div class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                                            Nuevo
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay mensajes</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Comienza por <a href="{{ route('messages.create') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">enviar un nuevo mensaje</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
