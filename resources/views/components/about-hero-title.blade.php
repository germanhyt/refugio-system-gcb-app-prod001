@props([
    'title' => null,
])

@php
    $raw = trim((string) ($title ?: "¿Quiénes\nSomos?"));
    $parts = preg_split('/\R/u', $raw, 2) ?: [$raw];
@endphp

<h1 {{ $attributes->merge(['class' => 'rg-visit-hero-title']) }}>
    @if (count($parts) > 1)
        <span>{{ $parts[0] }}</span><br>{{ $parts[1] }}
    @else
        {{ $raw }}
    @endif
</h1>
