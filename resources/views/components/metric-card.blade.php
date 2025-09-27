<div {{ $attributes->merge(['class' => 'p-4 bg-white dark:bg-gray-800 rounded shadow flex flex-col']) }}>
    <span class="text-xs font-medium tracking-wide uppercase text-gray-500 dark:text-gray-400">{{ $label }}</span>
    <span class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</span>
    @if(isset($slot) && trim($slot) !== '')
        <div class="mt-2 text-xs">{{ $slot }}</div>
    @endif
</div>
