@if ($logoPath)
    <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-auto {{ $height }}">
@elseif ($fallbackImage)
    <img src="{{ $fallbackImage }}" alt="Logo" class="w-auto {{ $height }}">
@else
    <x-application-logo class="{{ $height }} w-auto fill-current text-gray-500" />
@endif