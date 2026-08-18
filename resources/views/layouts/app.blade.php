<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteSettings->seo_title ?: $siteSettings->site_name)</title>
    <meta name="description" content="@yield('meta_description', $siteSettings->seo_description)">
    <meta property="og:title" content="@yield('og_title', $siteSettings->seo_title ?: $siteSettings->site_name)">
    <meta property="og:description" content="@yield('meta_description', $siteSettings->seo_description)">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @php($ogImage = $siteSettings->getFirstMediaUrl('og_image') ?: $siteSettings->getFirstMediaUrl('logo'))
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @php($faviconUrl = $siteSettings->getFirstMediaUrl('favicon') ?: asset('images/refugio/favicon-150x150.png'))
    @php($faviconLargeUrl = asset('images/refugio/favicon-300x300.png'))
    <link rel="icon" href="{{ $faviconUrl }}" sizes="32x32">
    <link rel="icon" href="{{ $faviconLargeUrl }}" sizes="192x192">
    <link rel="apple-touch-icon" href="{{ $faviconLargeUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[var(--color-surface)] text-[var(--color-text-body)] antialiased" x-data="{ menuOpen: false }" :class="{ 'overflow-hidden': menuOpen }">
    <x-header :settings="$siteSettings" />
    <x-fixed-social-sidebar :settings="$siteSettings" />

    <main>
        @yield('content')
    </main>

    <x-footer :settings="$siteSettings" />
    <x-scroll-top />

    @stack('scripts')
</body>
</html>
