@extends('layouts.app')

@section('title', 'Contacto | Refugio Gastronómico')

@section('content')
<section class="rg-contact-hero rg-contact-hero--birds-dark">
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

    <div class="rg-contact-hero-overlay" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="rg-contact-hero-title">¡Hola! ¿En qué te podemos ayudar?</h1>
        <p class="rg-contact-hero-subtitle">Encuentra el canal adecuado o escríbenos directamente.</p>
    </div>
    <div
        class="rg-contact-hero-divider"
        style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
        aria-hidden="true"
    ></div>
</section>

<x-contact-blocks :blocks="$contactBlocks" class="rg-contact-blocks--page" />

<section class="rg-contact-form-section">
    <div class="container-refugio">
        <div class="rg-contact-form-layout" data-aos="fade-up">
            <div class="rg-contact-form-intro">
                <h2 class="rg-contact-form-heading">Envíanos un mensaje</h2>
                <p class="rg-contact-form-lead">
                    Si no encontraste lo que buscabas arriba, completa el formulario y nuestro equipo te responderá pronto.
                </p>
                <ul class="rg-contact-form-meta">
                    @if($visit->address)
                        <li><strong>Ubicación:</strong> {{ $visit->address }}</li>
                    @endif
                    @if($visit->email)
                        <li>
                            <strong>Email:</strong>
                            <a href="mailto:{{ $visit->email }}">{{ $visit->email }}</a>
                        </li>
                    @endif
                    @if($visit->phone_reservations)
                        <li><strong>Reservas:</strong> {{ $visit->phone_reservations }}</li>
                    @endif
                    @if($visit->phone_events)
                        <li><strong>Eventos:</strong> {{ $visit->phone_events }}</li>
                    @endif
                </ul>
            </div>

            <x-inquiry-form :title="null" :subtitle="null" />
        </div>
    </div>
</section>

<x-visit-us />
@endsection
