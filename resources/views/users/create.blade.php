<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Create User') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Create a new user by filling out the form below.') }}
        </p>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"
                    class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <!-- Name -->
                    <div class="flex flex-col gap-1">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input name="name" id="name" class="w-full" value="{{ old('name') }}"
                            required />
                        <x-input-error :messages="$errors->store->get('name')" />
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-1">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="w-full" :value="old('email')"
                            required autocomplete="username" />
                        <x-input-error :messages="$errors->store->get('email')" />
                    </div>

                    <!-- Role -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <x-input-label for="role" :value="__('Role')" />
                        <x-select-input name="role" :options="$roles" :value="old('role')" label="{{ __('Role') }}"
                            required />
                        <x-input-error :messages="$errors->store->get('role')" />
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <x-input-label for="description" :value="__('Description')" />
                        <x-textarea-input id="description" name="description">
                            {{ old('description') }}
                        </x-textarea-input>
                        <x-input-error :messages="$errors->store->get('description')" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-2 md:col-span-2 pt-2">
                        <x-secondary-button x-data=""
                            x-on:click.prevent="window.location.href='{{ route('users.index') }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button>{{ __('Create') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
