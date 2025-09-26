<x-app-layout>
    <x-slot name="header">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('categories.actions.create') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
                    @csrf
                    <div class="flex flex-col gap-1">
                        <x-input-label for="name" :value="__('categories.fields.name')" />
                        <x-text-input id="name" name="name" class="w-full" value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('categories.index') }}'">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Create') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
