<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $rootMessage->subject }}
                </h2>
            </div>
            <a
                href="{{ route('messages.inbox') }}"
                class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Thread messages -->
            <div class="space-y-4">
                @foreach($thread as $msg)
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <x-user-avatar :user="$msg->sender" size="md" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $msg->sender->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $msg->created_at->format('d \d\e F, Y \a \l\a\s H:i') }}
                                        </p>
                                    </div>
                                </div>
                                @if($msg->sender_id === auth()->id())
                                    <span class="inline-flex flex-shrink-0 items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 whitespace-nowrap">
                                        Tú
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="px-4 py-6 sm:px-6">
                            <div class="prose prose-sm max-w-none space-y-3 text-gray-700 break-words">
                                {!! nl2br(e($msg->body)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply form -->
            @can('reply', $rootMessage)
                <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 px-4 py-3 sm:px-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Responder
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <form action="{{ route('messages.reply', $rootMessage) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="reply_body" class="block text-sm font-medium text-gray-700">
                                    Tu respuesta *
                                </label>
                                <textarea
                                    name="body"
                                    id="reply_body"
                                    rows="6"
                                    placeholder="Escribe tu respuesta..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-4">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Responder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
            @cannot('reply', $rootMessage)
                <div class="mt-6 rounded-lg bg-yellow-50 p-4">
                    <p class="text-sm text-yellow-800">
                        No puedes responder a este mensaje.
                    </p>
                </div>
            @endcannot
        </div>
    </div>
</x-app-layout>
