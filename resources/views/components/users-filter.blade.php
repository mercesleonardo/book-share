@props([
    'roles' => [],
    'name' => null,
    'role' => null,
    'action' => null,
])

<form method="GET" action="{{ $action ?? route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <div class="md:col-span-2">
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" class="w-full" value="{{ $name }}" />
    </div>
    <div class="md:col-span-1">
        <x-input-label for="role" :value="__('Role')" />
        <x-select-input id="role" name="role" :options="$roles" :value="$role" label="{{ __('Role') }}" :placeholder="__('Select') . '…'" />
    </div>
    <div class="md:col-span-1 flex items-end gap-2">
        <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
        <x-secondary-button x-data="" x-on:click.prevent="window.location.href='{{ $action ?? route('users.index') }}'">{{ __('Clear') }}</x-secondary-button>
    </div>
</form>
