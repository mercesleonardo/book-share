<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <x-primary-button x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'create-user')">
                            {{ __('Create User') }}
                        </x-primary-button>
                        <x-modal name="create-user" :show="$errors->store->isNotEmpty()" focusable>
                            <div class="p-6">
                                @include('users.partials.create-modal', ['roles' => $roles])
                            </div>
                        </x-modal>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 rounded shadow">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Avatar') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Name') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Email') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Role') }}</th>
                                    <th class="py-2 px-4 border-b text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                        <td class="py-2 px-4 border-b">
                                            <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.svg') }}"
                                                alt="Avatar" class="w-10 h-10 rounded-full object-cover border" />
                                        </td>
                                        <td class="py-2 px-4 border-b font-medium">{{ $user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $user->role->label() }}
                                        </td>
                                        <td class="py-2 px-4 border-b space-x-2">
                                            <x-primary-button x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'edit-user-{{ $user->id }}')">
                                                {{ __('Edit') }}
                                            </x-primary-button>
                                            <x-modal name="edit-user-{{ $user->id }}" :show="$errors->update->isNotEmpty() &&
                                                session('failed_user_id') === $user->id" focusable>
                                                <div class="p-6">
                                                    @include('users.partials.edit-modal', [
                                                        'user' => $user,
                                                        'roles' => $roles,
                                                    ])
                                                </div>
                                            </x-modal>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">
                                            {{ __('No users found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
