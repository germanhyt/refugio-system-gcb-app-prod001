@php
    $visit = $visitInfo ?? \App\Models\VisitInfo::current();
    $address = $visit->address ?: 'Av. Javier Prado Este 4492';
    $scheduleRows = collect($visit->schedule ?? [])
        ->map(function ($row) {
            $days = trim((string) ($row['days'] ?? ''));
            $hours = trim((string) ($row['hours'] ?? ''));
            if ($days === '' || $hours === '') {
                return null;
            }

            return ['days' => $days, 'hours' => $hours];
        })
        ->filter()
        ->values()
        ->all();

    if ($scheduleRows === []) {
        $scheduleRows = [
            ['days' => 'Domingo a Miércoles', 'hours' => 'Hasta las 10 p.m.'],
            ['days' => 'Jueves', 'hours' => 'Hasta la 1:00 p.m.'],
            ['days' => 'Viernes y sábado', 'hours' => 'Hasta las 3:00 p.m.'],
            ['days' => 'Música en vivo', 'hours' => 'Jueves a viernes, desde las 8:00 p.m.'],
        ];
    }

    $mapEmbed = $visit->mapEmbedUrl();
@endphp

<section class="rg-visit-us" {{ $attributes }}>
    <div class="rg-visit-us-map" aria-hidden="true">
        <iframe
            data-visit-map
            data-src="{{ $mapEmbed }}"
            src="about:blank"
            class="rg-visit-us-iframe"
            loading="lazy"
            tabindex="-1"
            title="Mapa Refugio Gastronómico"
        ></iframe>
    </div>

    <div class="container-refugio relative z-10">
        <div class="rg-visit-us-card">
            <h2 class="rg-visit-us-title">¿Nos visitas?</h2>
            <p class="rg-visit-us-address">
                Nos encuentras en <strong>{{ $address }}</strong>
            </p>
            <p class="rg-visit-us-hours-label"><strong>Horarios de atención:</strong> ¡Abrimos todos los días!</p>
            <ul class="rg-visit-us-hours">
                @foreach($scheduleRows as $row)
                    <li>
                        <strong>{{ $row['days'] }}:</strong>
                        <span>{{ $row['hours'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
