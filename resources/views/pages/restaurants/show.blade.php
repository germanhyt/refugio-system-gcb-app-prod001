@extends('layouts.app')

@section('title', ($restaurant->meta_title ?: $restaurant->name).' | Refugio Gastronómico')
@section('meta_description', $restaurant->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($restaurant->short_description ?: $restaurant->description ?? ''), 160))

@php
    $heroImage = $restaurant->getFirstMediaUrl('featured_image') ?: $restaurant->getFirstMediaUrl('logo');
    $locationImage = $restaurant->parkPositionImageUrl();
    $leadText = $restaurant->detailLeadText();
    $bodyHtml = $restaurant->detailBodyHtml();
    $menuPdf = $restaurant->getFirstMediaUrl('menu_pdf');
    $hasMeta = $restaurant->hasSocialLinks() || filled($menuPdf);
@endphp

@section('content')
<section
    class="rg-rest-detail-hero"
    @if($heroImage) style="background-image: url('{{ $heroImage }}');" @endif
>
    <div class="rg-rest-detail-hero-inner container-refugio">
        <div
            class="rg-rest-detail-card"
            data-aos="fade-right"
            style="background-image: url('{{ asset('images/refugio/bg-p-restaurante.png') }}');"
        >
            <a href="{{ route('restaurants.index') }}" class="rg-rest-detail-back">
                <svg width="8" height="12" viewBox="0 0 7 12" fill="currentColor" aria-hidden="true">
                    <path d="M6,12a1,1,0,0,1-.71-.29l-5-5a1,1,0,0,1,0-1.42l5-5A1,1,0,0,1,6.71,1.71L2.41,6l4.3,4.29a1,1,0,0,1,0,1.42A1,1,0,0,1,6,12Z"/>
                </svg>
                Regresar
            </a>

            <h1 class="rg-rest-detail-title">{{ $restaurant->name }}</h1>

            <div
                class="rg-rest-detail-divider"
                style="background-image: url('{{ asset('images/refugio/separador-p-restaurante.svg') }}');"
                aria-hidden="true"
            >
                <span class="rg-rest-detail-divider-spacer"></span>
            </div>
        </div>
    </div>
</section>

<section class="rg-rest-detail-body">
    <div class="container-refugio">
        <div @class([
            'rg-rest-detail-layout',
            'rg-rest-detail-layout--with-aside' => $restaurant->hasDetailAside(),
        ]) data-aos="fade-up">
            <div class="rg-rest-detail-main">
                @if($restaurant->categories->isNotEmpty())
                    <p class="rg-rest-detail-cats">{{ $restaurant->categories->pluck('name')->join(' · ') }}</p>
                @endif

                @if(filled($leadText))
                    <p class="rg-rest-detail-short">{{ $leadText }}</p>
                @endif

                @if(filled($bodyHtml))
                    <div class="rg-rest-detail-copy">
                        {!! $bodyHtml !!}
                    </div>
                @endif

                <x-restaurant-corporate-discounts :restaurant="$restaurant" />

                @if($hasMeta)
                    <div class="rg-rest-detail-meta">
                        <x-restaurant-social-links :restaurant="$restaurant" />

                        @if(filled($menuPdf))
                            <a
                                href="{{ $menuPdf }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rg-btn-outline"
                            >Ver menú PDF</a>
                        @endif
                    </div>
                @endif
            </div>

            @if($restaurant->hasDetailAside())
                <aside class="rg-rest-detail-aside">
                    @if($locationImage)
                        <div class="rg-rest-panel rg-rest-detail-location">
                            <h2 class="rg-rest-panel-title">Posición en el parque</h2>
                            <figure class="rg-rest-detail-location-figure">
                                <img
                                    src="{{ $locationImage }}"
                                    alt="Posición de {{ $restaurant->name }} dentro del Refugio"
                                    loading="lazy"
                                >
                            </figure>
                        </div>
                    @endif

                    <x-delivery-logos :restaurant="$restaurant" />
                </aside>
            @endif
        </div>
    </div>
</section>

@if($similarRestaurants->isNotEmpty())
    <section class="rg-rest-similar">
        <div class="container-refugio">
            <h2 class="rg-rest-similar-title" data-aos="fade-up">Ofertas similares</h2>
            <div data-aos="fade-up">
                <x-similar-restaurants-carousel :restaurants="$similarRestaurants" />
            </div>
        </div>
    </section>
@endif
@endsection
