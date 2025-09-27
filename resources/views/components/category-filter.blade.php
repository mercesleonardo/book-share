@props(['categories' => collect()])

<form method="GET" action="{{ route('categories.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div class="flex flex-col gap-1">
        <x-input-label for="category" :value="__('posts.fields.category')" />
        <select id="category" name="category"
            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- {{ __('categories.filters.all') }} --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-1 flex items-end gap-2">
        <x-primary-button>{{ __('categories.filters.filter') }}</x-primary-button>
        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.index') }}'">{{ __('categories.filters.reset') }}</x-secondary-button>
    </div>
</form>
