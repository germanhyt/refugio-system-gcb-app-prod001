@extends('layouts.app')

@section('title', 'Eventos | Refugio Gastronómico')

@section('content')
<section class="rg-events-hero relative flex min-h-[420px] items-center justify-center overflow-hidden tablet:min-h-[500px]">
    <img
        src="{{ $siteSettings->pageHeroBannerUrl('events') }}"
        alt=""
        class="absolute inset-0 h-full w-full object-cover object-bottom"
        fetchpriority="high"
    >
    <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/20 to-black/40" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="font-display text-3xl font-medium uppercase tracking-wide text-white tablet:text-5xl wide:text-6xl">
            {{ $siteSettings->hero_title_events ?: '¡Somos el refugio de tu diversión!' }}
        </h1>
    </div>
    <div class="rg-events-hero-tear" aria-hidden="true"></div>
</section>

<section
    class="rg-events-page-listing relative overflow-hidden py-16 tablet:py-24"
    style="background-image: url('{{ asset('images/refugio/bg_eventos-home.png') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;"
>
    <div class="container-refugio relative">
        <div class="grid gap-6 tablet:grid-cols-2 desktop:grid-cols-4 tablet:items-stretch" data-aos="fade-up">
            @forelse($offers as $offer)
                @php
                    $cover = $offer->getFirstMediaUrl('cover') ?: asset('images/refugio/eventos-hero.jpg');
                    $href = $offer->cta_url ?: route('contact');
                    $isExternal = str_starts_with($href, 'http://') || str_starts_with($href, 'https://');
                @endphp
                <article class="rg-event-offer-card group">
                    <div class="rg-event-offer-media">
                        <img src="{{ $cover }}" alt="{{ $offer->title }}" loading="lazy">
                    </div>
                    <div class="rg-event-offer-body">
                        <h2 class="rg-event-offer-title">{{ $offer->title }}</h2>
                        @if($offer->summary)
                            <p class="rg-event-offer-summary">{{ $offer->summary }}</p>
                        @endif
                        @if($offer->cta_text)
                            <a
                                href="{{ $href }}"
                                class="rg-event-offer-cta"
                                @if($isExternal) target="_blank" rel="noopener noreferrer" @endif
                            >{{ $offer->cta_text }}</a>
                        @else
                            <span class="rg-event-offer-cta-spacer" aria-hidden="true"></span>
                        @endif
                    </div>
                </article>
            @empty
                <p class="col-span-full py-10 text-center text-white/70">Pronto anunciaremos nuevas experiencias.</p>
            @endforelse
        </div>
    </div>
</section>

<x-visit-us />
@endsection
