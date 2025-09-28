<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('posts.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                        </div>
                    @endif

                    <div class="mb-6 flex flex-col gap-4">
                        <x-posts-filter :categories="$categories" :users="$users" />
                        <div class="flex justify-between items-center">
                            <x-create-button x-data=""
                                x-on:click.prevent="window.location.href='{{ route('posts.create') }}'">
                                {{ __('posts.actions.create') }}
                            </x-create-button>
                        </div>
                    </div>

                    @php
                        $activeFilters = collect([
                            'q' => request('q'),
                            'book_author' => request('book_author'),
                            'category' => request('category'),
                            'user' => request('user'),
                            'status' => request('status'),
                        ])->filter(fn($v) => filled($v));
                    @endphp

                    @if($activeFilters->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach($activeFilters as $key => $value)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                    <span>
                                        @switch($key)
                                            @case('q') {{ __('posts.filters.search') }}: {{ $value }} @break
                                            @case('book_author') {{ __('posts.fields.author') }}: {{ $value }} @break
                                            @case('category') {{ __('posts.fields.category') }}: {{ optional($categories->firstWhere('id', (int)$value))->name }} @break
                                            @case('user') {{ __('posts.fields.user') }}: {{ optional($users->firstWhere('id', (int)$value))->name }} @break
                                            @case('status') {{ __('posts.meta.status') }}: {{ ucfirst($value) }} @break
                                        @endswitch
                                    </span>
                                    <a class="hover:text-red-600 dark:hover:text-red-400" href="{{ route('posts.index', request()->except($key)) }}" title="{{ __('posts.filters.remove_filter') }}">&times;</a>
                                </span>
                            @endforeach
                            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">{{ __('posts.filters.clear_all') }}</a>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 rounded shadow">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">{{ __('posts.fields.image') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('posts.fields.title') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('posts.fields.category') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('posts.fields.author') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($posts as $post)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                        <td class="py-2 px-4 border-b">
                                            @if ($post->image)
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image"
                                                    class="h-12 w-12 rounded object-cover" />
                                            @else
                                                <div
                                                    class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-500">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b font-medium">{{ $post->title }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $post->category?->name }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $post->book_author }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex flex-wrap gap-2">
                                                <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.show', $post) }}'">{{ __('posts.actions.view') }}</x-secondary-button>
                                                @can('update', $post)
                                                    <x-secondary-button x-data=""
                                                        x-on:click.prevent="window.location.href='{{ route('posts.edit', $post) }}'">{{ __('posts.actions.edit') }}</x-secondary-button>
                                                @endcan
                                                @can('delete', $post)
                                                    <x-danger-button x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'delete-post-{{ $post->id }}')">{{ __('posts.actions.delete') }}</x-danger-button>
                                                    <x-modal name="delete-post-{{ $post->id }}" :show="false"
                                                        focusable>
                                                        <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                                            class="p-6">
                                                            @csrf
                                                            @method('DELETE')
                                                            <h2
                                                                class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                                {{ __('posts.actions.delete') }}</h2>
                                                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                                                {{ __('posts.messages.confirm_delete') }}</p>
                                                            <div class="mt-6 flex justify-end gap-2">
                                                                <x-secondary-button type="button"
                                                                    x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                                                <x-danger-button
                                                                    type="submit">{{ __('posts.actions.delete') }}</x-danger-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">
                                            {{ __('posts.messages.not_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $posts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
