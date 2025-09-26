<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('posts.actions.edit') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-1">
                        <x-input-label for="title" :value="__('posts.fields.title')" />
                        <x-text-input id="title" name="title" class="w-full"
                            value="{{ old('title', $post->title) }}" required />
                        <div class="text-xs text-gray-500 dark:text-gray-400">Slug: {{ $post->slug }}</div>
                        <x-input-error :messages="$errors->get('title')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="category_id" :value="__('posts.fields.category')" />
                        <x-select-input name="category_id" :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])" :value="old('category_id', $post->category_id)" required />
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="description" :value="__('posts.fields.description')" />
                        <x-textarea-input id="description"
                            name="description">{{ old('description', $post->description) }}
                        </x-textarea-input>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="image" :value="__('posts.fields.image')" />
                        @if ($post->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="Current"
                                    class="h-20 rounded object-cover" />
                            </div>
                        @endif
                        <input id="image" name="image" type="file" accept="image/*"
                            class="w-full text-sm text-gray-700 dark:text-gray-200" />
                        <x-input-error :messages="$errors->get('image')" />
                    </div>
                    <div class="flex items-center justify-end gap-4 md:gap-6 md:col-span-2">
                        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.index') }}'">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
