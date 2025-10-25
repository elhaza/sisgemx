@if($unreadMessageCount > 0)
    <a href="{{ route('messages.inbox') }}" class="block overflow-hidden rounded-lg bg-blue-50 shadow-sm hover:shadow-md transition hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                            Mensajes no leídos
                        </p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                            {{ $unreadMessageCount }}
                        </p>
                    </div>
                </div>
                <svg class="h-6 w-6 text-blue-400 dark:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V16a1 1 0 11-2 0V5.414L5.707 10.707a1 1 0 01-1.414-1.414l6-6z" fill-rule="evenodd" />
                </svg>
            </div>
            <p class="mt-2 text-sm text-blue-600 dark:text-blue-300">
                Tienes {{ $unreadMessageCount }} mensaje{{ $unreadMessageCount !== 1 ? 's' : '' }} sin leer
            </p>
        </div>
    </a>
@endif
