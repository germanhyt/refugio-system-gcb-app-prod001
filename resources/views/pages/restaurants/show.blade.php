@extends('layouts.app')

@section('title', ($restaurant->meta_title ?: $restaurant->name).' | Refugio Gastronómico')
@section('meta_description', $restaurant->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($restaurant->description ?? ''), 160))

@php
    $heroImage = $restaurant->getFirstMediaUrl('featured_image') ?: $restaurant->getFirstMediaUrl('logo');
    $orderUrl = $restaurant->whatsapp_url
        ?: ($siteSettings->whatsapp_url ?? null)
        ?: $restaurant->google_maps_url
        ?: route('restaurants.index');
    $orderExternal = ! str_starts_with((string) $orderUrl, url('/'));
@endphp

@section('content')
{{-- Hero detalle restaurante --}}
<section
    class="rg-rest-detail-hero"
    @if($heroImage) style="background-image: url('{{ $heroImage }}');" @endif
>
    <div class="rg-rest-detail-hero-inner container-refugio">
        <div class="rg-rest-detail-card" data-aos="fade-right">
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

            <a
                href="{{ $orderUrl }}"
                class="rg-rest-detail-order"
                @if($orderExternal) target="_blank" rel="noopener noreferrer" @endif
            >
                <img
                    src="{{ asset('images/refugio/img-boton-restaurante.png') }}"
                    alt="Pide ahora"
                    width="148"
                    height="148"
                >
            </a>
        </div>
    </div>
</section>

{{-- Contenido --}}
<section class="rg-rest-detail-body">
    <div class="container-refugio">
        <div class="rg-rest-detail-content" data-aos="fade-up">
            @if($restaurant->categories->isNotEmpty())
                <p class="rg-rest-detail-cats">{{ $restaurant->categories->pluck('name')->join(' · ') }}</p>
            @endif

            @if($restaurant->description)
                <div class="rg-rest-detail-copy">
                    {!! $restaurant->description !!}
                </div>
            @else
                <p class="rg-rest-detail-copy">
                    Descubre la propuesta de {{ $restaurant->name }} en Refugio Gastronómico.
                </p>
            @endif

            <div class="rg-rest-detail-actions">
                @if($restaurant->whatsapp_url || ($siteSettings->whatsapp_url ?? false))
                    <a
                        href="{{ $restaurant->whatsapp_url ?: $siteSettings->whatsapp_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-cta"
                    >Pide ahora</a>
                @endif

                @if($restaurant->getFirstMediaUrl('menu_pdf'))
                    <a
                        href="{{ $restaurant->getFirstMediaUrl('menu_pdf') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-outline"
                    >Ver menú PDF</a>
                @endif

                @if($restaurant->google_maps_url)
                    <a
                        href="{{ $restaurant->google_maps_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rg-rest-detail-maps"
                    >Ver en Google Maps</a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
