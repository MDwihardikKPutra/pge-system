@props(['show', 'maxWidth' => 'max-w-2xl'])

<!-- Modal Background Overlay -->
<div x-show="{{ $show }}" 
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
    @click="{{ $show }} = false"
    style="display: none;"></div>

<!-- Modal Dialog -->
<div x-show="{{ $show }}" 
    x-cloak
    @keydown.escape.window="{{ $show }} = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div @click.away="{{ $show }} = false" class="bg-white rounded-lg shadow-xl {{ $maxWidth }} w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-5 sticky top-0 z-10 rounded-t-lg" style="background-color: #0a1628;">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        @if(isset($icon))
                        <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-10 rounded-lg flex items-center justify-center">
                            {{ $icon }}
                        </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ $title ?? 'Modal Title' }}
                            </h3>
                            @if(isset($subtitle))
                            <p class="text-sm text-gray-300 mt-0.5">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <button @click="{{ $show }} = false" class="text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg p-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            <!-- Modal Footer (Optional) -->
            @if(isset($footer))
            <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>


