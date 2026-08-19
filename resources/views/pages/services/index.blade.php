@extends('layouts.app')

@section('title', 'Servicios | Refugio Gastronómico')

@section('content')
@php $servicesBanner = $siteSettings->pageHeroBannerUrl('services'); @endphp
<section
    class="rg-rest-hero"
    @if($servicesBanner) style="background-image: url('{{ $servicesBanner }}');" @endif
>
    <div class="rg-rest-hero-overlay" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="rg-rest-hero-title">{{ $siteSettings->hero_title_services }}</h1>
    </div>
</section>

<section class="rg-services-page rg-section--decorated">
    <x-services-edge-decos />
    <div class="container-refugio relative z-[1]">
        <x-services-grid :services="$services" />
    </div>
</section>

<x-visit-us />
@endsection
