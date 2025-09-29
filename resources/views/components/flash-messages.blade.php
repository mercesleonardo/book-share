@props(['class' => ''])

@php
    $status = session('status');
    $success = session('success');
    $error = session('error');
    $errorsBag = $errors ?? null;
@endphp

<div {{ $attributes->class('space-y-3 '.$class) }}>
    @if($status)
        <div class="rounded-md border border-blue-300/50 bg-blue-50 dark:bg-blue-500/10 px-4 py-2 text-sm text-blue-700 dark:text-blue-300">
            {{ $status }}
        </div>
    @endif
    @if($success)
        <div class="rounded-md border border-green-300/50 bg-green-50 dark:bg-green-500/10 px-4 py-2 text-sm text-green-700 dark:text-green-300">
            {{ $success }}
        </div>
    @endif
    @if($error)
        <div class="rounded-md border border-red-300/50 bg-red-50 dark:bg-red-500/10 px-4 py-2 text-sm text-red-700 dark:text-red-300">
            {{ $error }}
        </div>
    @endif
    @if($errorsBag && $errorsBag->any())
        <div class="rounded-md border border-red-300/50 bg-red-50 dark:bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300 space-y-1">
            <div class="font-semibold">{{ __('Validation errors:') }}</div>
            <ul class="list-disc ml-5">
                @foreach($errorsBag->all() as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>