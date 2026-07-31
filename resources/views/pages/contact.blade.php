@extends('layouts.app')

@section('title', 'Contacto | Refugio Gastronómico')

@section('content')
<section class="relative bg-[var(--color-primary)] pb-16 pt-32 text-white">
    <div class="container-refugio text-center">
        <h1 class="font-display text-4xl font-bold tablet:text-5xl">¡Hola! ¿En qué te podemos ayudar?</h1>
    </div>
</section>

<section class="py-[var(--section-gap)]">
    <div class="container-refugio grid gap-10 desktop:grid-cols-2">
        <div>
            <h2 class="font-display text-3xl font-bold">Ubícanos</h2>
            <p class="mt-4 text-lg">{{ $visit->address }}</p>

            <h3 class="mt-8 font-semibold uppercase tracking-wide text-[var(--color-accent)]">Horario de atención</h3>
            <ul class="mt-3 space-y-2 text-[var(--color-text-body)]">
                @foreach($visit->schedule ?? [] as $row)
                    <li>{{ $row['days'] ?? '' }} – {{ $row['hours'] ?? '' }}</li>
                @endforeach
            </ul>

            <div class="mt-8 space-y-3 text-sm">
                @if($visit->phone_reservations)
                    <p><span class="font-semibold">Reservas:</span> {{ $visit->phone_reservations }}</p>
                @endif
                @if($visit->phone_events)
                    <p><span class="font-semibold">Eventos:</span> {{ $visit->phone_events }}</p>
                @endif
                @if($visit->email)
                    <p><span class="font-semibold">Email:</span> <a class="text-[var(--color-accent)]" href="mailto:{{ $visit->email }}">{{ $visit->email }}</a></p>
                @endif
            </div>

            @if(!empty($visit->amenities))
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach($visit->amenities as $amenity)
                        <span class="rounded-full bg-[var(--color-surface-alt)] px-3 py-1 text-xs font-semibold">{{ $amenity }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-card bg-[var(--color-surface-alt)] min-h-[320px]">
            @if($visit->map_embed_url)
                <iframe src="{{ $visit->map_embed_url }}" class="h-full min-h-[320px] w-full border-0" loading="lazy" allowfullscreen title="Mapa Refugio"></iframe>
            @else
                <iframe
                    src="https://maps.google.com/maps?q=Av.%20Javier%20Prado%20Este%204492%20Surco&t=&z=15&ie=UTF8&iwloc=&output=embed"
                    class="h-full min-h-[320px] w-full border-0"
                    loading="lazy"
                    allowfullscreen
                    title="Mapa Refugio"
                ></iframe>
            @endif
        </div>
    </div>
</section>

@if($ctaBlocks->isNotEmpty())
    <x-cta-triple :blocks="$ctaBlocks" />
@endif
@endsection
