<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Edit User') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Edit user's information below.") }}
        </p>
    </header>


    <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('put')

        <div>
            <x-input-label for="name-{{ $user->id }}" :value="__('Name')" />
            <x-text-input id="name-{{ $user->id }}" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->update->get('name')" />
        </div>

        <div>
            <x-input-label for="email-{{ $user->id }}" :value="__('Email')" />
            <x-text-input id="email-{{ $user->id }}" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->update->get('email')" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Role')" />
            <x-select-input name="role" :options="$roles" :value="old('role', $user->role->value)" label="{{ __('Role') }}" required />
            <x-input-error class="mt-2" :messages="$errors->update->get('role')" />
        </div>

        <div class="flex justify-between items-center gap-2">
            <div>
                <x-danger-button type="button" x-on:click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')">
                    {{ __('Delete account') }}
                </x-danger-button>
            </div>

            <div class="flex justify-end gap-2">
                <x-secondary-button type="button" x-on:click.prevent="$dispatch('close'); window.location.reload()">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                @endif
            </div>
        </div>
    </form>

    <!-- Confirm Delete User Modal -->
    <x-modal name="confirm-user-deletion-{{ $user->id }}" :show="false" focusable>
        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
            </p>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button type="submit">
                    {{ __('Delete') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
