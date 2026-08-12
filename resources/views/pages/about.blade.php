@extends('layouts.app')

@section('title', 'Nosotros | Refugio Gastronómico')

@section('content')
{{-- Fondo pájaros negros: public/images/refugio/bg-pagajaras-negro-1.png --}}
@once
    <style>
        .rg-visit-hero.rg-visit-hero--birds-dark,
        .rg-contact-hero.rg-contact-hero--birds-dark,
        .rg-static-hero.rg-static-hero--birds-dark {
            position: relative !important;
            isolation: isolate !important;
            background-color: #1e4d33 !important;
            background-image: none !important;
            overflow: hidden !important;
        }
        .rg-visit-hero.rg-visit-hero--birds-dark::before,
        .rg-contact-hero.rg-contact-hero--birds-dark::before,
        .rg-static-hero.rg-static-hero--birds-dark::before {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            z-index: 0 !important;
            pointer-events: none !important;
            background-image: url('{{ asset('images/refugio/bg-pagajaras-negro-1.png') }}') !important;
            background-repeat: no-repeat !important;
            background-position: center top !important;
            background-size: cover !important;
            opacity: 0.55 !important;
        }
        .rg-static-hero {
            min-height: 360px !important;
            padding: 8.75rem 0 5.5rem !important;
        }
        .rg-static-hero-title {
            padding-block: 0.85rem !important;
        }
    </style>
@endonce
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

    <div class="container-refugio relative z-10 px-6 text-center">
        <x-about-hero-title :title="$siteSettings->hero_title_about" />
    </div>
</section>

<div
    class="rg-visit-leaf-divider"
    style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
    aria-hidden="true"
></div>

<x-hola-section class="rg-hola-section--with-gallery rg-hola-section--birds-light" />
<x-about-gallery-carousel :images="$aboutGalleryImages" class="rg-about-gallery--birds-light" />
<x-visit-us />
@endsection
