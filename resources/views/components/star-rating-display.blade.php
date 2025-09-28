@props([
    'value' => 0, // número (float ou int)
    'max' => 5,
    'precision' => 1, // casas decimais exibidas ao lado, se quiser
    'showValue' => true,
])

@php
    $val = (float) $value;
    $full = (int) round($val, 0, PHP_ROUND_HALF_DOWN); // apenas estrelas inteiras (trunca)
@endphp

<div class="inline-flex items-center gap-1 select-none" aria-label="{{ $val }} / {{ $max }}">
    <div class="flex items-center">
        @for($i=1;$i<=$max;$i++)
            @php($state = $i <= $full ? 'full' : 'empty')
            <span class="w-4 h-4 inline-block">
                @if($state==='full')
                    <svg viewBox="0 0 24 24" class="w-4 h-4 text-yellow-400" fill="currentColor" aria-hidden="true"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/></svg>
                @else
                    <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" aria-hidden="true"><path d="M12 2l2.317 5.573 5.997.478-4.57 3.914 1.395 5.846L12 15.902l-5.139 3.909 1.395-5.846L3.686 8.05l5.997-.478z"/></svg>
                @endif
            </span>
        @endfor
    </div>
    @if($showValue)
        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ number_format($val, $precision, ',', '.') }}</span>
    @endif
</div>
