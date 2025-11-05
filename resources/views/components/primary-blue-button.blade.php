@props(['type' => 'button', 'href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-primary-blue hover:bg-primary-blue shadow-sm']) }} style="background-color: #1e40af;">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-primary-blue hover:bg-primary-blue shadow-sm']) }} style="background-color: #1e40af;">
        {{ $slot }}
    </button>
@endif


