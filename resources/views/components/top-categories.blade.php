@props([
    'rows',
    'total' => 0,
])
<div class="bg-white dark:bg-gray-800 rounded shadow p-6">
    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">{{ __('dashboard.sections.top_categories') }}</h3>
    <ul class="space-y-2">
        @forelse($rows as $row)
            <li class="flex items-center justify-between text-sm">
                <span class="max-w-[10rem] truncate text-gray-700 dark:text-gray-300" title="{{ $row->category?->name ?? __('(sem categoria)') }}">{{ $row->category?->name ?? __('(sem categoria)') }}</span>
                <span class="font-semibold tabular-nums text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    {{ $row->total }}
                    @if($total > 0)
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ number_format(($row->total / $total) * 100, 1) }}%)</span>
                    @endif
                </span>
            </li>
        @empty
            <li class="text-xs text-gray-500">{{ __('dashboard.sections.no_categories_data') }}</li>
        @endforelse
    </ul>
</div>
