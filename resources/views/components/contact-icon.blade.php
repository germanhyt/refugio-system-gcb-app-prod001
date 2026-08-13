@props(['title' => ''])

@php
    $normalized = \Illuminate\Support\Str::lower(trim((string) $title));
    $paths = [
        'espacios publicitarios y comerciales' => 'M3.75 9.25v5.5c0 .69.56 1.25 1.25 1.25h2.1l3.4 2.85V6.4L7.1 9.25H5c-.69 0-1.25.56-1.25 1.25Zm11.02-.9a.75.75 0 0 1 1.06.1 6.25 6.25 0 0 1 0 7.1.75.75 0 1 1-1.16-.95 4.75 4.75 0 0 0 0-5.2.75.75 0 0 1 .1-1.05Zm2.3-2.35a.75.75 0 0 1 1.05.15 9.5 9.5 0 0 1 0 11.7.75.75 0 1 1-1.2-.9 8 8 0 0 0 0-9.9.75.75 0 0 1 .15-1.05Z',
        'espacios publicitarios' => 'M3.75 9.25v5.5c0 .69.56 1.25 1.25 1.25h2.1l3.4 2.85V6.4L7.1 9.25H5c-.69 0-1.25.56-1.25 1.25Zm11.02-.9a.75.75 0 0 1 1.06.1 6.25 6.25 0 0 1 0 7.1.75.75 0 1 1-1.16-.95 4.75 4.75 0 0 0 0-5.2.75.75 0 0 1 .1-1.05Zm2.3-2.35a.75.75 0 0 1 1.05.15 9.5 9.5 0 0 1 0 11.7.75.75 0 1 1-1.2-.9 8 8 0 0 0 0-9.9.75.75 0 0 1 .15-1.05Z',
        'espacios comerciales' => 'M4 5h7v7H4V5Zm9 0h7v4h-7V5ZM4 14h7v5H4v-5Zm9-3h7v8h-7v-8Z',
        'servicio al cliente' => 'M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9Zm0 4a2 2 0 1 1-2 2 2 2 0 0 1 2-2Zm0 12a6.5 6.5 0 0 1-5.2-2.6c.1-1.7 3.5-2.6 5.2-2.6s5.1.9 5.2 2.6A6.5 6.5 0 0 1 12 19Z',
        '¡trabaja con nosotros!' => 'M8 7V5a4 4 0 1 1 8 0v2h2v12H6V7h2Zm2-2a2 2 0 1 1 4 0v2h-4V5Z',
        'trabaja con nosotros' => 'M8 7V5a4 4 0 1 1 8 0v2h2v12H6V7h2Zm2-2a2 2 0 1 1 4 0v2h-4V5Z',
    ];
    $path = $paths[$normalized] ?? 'M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9Zm1 4v4h4v-2h-2V7h-2Z';
@endphp

<span class="rg-contact-block-icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
        <path d="{{ $path }}"/>
    </svg>
</span>
