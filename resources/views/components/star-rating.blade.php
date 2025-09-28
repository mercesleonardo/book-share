@props([
    'name' => 'rating',
    'value' => null, // current numeric value (1..5 or nullable)
    'max' => 5,
    'half' => true, // enable half stars
    'size' => '7', // tailwind size (w-? h-?)
])

@php
    $current = (float) ($value ?? 0);
    $steps = $half ? $max * 2 : $max; // half steps if enabled
@endphp

<div x-data="{
        value: {{ $current }},
        hover: 0,
        set(val){ this.value = val; },
        label(val){ return val.toFixed(1).replace('.0',''); }
    }" class="flex items-center gap-1 select-none" x-id="['star-rating']">
    <input type="hidden" name="{{ $name }}" :value="value" />
    <template x-for="i in {{ $steps }}" :key="i">
        <button type="button"
            x-on:mouseenter="hover = i / {{ $half ? 2 : 1 }}"
            x-on:mouseleave="hover = 0"
            x-on:click="set(i / {{ $half ? 2 : 1 }})"
            class="group focus:outline-none"
            :title="label(i / {{ $half ? 2 : 1 }})">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="w-{{ $size }} h-{{ $size }} transition-colors"
                 :class="{
                    'text-yellow-400 drop-shadow': (hover ? (i/{{ $half ? 2 : 1 }}) <= hover : (i/{{ $half ? 2 : 1 }}) <= value),
                    'text-gray-300 dark:text-gray-600': (hover ? (i/{{ $half ? 2 : 1 }}) > hover : (i/{{ $half ? 2 : 1 }}) > value)
                 }">
                <defs>
                    <!-- Optionally gradient for half fill (not required since we duplicate steps for halves) -->
                </defs>
                <path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" />
            </svg>
        </button>
    </template>
    <span class="ml-1 text-xs text-gray-500 dark:text-gray-400" x-text="value ? value.toFixed(1).replace('.0','') + '/' + {{ $max }} : ''"></span>
</div>
