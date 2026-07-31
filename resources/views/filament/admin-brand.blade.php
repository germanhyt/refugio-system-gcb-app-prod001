@php
    $logoUrl = $logoUrl ?? asset('images/refugio/logo-v2.svg');
@endphp

<div class="flex items-center gap-3">
    <img
        src="{{ $logoUrl }}"
        alt="Refugio Gastronómico"
        class="h-10 w-auto shrink-0 object-contain"
    />
    <span class="text-base font-semibold tracking-tight text-gray-950 dark:text-white whitespace-nowrap">
        Panel Refugio
    </span>
</div>
