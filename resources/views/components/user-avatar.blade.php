@props(['user', 'size' => 'md', 'class' => ''])

@if($user)
    @php
        $sizeClasses = match($size) {
            'xs' => 'h-6 w-6 text-xs',
            'sm' => 'h-8 w-8 text-sm',
            'md' => 'h-10 w-10',
            'lg' => 'h-12 w-12 text-lg',
            'xl' => 'h-16 w-16 text-xl',
            default => 'h-10 w-10',
        };

        // Generar color basado en ID para consistencia
        $colors = [
            'bg-red-500',
            'bg-blue-500',
            'bg-green-500',
            'bg-yellow-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500',
            'bg-cyan-500',
            'bg-teal-500',
            'bg-orange-500',
        ];
        $colorIndex = ($user->id % count($colors));
        $color = $colors[$colorIndex];

        // Obtener iniciales del nombre
        $initials = collect(explode(' ', $user->name))
            ->take(2)
            ->map(fn($part) => strtoupper($part[0] ?? ''))
            ->implode('');
    @endphp

    <div class="{{ $sizeClasses }} {{ $color }} rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0 {{ $class }}"
         title="{{ $user->name }}">
        {{ $initials ?: '?' }}
    </div>
@else
    @php
        $sizeClasses = match($size) {
            'xs' => 'h-6 w-6 text-xs',
            'sm' => 'h-8 w-8 text-sm',
            'md' => 'h-10 w-10',
            'lg' => 'h-12 w-12 text-lg',
            'xl' => 'h-16 w-16 text-xl',
            default => 'h-10 w-10',
        };
    @endphp

    <div class="{{ $sizeClasses }} bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-semibold flex-shrink-0 {{ $class }}"
         title="Usuario desconocido">
        ?
    </div>
@endif

