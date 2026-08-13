@props([
    'eyebrow' => '¡Hola! Somos Refugio Gastronómico.',
    'headline' => null,
    'body' => null,
    'cornerDeco' => null,
    'topRibbon' => false,
])

@php
    $visit = $visitInfo ?? \App\Models\VisitInfo::current();
    $raw = $body ?? $visit->about_content ?? '';
    $parts = preg_split("/\n\s*\n/", trim((string) $raw), 2) ?: [''];
    $headline = $headline ?: trim($parts[0] ?? 'TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.');
    $paragraph = trim($parts[1] ?? (count($parts) === 1 && ! str_contains($headline, 'TODO LO QUE')
        ? (string) $raw
        : 'Refugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.'));
    if ($paragraph === $headline) {
        $paragraph = 'Refugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.';
    }
@endphp

<section @class(['rg-hola-section', 'rg-section--decorated' => filled($cornerDeco), 'rg-hola-section--top-ribbon' => $topRibbon]) {{ $attributes }}>
    @if($topRibbon)
        @once
            <style>
                .rg-hola-section--top-ribbon { padding-top: 0 !important; }
                .rg-hola-leaf-ribbon {
                    height: 48px !important;
                    margin-bottom: 1.5rem !important;
                    background-repeat: repeat-x !important;
                    background-position: center top !important;
                    background-size: auto 100% !important;
                    transform: scaleY(-1) !important;
                    pointer-events: none !important;
                }
            </style>
        @endonce
        <div
            class="rg-hola-leaf-ribbon"
            style="height:48px;margin-bottom:1.5rem;background-repeat:repeat-x;background-position:center top;background-size:auto 100%;transform:scaleY(-1);background-image:url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
            aria-hidden="true"
        ></div>
    @endif
    @if($cornerDeco)
        <x-section-corner-deco :position="$cornerDeco" />
    @endif
    <div class="container-refugio relative z-10 rg-hola-grid">
        <div class="rg-hola-left" data-aos="fade-up">
            <h2 class="rg-hola-eyebrow">{{ $eyebrow }}</h2>
        </div>
        <div class="rg-hola-right" data-aos="fade-up" data-aos-delay="80">
            <h3 class="rg-hola-headline">{{ $headline }}</h3>
            <p class="rg-hola-body">{{ $paragraph }}</p>
        </div>
    </div>
</section>
