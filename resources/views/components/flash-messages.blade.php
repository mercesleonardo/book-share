@props([
    'class' => '',
    'timeout' => 5000,
    // variant: 'inline' ou 'floating'
    'variant' => 'inline',
    // position somente para floating: top-right | top-left | top-center
    'position' => 'top-right',
    // size: normal | compact
    'size' => 'normal',
])

@php
    $status = session('status');
    $success = session('success');
    $error = session('error');
    // Permite arrays ou strings
    $toCollection = function($value) {
        if(!$value) { return collect(); }
        return collect(is_array($value) ? $value : [$value]);
    };
    $statusList  = $toCollection($status);
    $successList = $toCollection($success);
    $errorList   = $toCollection($error);
    $errorsBag = $errors ?? null;
@endphp

@php
    if ($variant === 'floating') {
        $pos = match($position) {
            'top-left' => 'top-4 left-4 items-start',
            'top-center' => 'top-4 inset-x-0 items-center',
            default => 'top-4 right-4 items-end', // top-right
        };
        $wrapperBase = 'pointer-events-none fixed z-50 flex flex-col space-y-2 '.$pos;
    } else {
        $wrapperBase = 'space-y-3';
    }
    $wrapperClasses = $wrapperBase.' '.$class;

    $groups = [
        [
            'list' => $statusList,
            'color' => 'blue',
            'icon' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8.75-3a.75.75 0 100 1.5.75.75 0 000-1.5zM9 9.75A.75.75 0 019.75 9h.5a.75.75 0 01.75.75v4a.75.75 0 01-.75.75h-.5A.75.75 0 019 13.75v-4z" clip-rule="evenodd" /></svg>',
        ],
        [
            'list' => $successList,
            'color' => 'green',
            'icon' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
        ],
        [
            'list' => $errorList,
            'color' => 'red',
            'icon' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.59c.75 1.334-.213 2.987-1.742 2.987H3.48c-1.53 0-2.492-1.653-1.742-2.988l6.52-11.589zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-.25-6.25a.75.75 0 00-1.5 0v3.5a.75.75 0 001.5 0v-3.5z" clip-rule="evenodd" /></svg>',
        ],
    ];

    // Tamanho
    // Compact agora com um pouco mais de altura (py-2 ao invés de py-1.5)
    $padding = $size === 'compact' ? 'pl-3 pr-8 py-2 text-xs' : 'pl-4 pr-10 py-2 text-sm';
    $iconSizeReplace = $size === 'compact' ? ['h-5 w-5' => 'h-4 w-4'] : [];
@endphp

<div {{ $attributes->class($wrapperClasses) }}>
    @foreach($groups as $group)
        @foreach($group['list'] as $msg)
            <div
                x-data="{show:true}"
                x-init="setTimeout(()=>show=false, {{ (int)$timeout }})"
                x-show="show"
                x-transition:enter="transform ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                role="alert"
                aria-live="assertive"
                class="pointer-events-auto relative w-full max-w-sm sm:max-w-md rounded-md border border-{{ $group['color'] }}-300/50 bg-{{ $group['color'] }}-50 dark:bg-{{ $group['color'] }}-500/10 {{ $padding }} text-{{ $group['color'] }}-700 dark:text-{{ $group['color'] }}-300 shadow-sm backdrop-blur flex items-start gap-2">
                <span class="mt-0.5 shrink-0 text-{{ $group['color'] }}-500 dark:text-{{ $group['color'] }}-300" aria-hidden="true">{!! $group['icon'] !!}</span>
                <div class="flex-1 leading-relaxed">{{ $msg }}</div>
                <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-{{ $group['color'] }}-500 hover:text-{{ $group['color'] }}-700 dark:text-{{ $group['color'] }}-300 dark:hover:text-{{ $group['color'] }}-100" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endforeach
    @endforeach

    @if($errorsBag && $errorsBag->any())
        <div
            x-data="{show:true}"
            x-show="show"
            x-transition:enter="transform ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transform ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            role="alert"
            aria-live="assertive"
            class="pointer-events-auto relative w-full max-w-sm sm:max-w-md rounded-md border border-red-300/50 bg-red-50 dark:bg-red-500/10 {{ $size === 'compact' ? 'px-3 py-2 text-xs' : 'px-4 py-3 text-sm' }} text-red-700 dark:text-red-300 space-y-1 shadow-sm backdrop-blur">
            <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-100" aria-label="{{ __('Close') }}">&times;</button>
            <div class="flex items-start gap-2">
                <span class="mt-0.5 text-red-500 dark:text-red-300" aria-hidden="true"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.59c.75 1.334-.213 2.987-1.742 2.987H3.48c-1.53 0-2.492-1.653-1.742-2.988l6.52-11.589zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-.25-6.25a.75.75 0 00-1.5 0v3.5a.75.75 0 001.5 0v-3.5z" clip-rule="evenodd" /></svg></span>
                <div class="flex-1">
                    <div class="font-semibold">{{ __('validation.errors_title') }}</div>
                    <ul class="mt-1 list-disc ml-5 space-y-0.5">
                        @foreach($errorsBag->all() as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
