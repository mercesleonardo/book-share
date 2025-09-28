@props([
    'filters' => collect(), // Illuminate\Support\Collection|string[]
    'route' => null, // required route name
    'categories' => collect(),
    'users' => collect(),
])

@if($filters instanceof Illuminate\Support\Collection ? $filters->isNotEmpty() : !empty($filters))
    @php($filters = $filters instanceof Illuminate\Support\Collection ? $filters : collect($filters))
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach($filters as $key => $value)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                <span>
                    @switch($key)
                        @case('q') {{ __('posts.filters.search') }}: {{ $value }} @break
                        @case('book_author') {{ __('posts.fields.author') }}: {{ $value }} @break
                        @case('category') {{ __('posts.fields.category') }}: {{ optional($categories->firstWhere('id', (int) $value))->name }} @break
                        @case('user') {{ __('posts.fields.user') }}: {{ optional($users->firstWhere('id', (int) $value))->name }} @break
                        @case('status') {{ __('posts.meta.status') }}: {{ ucfirst($value) }} @break
                        @case('name') {{ __('Name') }}: {{ $value }} @break
                        @case('role') {{ __('Role') }}: {{ ucfirst($value) }} @break
                        @case('only_trashed') {{ 'Removed' }} @break
                        @case('category_single') {{ __('categories.single') }}: {{ optional($categories->firstWhere('id', (int) $value))->name }} @break
                        @default {{ ucfirst(str_replace('_',' ',$key)) }}: {{ $value }}
                    @endswitch
                </span>
                <a class="hover:text-red-600 dark:hover:text-red-400" href="{{ route($route, request()->except($key)) }}" title="{{ __('posts.filters.remove_filter') }}">&times;</a>
            </span>
        @endforeach
        <a href="{{ route($route) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">{{ __('posts.filters.clear_all') }}</a>
    </div>
@endif
