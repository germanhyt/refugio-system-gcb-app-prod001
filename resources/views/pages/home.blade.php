@extends('layouts.app')

@section('title', $siteSettings->seo_title ?: 'Refugio Gastronómico')

@section('content')
    <x-hero-slider :slides="$slides" />
    <x-hero-slogan />
    <x-restaurant-grid :features="$featuredRestaurants" />
    <x-event-carousel :events="$events" />
    <x-instagram-feed :posts="$instagramPosts" />
    <x-blog-foodies :posts="$blogPosts" />
@endsection
