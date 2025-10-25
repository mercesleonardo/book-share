@props(['provider' => 'google', 'label' => 'Sign in with Google'])

<a
    href="{{ route('auth.' . $provider . '.redirect') }}"
    role="button"
    aria-label="{{ $label }}"
    {{ $attributes->merge(['class' => 'w-full flex items-center justify-center gap-3 px-4 py-2 border rounded-md text-sm font-medium text-gray-700 dark:text-gray-100 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 shadow-sm transition']) }}
>
    <img src="{{ asset('build/images/google.svg') }}" alt="" class="h-5 w-5 mr-3" aria-hidden="true" />
    <span class="text-sm font-medium">{{ __($label) }}</span>
</a>
