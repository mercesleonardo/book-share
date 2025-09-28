@props([
    'categories' => collect(),
    'users' => collect(),
    'activeFilters' => collect(),
])
<form method="GET" action="{{ route('posts.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <div class="flex flex-col gap-1">
        <x-input-label for="q" :value="__('posts.filters.search')" />
        <x-text-input id="q" name="q" value="{{ request('q') }}"
            placeholder="{{ __('posts.filters.search_ph') }}" />
    </div>
    <div class="flex flex-col gap-1">
        <x-input-label for="book_author" :value="__('posts.fields.author')" />
        <x-text-input id="book_author" name="book_author" value="{{ request('book_author') }}" placeholder="{{ __('posts.filters.author_ph') }}" />
    </div>
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
    <div class="md:col-span-1 flex items-end gap-2">
        <x-primary-button>{{ __('posts.filters.filter') }}</x-primary-button>
        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.index') }}'">{{ __('posts.filters.reset') }}</x-secondary-button>
    </div>
</form>
