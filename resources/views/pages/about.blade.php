@extends('layouts.app')

@section('title', 'Nosotros | Refugio Gastronómico')

@push('head')
    <x-page-hero-birds-dark-styles />
    <x-page-hero-critical-styles />
@endpush

@section('content')
<section
    class="rg-visit-hero rg-visit-hero--birds-dark"
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

    <div class="container-refugio relative z-10 text-center">
        <x-about-hero-title :title="$siteSettings->hero_title_about" />
    </div>
</section>

<div class="rg-visit-leaf-divider" aria-hidden="true"></div>

<x-hola-section class="rg-hola-section--with-gallery rg-hola-section--birds-light" />
<x-about-gallery-carousel :images="$aboutGalleryImages" class="rg-about-gallery--birds-light" />
<x-visit-us />
@endsection
