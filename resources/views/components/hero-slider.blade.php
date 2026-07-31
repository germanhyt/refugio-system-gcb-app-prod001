@props(['slides'])

@php
    $fallbackBg = asset('images/refugio/fondohome.jpg');
    $firstSlide = $slides->first();
    $slide1Bg = $firstSlide?->getFirstMediaUrl('background_image') ?: $fallbackBg;
@endphp

<section class="rg-hero relative min-h-screen overflow-hidden bg-[#1a1210]" id="top">
    <div class="swiper hero-swiper h-screen w-full" data-hero-swiper>
        <div class="swiper-wrapper">
            {{-- Slide 1: Propuestas gastronómicas --}}
            <div class="swiper-slide relative overflow-hidden">
                <div class="absolute inset-0">
                    <img
                        src="{{ $slide1Bg }}"
                        alt="Refugio Gastronómico"
                        class="hero-kenburns h-full w-full object-cover"
                        fetchpriority="high"
                    >
                    <div class="rg-hero-overlay absolute inset-0"></div>
                </div>
                <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
                    <div class="flex max-w-4xl flex-col items-center">
                        <img
                            src="{{ asset('images/refugio/hero-las-mejores.png') }}"
                            alt="Las mejores"
                            class="rg-hero-eyebrow"
                            data-hero-subtitle
                        >
                        <img
                            src="{{ asset('images/refugio/hero-propuestas.png') }}"
                            alt="Propuestas gastronómicas"
                            class="rg-hero-title-img"
                            data-hero-title
                        >
                        <div class="rg-hero-ctas" data-hero-cta>
                            <a href="{{ route('restaurants.index') }}" class="rg-hero-btn">
                                <img src="{{ asset('images/refugio/hero-btn-restaurantes.png') }}" alt="Restaurantes">
                            </a>
                            <a href="{{ route('events.index') }}" class="rg-hero-btn">
                                <img src="{{ asset('images/refugio/hero-btn-eventos.png') }}" alt="Eventos">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2: Ubicación / estacionamientos --}}
            <div class="swiper-slide rg-hero-location relative overflow-hidden">
                <img
                    src="{{ asset('images/refugio/hero-leaf-1.png') }}"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover"
                    loading="lazy"
                    aria-hidden="true"
                >

                <img src="{{ asset('images/refugio/hero-leaf-9.png') }}" alt="" class="rg-loc-deco rg-loc-deco-tr" aria-hidden="true">
                <img src="{{ asset('images/refugio/hero-leaf-4.png') }}" alt="" class="rg-loc-deco rg-loc-deco-bl" aria-hidden="true">
                <img src="{{ asset('images/refugio/hero-leaf-8.png') }}" alt="" class="rg-loc-deco rg-loc-deco-birds" aria-hidden="true">

                <div class="rg-hero-loc-grid" data-hero-title>
                    <div class="rg-loc-col rg-loc-col--parking">
                        <img src="{{ asset('images/refugio/hero-leaf-2.png') }}" alt="" class="rg-loc-icon" aria-hidden="true">
                        <p class="rg-loc-parking-text">¡Más de 500<br>estacionamientos<br>disponibles!</p>
                    </div>

                    <div class="rg-loc-col rg-loc-col--map">
                        <div class="rg-loc-map-wrap">
                            <img src="{{ asset('images/refugio/hero-leaf-5.png') }}" alt="Ubicación Refugio Gastronómico" class="rg-loc-map-img">
                        </div>
                    </div>

                    <div class="rg-loc-col rg-loc-col--address">
                        <img src="{{ asset('images/refugio/hero-leaf-6.png') }}" alt="" class="rg-loc-icon" aria-hidden="true">
                        <p class="rg-loc-address-title">Av. Javier Prado Este 4492<br>Surco</p>
                        <p class="rg-loc-address-sub">Al costado del Jockey Plaza<br>Frente a la Universidad de Lima</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-pagination !bottom-16"></div>
        <div class="swiper-button-prev hero-nav-prev" aria-label="Anterior"></div>
        <div class="swiper-button-next hero-nav-next" aria-label="Siguiente"></div>
    </div>

    <div
        class="rg-hero-leaves pointer-events-none absolute inset-x-0 bottom-0 z-20 h-12 bg-[length:auto_100%] bg-bottom bg-repeat-x tablet:h-16"
        style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
        aria-hidden="true"
    ></div>
</section>
