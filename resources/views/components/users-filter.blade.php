@props([
    'roles' => [],
    'name' => null,
    'role' => null,
    'action' => null,
    'activeFilters' => collect(),
])

<form method="GET" action="{{ $action ?? route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <div class="md:col-span-2">
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" class="w-full" value="{{ $name }}" />
    </div>
    <div class="md:col-span-1">
        <x-input-label for="role" :value="__('Role')" />
        <x-select-input id="role" name="role" :options="$roles" :value="$role" label="{{ __('Role') }}" :placeholder="__('All')" />
    </div>
    <div class="md:col-span-1 flex items-end gap-2">
        <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ $action ?? route('admin.users.index') }}'">{{ __('Clear') }}</x-secondary-button>
        @if($activeFilters->isNotEmpty())
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                {{ $activeFilters->count() }}
            </span>
        @endif
    </div>
</form>
