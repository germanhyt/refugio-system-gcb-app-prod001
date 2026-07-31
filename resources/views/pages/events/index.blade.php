@extends('layouts.app')

@section('title', 'Eventos | Refugio Gastronómico')

@section('content')
{{-- Hero con foto de comida (origen: pexels-ella-olsson) --}}
<section class="rg-events-hero relative flex min-h-[420px] items-center justify-center overflow-hidden tablet:min-h-[500px]">
    <img
        src="{{ asset('images/refugio/eventos-hero.jpg') }}"
        alt=""
        class="absolute inset-0 h-full w-full object-cover object-bottom"
        fetchpriority="high"
    >
    <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/20 to-black/40" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="font-display text-4xl font-medium uppercase tracking-wide text-white tablet:text-5xl wide:text-6xl">Eventos</h1>
    </div>
    <div class="rg-events-hero-tear" aria-hidden="true"></div>
</section>

{{-- Grilla Eventos & actividades con fondo púrpura --}}
<section
    class="rg-events-page-listing relative overflow-hidden py-16 tablet:py-24"
    style="background-image: url('{{ asset('images/refugio/bg_eventos-home.png') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;"
>
    <div class="container-refugio relative">
        <div class="mb-10 tablet:mb-14" data-aos="fade-up">
            <h2 class="rg-events-title text-white">
                Eventos <span>&amp; actividades</span>
            </h2>
        </div>

        <div class="grid gap-6 tablet:grid-cols-2 desktop:grid-cols-4" data-aos="fade-up">
            @forelse($events as $event)
                <x-event-card :event="$event" />
            @empty
                <p class="col-span-full py-10 text-center text-white/70">Pronto anunciaremos nuevos eventos.</p>
            @endforelse
        </div>

        @if($events->hasPages())
            <div class="rg-events-pagination mt-12">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</section>

{{-- Suscríbete: no te pierdas de nada --}}
<section class="rg-events-subscribe">
    <div class="rg-events-subscribe-inner container-refugio">
        <div class="rg-events-subscribe-copy" data-aos="fade-up">
            <h2 class="rg-events-subscribe-title">
                no te pierdas<br><span>de nada</span>
            </h2>
            <p class="rg-events-subscribe-text">
                Suscríbete gratis y entérate de las últimas novedades, tendencias y reviews para verdaderos foodies.
            </p>
        </div>

        <div class="rg-events-subscribe-form" data-aos="fade-up" data-aos-delay="80">
            @if(session('newsletter_success'))
                <p class="mb-4 bg-[#d4f4f4] px-4 py-3 text-sm text-[#236869]">{{ session('newsletter_success') }}</p>
            @endif

            <form action="{{ route('newsletter.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="newsletter-name" class="sr-only">Nombre</label>
                    <input
                        id="newsletter-name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Nombre"
                        required
                        class="rg-newsletter-input"
                    >
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="newsletter-email" class="sr-only">Correo electrónico</label>
                    <input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Correo electrónico"
                        required
                        class="rg-newsletter-input"
                    >
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-cta w-full !bg-[#a4464c] !text-[1.35rem] !font-medium">Suscríbete</button>
            </form>
        </div>

        <div
            class="rg-events-subscribe-media"
            style="background-image: url('{{ asset('images/refugio/Group-1051-1.png') }}');"
            aria-hidden="true"
            data-aos="fade-left"
            data-aos-delay="120"
        ></div>
    </div>
</section>

{{-- Banner: Organiza un evento (origen: pexels-jane-doan) --}}
<section class="rg-events-organize">
    <img
        src="{{ asset('images/refugio/eventos-subscribe-bg.jpg') }}"
        alt=""
        class="rg-events-organize-bg"
        loading="lazy"
    >
    <div class="rg-events-organize-overlay" aria-hidden="true"></div>
    <div class="rg-events-organize-content" data-aos="fade-up">
        <h2 class="rg-events-organize-title">¡Organiza un evento con nosotros!</h2>
        <a href="{{ route('contact') }}" class="rg-events-organize-btn">Escríbenos</a>
    </div>
</section>
@endsection
