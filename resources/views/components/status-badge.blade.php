@props([
    'status' => null,
    'showApproved' => false, // se false, oculta badge quando status = approved
])
@php
    $enum = $status instanceof \App\Enums\ModerationStatus
        ? $status
        : (\App\Enums\ModerationStatus::tryFrom($status ?? 'pending') ?? \App\Enums\ModerationStatus::Pending);
    $statusValue = $enum->value;
@endphp
@if($statusValue !== 'approved' || $showApproved)
    <span {{ $attributes->merge(['class' => "px-2 py-0.5 rounded text-[10px] font-medium tracking-wide uppercase {$enum->color()}"]) }}>
        {{ $enum->label() }}
    </span>
@endif
