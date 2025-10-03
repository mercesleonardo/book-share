<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Edit user's information below.") }}
        </p>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <x-alert type="success" dismissible>
                    {{ session('success') }}
                </x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" dismissible>
                    {{ session('error') }}
                </x-alert>
            @endif
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                    class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    @method('PATCH')

                    <!-- Name -->
                    <div class="flex flex-col gap-1">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input name="name" id="name" class="w-full"
                            value="{{ old('name', $user->name) }}" required />
                        <x-input-error :messages="$errors->update->get('name')" />
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-1">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="w-full" :value="old('email', $user->email)"
                            required autocomplete="username" />
                        <x-input-error :messages="$errors->update->get('email')" />
                    </div>

                    <!-- Role -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <x-input-label for="role" :value="__('Role')" />
                        <x-select-input name="role" :options="$roles" :value="old('role', $user->role->value)" label="{{ __('Role') }}"
                            required />
                        <x-input-error :messages="$errors->update->get('role')" />
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <x-input-label for="description" :value="__('Description')" />
                        <x-textarea-input id="description" name="description">
                            {{ old('description', $user->description) }}
                        </x-textarea-input>
                        <x-input-error :messages="$errors->update->get('description')" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-4 md:gap-6 md:col-span-2">
                        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('admin.users.index') }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
