<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('posts.actions.create') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('posts.intro.create_hint') }}</p>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="flex flex-col gap-1">
                        <x-input-label for="title" :value="__('posts.fields.title')" />
                        <x-text-input id="title" name="title" class="w-full" value="{{ old('title') }}" required />
                        <x-input-error :messages="$errors->store->get('title')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="book_author" :value="__('posts.fields.author')" />
                        <x-text-input id="book_author" name="book_author" class="w-full" value="{{ old('book_author') }}" />
                        <x-input-error :messages="$errors->store->get('book_author')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="category_id" :value="__('posts.fields.category')" />
                        <x-select-input name="category_id" :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])" :value="old('category_id')" required />
                        <x-input-error :messages="$errors->store->get('category_id')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="description" :value="__('posts.fields.description')" />
                        <x-textarea-input id="description" name="description">{{ old('description') }}</x-textarea-input>
                        <x-input-error :messages="$errors->store->get('description')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="user_rating" value="{{ __('posts.fields.user_rating') }}" />
                        <x-star-rating name="user_rating" :value="old('user_rating', 5)" :half="false" />
                        <x-input-error :messages="$errors->store->get('user_rating')" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-input-label for="image" :value="__('posts.fields.image')" />
                        <input id="image" name="image" type="file" accept="image/*" class="w-full text-sm text-gray-700 dark:text-gray-200" />
                        <x-input-error :messages="$errors->store->get('image')" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('posts.index') }}'">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Create') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
