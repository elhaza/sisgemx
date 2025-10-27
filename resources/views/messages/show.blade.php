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
                    @php
                        $isUserSender = $msg->sender_id === auth()->id();
                        $recipients = $msg->recipients->pluck('recipient');
                    @endphp
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg {{ $isUserSender ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-blue-500' }}">
                        <!-- Header with Sender Info -->
                        <div class="border-b border-gray-200 px-4 py-4 sm:px-6 {{ $isUserSender ? 'bg-green-50' : 'bg-blue-50' }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <x-user-avatar :user="$msg->sender" size="md" />
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $msg->sender->name }}
                                            </p>
                                            @if($isUserSender)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700 flex-shrink-0">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    Enviado por ti
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 flex-shrink-0">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m0 0L5 12m7-7l7 7" />
                                                    </svg>
                                                    Recibido
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $msg->created_at->format('d \d\e F, Y \a \l\a\s H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Recipients Info -->
                            @if($recipients->count() > 0)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">
                                        {{ $recipients->count() }} {{ $recipients->count() === 1 ? 'Destinatario' : 'Destinatarios' }}:
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($recipients as $recipient)
                                            <div class="flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs text-gray-700 border border-gray-200">
                                                <x-user-avatar :user="$recipient" size="xs" />
                                                <span>{{ $recipient->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Message Content -->
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
