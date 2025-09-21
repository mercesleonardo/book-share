@props([
    'name',
    'id' => null,
    'options' => [], // array de objetos ou arrays com value/label
    'value' => null, // valor selecionado
    'label' => null,
    'required' => false,
    'class' => ''
])

<div>
    <select name="{{ $name }}" id="{{ $id ?? $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100 ' . $class]) }}>
        @foreach ($options as $option)
            <option value="{{ is_object($option) ? $option->value : $option['value'] }}"
                {{ (old($name, $value) == (is_object($option) ? $option->value : $option['value'])) ? 'selected' : '' }}>
                {{ is_object($option) ? ($option->label() ?? $option->value) : ($option['label'] ?? $option['value']) }}
            </option>
        @endforeach
    </select>
</div>
