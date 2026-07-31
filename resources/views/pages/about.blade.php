@extends('layouts.app')

@section('title', 'Visítanos | Refugio Gastronómico')

@php
    $address = $visit->address ?: 'Av. Javier Prado Este 4492 – Santiago de Surco';
    $scheduleLines = collect($visit->schedule ?? [])
        ->map(function ($row) {
            $days = trim((string) ($row['days'] ?? ''));
            $hours = trim((string) ($row['hours'] ?? ''));
            if ($days === '' || $hours === '') {
                return null;
            }

            return $days.' – '.$hours;
        })
        ->filter()
        ->values()
        ->all();

    if ($scheduleLines === []) {
        $scheduleLines = [
            'Domingo a Miércoles – 8 am a 10 pm',
            'Jueves – 8 am a 12 am',
            'Viernes y Sábado – 8 am a 1 am',
        ];
    }

    $mapEmbed = $visit->map_embed_url
        ?: 'https://maps.google.com/maps?q=-12.0842658,-76.9734978&z=16&hl=es&t=m&output=embed';
    $directionsUrl = 'https://www.google.com/maps/place/Av.+Javier+Prado+Este+4492,+Santiago+de+Surco+15023/@-12.0842605,-76.9756865,17z/data=!3m1!4b1!4m5!3m4!1s0x9105c772e0a01d4f:0xd86c503827f051af!8m2!3d-12.0842658!4d-76.9734978';
@endphp

@section('content')
{{-- Hero --}}
<section
    class="rg-visit-hero"
    style="background-image: url('{{ asset('images/refugio/bg-nosotros.png') }}');"
>
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-visit-hero-deco rg-visit-hero-deco--right"
        aria-hidden="true"
    >
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-visit-hero-deco rg-visit-hero-deco--bottom-left"
        aria-hidden="true"
    >
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-visit-hero-deco rg-visit-hero-deco--bottom-right"
        aria-hidden="true"
    >

    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="rg-visit-hero-title">
            <span>Conoce más</span><br>sobre nosotros
        </h1>
    </div>
</section>

{{-- Divisor hojas --}}
<div
    class="rg-visit-leaf-divider"
    style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
    aria-hidden="true"
></div>

{{-- Mapa estático + tarjeta Ubícanos --}}
<section class="rg-visit-map-section">
    <div class="rg-visit-map-frame" aria-hidden="true">
        <iframe
            src="{{ $mapEmbed }}"
            class="rg-visit-map"
            loading="lazy"
            tabindex="-1"
            inert
            referrerpolicy="no-referrer-when-downgrade"
            title=""
        ></iframe>
        <div class="rg-visit-map-shield"></div>
    </div>

    <div
        class="rg-visit-card"
        style="background-image: url('{{ asset('images/refugio/bg-1-nosotros.png') }}');"
        data-aos="fade-up"
    >
        <h2 class="rg-visit-card-title">Ubícanos</h2>

        <div class="rg-visit-card-body">
            <p class="rg-visit-card-label">Puedes encontrarnos en:</p>
            <ul class="rg-visit-card-list">
                <li>{{ $address }}</li>
            </ul>

            <p class="rg-visit-card-label">Horario de atención:</p>
            <ul class="rg-visit-card-list">
                @foreach($scheduleLines as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>

        <a
            href="{{ $directionsUrl }}"
            class="rg-visit-directions btn-cta"
            target="_blank"
            rel="noopener noreferrer"
        >
            ¿Cómo llegar?
        </a>
    </div>
</section>
@endsection
