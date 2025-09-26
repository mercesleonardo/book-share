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

                    <div class="mb-6 flex justify-between items-center">
                        <x-create-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.create') }}'">
                            {{ __('posts.actions.create') }}
                        </x-create-button>
                    </div>

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
                                            @if($post->image)
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="h-12 w-12 rounded object-cover" />
                                            @else
                                                <div class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-500">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b font-medium">{{ $post->title }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $post->category?->name }}</td>
                                        <td class="py-2 px-4 border-b text-sm">{{ $post->user?->name }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex flex-wrap gap-2">
                                            @can('update', $post)
                                                <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.edit', $post) }}'">{{ __('Edit') }}</x-secondary-button>
                                            @endcan
                                            @can('delete', $post)
                                                <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-post-{{ $post->id }}')">{{ __('Delete') }}</x-danger-button>
                                                <x-modal name="delete-post-{{ $post->id }}" :show="false" focusable>
                                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="p-6">
                                                        @csrf
                                                        @method('DELETE')
                                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('posts.actions.delete') }}</h2>
                                                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ __('posts.messages.confirm_delete') }}</p>
                                                        <div class="mt-6 flex justify-end gap-2">
                                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                                            <x-danger-button type="submit">{{ __('posts.actions.delete') }}</x-danger-button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">{{ __('posts.messages.not_found') }}</td>
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
