<img
    src="{{ asset('images/logo-dark.png') }}"
    alt="{{ config('app.name') }} logo"
    {{ $attributes->merge(['class' => 'hidden dark:block h-12 w-auto']) }}
>
<img
    src="{{ asset('images/logo-light.png') }}"
    alt="{{ config('app.name') }} logo"
    {{ $attributes->merge(['class' => 'block dark:hidden h-12 w-auto']) }}
>
