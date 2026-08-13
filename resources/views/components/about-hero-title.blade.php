@props([
    'title' => null,
])

@php
    $raw = trim((string) ($title ?: "¿Quiénes\nSomos?"));
    $parts = preg_split('/\R/u', $raw, 2) ?: [$raw];
@endphp

<h1 {{ $attributes->merge(['class' => 'rg-visit-hero-title']) }}>
    @if (count($parts) > 1)
        <span class="rg-visit-hero-title-lead">{{ $parts[0] }}</span>
        <span class="rg-visit-hero-title-main">{{ $parts[1] }}</span>
    @else
        <span class="rg-visit-hero-title-main">{{ $raw }}</span>
    @endif
</h1>
