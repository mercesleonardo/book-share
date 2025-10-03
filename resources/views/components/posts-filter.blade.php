@props([
    'categories' => collect(),
    'users' => collect(),
    'activeFilters' => collect(),
    // New optional props for reuse outside admin
    'action' => null, // form action URL
    'resetUrl' => null, // reset button URL
    'showAuthor' => true, // toggle author input
    'showUser' => true, // toggle user select
    'cols' => 5, // md:grid-cols-N
])
@php
    $colsInt = (int) ($cols ?? 5);
    if (! in_array($colsInt, [1, 2, 3, 4, 5, 6], true)) {
        $colsInt = 5;
    }
    $mdColsClass = 'md:grid-cols-' . $colsInt;
@endphp
<form method="GET" action="{{ $action ?? route('admin.posts.index') }}" class="grid grid-cols-1 {{ $mdColsClass }} gap-4 items-end">
    <div class="flex flex-col gap-1">
        <x-input-label for="q" :value="__('posts.filters.search')" />
        <x-text-input id="q" name="q" value="{{ request('q') }}"
            placeholder="{{ __('posts.filters.search_ph') }}" />
    </div>
    @if($showAuthor)
        <div class="flex flex-col gap-1">
            <x-input-label for="book_author" :value="__('posts.fields.author')" />
            <x-text-input id="book_author" name="book_author" value="{{ request('book_author') }}" placeholder="{{ __('posts.filters.author_ph') }}" />
        </div>
    @endif
    <div class="flex flex-col gap-1">
        <x-input-label for="category" :value="__('posts.fields.category')" />
        <select id="category" name="category"
            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- {{ __('posts.filters.all') }} --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    @if($showUser)
        @php($auth = auth()->user())
        @if (
            $auth &&
                in_array($auth->role->value, [\App\Enums\UserRole::ADMIN->value, \App\Enums\UserRole::MODERATOR->value], true))
            <div class="flex flex-col gap-1">
                <x-input-label for="user" :value="__('posts.fields.user')" />
                <select id="user" name="user"
                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- {{ __('posts.filters.all') }} --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    @endif
    <div class="md:col-span-1 flex items-end gap-2">
        <x-primary-button>{{ __('posts.filters.filter') }}</x-primary-button>
        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ $resetUrl ?? route('admin.posts.index') }}'">{{ __('posts.filters.reset') }}</x-secondary-button>
        @if($activeFilters->isNotEmpty())
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                {{ $activeFilters->count() }}
            </span>
        @endif
    </div>
</form>
