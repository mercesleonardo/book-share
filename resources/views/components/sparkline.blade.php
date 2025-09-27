@props([
    'data' => [], // array de inteiros
    'stroke' => '#2563eb',
    'height' => 40,
])
@php
    $count = count($data);
    $width = max($count - 1, 1) * 8; // 8px por ponto
    $max = max($data ?: [1]);
    $points = [];
    foreach($data as $i => $v) {
        $x = $i * 8;
        $y = $max > 0 ? $height - (($v / $max) * ($height - 4)) : $height; // padding top/bottom 2px
        $points[] = $x . ',' . round($y, 2);
    }
@endphp
<svg {{ $attributes->merge(['class' => 'w-full']) }} viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="none" height="{{ $height }}" width="{{ $width }}" role="img">
    @if($count > 1)
        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="{{ $stroke }}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
    @elseif($count === 1)
        <circle cx="0" cy="{{ $height/2 }}" r="2" fill="{{ $stroke }}" />
    @endif
</svg>
