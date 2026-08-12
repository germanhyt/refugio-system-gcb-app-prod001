@extends('layouts.app')

@section('title', $siteSettings->seo_title ?: 'Refugio Gastronómico')

@section('content')
    <x-hero-slider :slides="$slides" />
    <x-hola-section topRibbon class="rg-hola-section--birds-wire" />
    <x-logo-carousel :features="$featuredRestaurants" />
    <x-services-preview :services="$homeServices" />
    <x-visit-us />
    <x-contact-blocks :blocks="$contactBlocks" cornerDeco="bottom-right" />
@endsection
