@props([
    'type' => 'info', // success | error | warning | info
    'dismissible' => false,
    'timeout' => 3000,
])

@php
    $colors = [
        'success' => 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-200 dark:border-green-800',
        'error' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-200 dark:border-red-800',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-200 dark:border-yellow-800',
        'info' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800',
    ];

    $iconPaths = [
        'success' => 'M4.5 12.75l6 6 9-13.5',
        'error' => 'M6 18L18 6M6 6l12 12',
        'warning' => 'M12 9v3m0 3h.01M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9z',
        'info' => 'M13 16h-1v-4h-1m2-4h.01M12 3a9 9 0 110 18 9 9 0 010-18z',
    ];

    $colorClass = $colors[$type] ?? $colors['info'];
    $iconPath = $iconPaths[$type] ?? $iconPaths['info'];
@endphp

<div {{ $attributes->merge(['class' => "border rounded-md {$colorClass} p-4 flex items-start gap-3"]) }}
    @if($dismissible) x-data="{ open: true }" x-init="setTimeout(() => open = false, {{ $timeout }})" x-show="open" @endif>
    <div class="shrink-0 mt-0.5">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="{{ $iconPath }}" />
        </svg>
    </div>
    <div class="grow text-sm">
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" class="shrink-0 text-current/60 hover:text-current" x-on:click="open=false" aria-label="Dismiss">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
