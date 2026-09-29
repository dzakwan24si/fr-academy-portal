@props([
    'show' => 'false',
    'close' => null,
    'title' => 'Modal Title',
    'subtitle' => null,
    'maxWidth' => '3xl',
    'headerBg' => 'bg-green-600',
    'titleColor' => 'text-white',
    'subtitleColor' => 'text-green-100',
    'closeBtnColor' => 'text-white hover:text-green-200',
    'initiallyOpen' => false
])

@php
    $closeAction = $close ?? $show . ' = false';
    $maxWidthClass = match($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        'full' => 'sm:max-w-[95%]',
        default => 'sm:max-w-3xl',
    };
@endphp

<div x-show="{{ $show }}" class="fixed inset-0 z-50 overflow-y-auto" @if(!$initiallyOpen) style="display: none;" x-cloak @endif>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="{{ $show }}" 
             @if(!$initiallyOpen) x-transition.opacity.duration.300ms @endif
             class="fixed inset-0 bg-black/40 backdrop-blur-md" 
             aria-hidden="true"
             @click="{{ $closeAction }}">
        </div>
        <div x-show="{{ $show }}" @if(!$initiallyOpen) x-transition.scale @endif class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle {{ $maxWidthClass }} w-full">
            
            <div class="{{ $headerBg }} px-4 sm:px-8 py-5 flex justify-between items-start border-b border-gray-100">
                <div>
                    @if(isset($titleHtml))
                        <h3 class="text-xl font-bold {{ $titleColor }}" {!! $titleHtml !!}></h3>
                    @else
                        <h3 class="text-xl font-bold {{ $titleColor }}">{{ $title }}</h3>
                    @endif
                    
                    @if(isset($subtitleHtml))
                        <p class="text-sm {{ $subtitleColor }} mt-1" {!! $subtitleHtml !!}></p>
                    @elseif($subtitle)
                        <p class="text-sm {{ $subtitleColor }} mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" @click="{{ $closeAction }}" class="{{ $closeBtnColor }} outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="bg-white px-4 sm:px-8 py-6">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
