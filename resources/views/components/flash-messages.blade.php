@props(['class' => '','timeout' => 5000])

@php
    $status = session('status');
    $success = session('success');
    $error = session('error');
    // Permite arrays ou strings
    $toCollection = function($value) {
        if(!$value) { return collect(); }
        return collect(is_array($value) ? $value : [$value]);
    };
    $statusList  = $toCollection($status);
    $successList = $toCollection($success);
    $errorList   = $toCollection($error);
    $errorsBag = $errors ?? null;
@endphp

<div {{ $attributes->class('space-y-3 '.$class) }}>
    @foreach($statusList as $msg)
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false, {{ (int)$timeout }})" x-show="show" x-transition.opacity.duration.200ms class="relative rounded-md border border-blue-300/50 bg-blue-50 dark:bg-blue-500/10 pl-4 pr-10 py-2 text-sm text-blue-700 dark:text-blue-300">
            <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100" aria-label="{{ __('Close') }}">&times;</button>
            {{ $msg }}
        </div>
    @endforeach
    @foreach($successList as $msg)
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false, {{ (int)$timeout }})" x-show="show" x-transition.opacity.duration.200ms class="relative rounded-md border border-green-300/50 bg-green-50 dark:bg-green-500/10 pl-4 pr-10 py-2 text-sm text-green-700 dark:text-green-300">
            <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-green-600 hover:text-green-800 dark:text-green-300 dark:hover:text-green-100" aria-label="{{ __('Close') }}">&times;</button>
            {{ $msg }}
        </div>
    @endforeach
    @foreach($errorList as $msg)
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false, {{ (int)$timeout }})" x-show="show" x-transition.opacity.duration.200ms class="relative rounded-md border border-red-300/50 bg-red-50 dark:bg-red-500/10 pl-4 pr-10 py-2 text-sm text-red-700 dark:text-red-300">
            <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-100" aria-label="{{ __('Close') }}">&times;</button>
            {{ $msg }}
        </div>
    @endforeach
    @if($errorsBag && $errorsBag->any())
        <div x-data="{show:true}" x-show="show" x-transition.opacity.duration.200ms class="relative rounded-md border border-red-300/50 bg-red-50 dark:bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300 space-y-1">
            <button type="button" @click="show=false" class="absolute top-1.5 right-1.5 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-100" aria-label="{{ __('Close') }}">&times;</button>
            <div class="font-semibold">{{ __('validation.errors_title') }}</div>
            <ul class="list-disc ml-5">
                @foreach($errorsBag->all() as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
