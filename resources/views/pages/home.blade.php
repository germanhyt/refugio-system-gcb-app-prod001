@extends('layouts.app')

@section('title', $siteSettings->seo_title ?: 'Refugio Gastronómico')

@section('content')
    <x-hero-slider :slides="$slides" />
    <x-hero-slogan />
    <x-restaurant-grid :features="$featuredRestaurants" />
    <x-event-carousel :events="$events" />
    <x-instagram-feed :posts="$instagramPosts" />
    @if(($siteSettings->show_blog_section ?? true) && $blogPosts->isNotEmpty())
        <x-blog-foodies :posts="$blogPosts" />
    @endif
@endsection
