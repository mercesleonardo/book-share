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
                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success" dismissible>
                                {{ session('success') }}
                            </x-alert>
                        </div>
                    @endif

                    <div class="mb-6 flex flex-col gap-4">
                        {{-- Filtros de busca --}}
                        <x-users-filter :roles="$roles" :name="request('name')" :role="request('role')" />

                        {{-- Botão criar --}}
                        <div>
                            <x-create-button x-data="" x-on:click.prevent="window.location.href='{{ route('users.create') }}'">
                                {{ __('Create User') }}
                            </x-create-button>
                        </div>
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
                                        <td class="py-2 px-4 border-b {{ $user->role->color() }}">{{ $user->role->label() }}</td>
                                        <td class="py-2 px-4 border-b space-x-2">
                                            @if ($user->trashed())
                                                <form method="POST" action="{{ route('users.restore', $user->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-primary-button>{{ __('Restore') }}</x-primary-button>
                                                </form>
                                            @else
                                                @can('update', $user)
                                                    <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ route('users.edit', $user->id) }}'">{{ __('Edit') }}</x-secondary-button>
                                                @endcan
                                                @can('delete', $user)
                                                    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-user-{{ $user->id }}')">{{ __('Delete') }}</x-danger-button>
                                                    <x-modal name="delete-user-{{ $user->id }}" :show="false" focusable>
                                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="p-6">
                                                            @csrf
                                                            @method('DELETE')
                                                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Delete User') }}</h2>
                                                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ __('Once the user is deleted, all of related data will be permanently removed.') }}</p>
                                                            <div class="mt-6 flex justify-end gap-2">
                                                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                                                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-500">
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
