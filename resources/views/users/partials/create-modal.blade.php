<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Create User') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Create a new user by filling out the form below.') }}
        </p>
    </header>
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input name="name" id="name" class="w-full" value="{{ old('name') }}" required />
            <x-input-error class="mt-2" :messages="$errors->store->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->store->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input name="password" id="password" type="password" class="mt-1 block w-full" required
                autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->store->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input name="password_confirmation" id="password_confirmation" type="password"
                class="mt-1 block w-full" required autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->store->get('password_confirmation')" />
        </div>

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <x-select-input name="role" :options="$roles" :value="old('role')" label="{{ __('Role') }}" required />
            <x-input-error class="mt-2" :messages="$errors->store->get('role')" />
        </div>

        <div class="flex justify-end gap-2">
            <x-secondary-button type="button" x-on:click.prevent="$dispatch('close'); window.location.reload()">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button>{{ __('Create') }}</x-primary-button>
        </div>
    </form>
</section>
