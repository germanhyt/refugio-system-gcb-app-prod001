@props(['settings'])

@php
    $logo = $settings->getFirstMediaUrl('logo') ?: asset('images/refugio/logo-v2.svg');
    $whatsapp = $settings->whatsapp_url ?: 'https://wa.link/ltbwxk';
    $instagram = $settings->instagram_url ?: 'https://www.instagram.com/refugiogastronomico.pe/';
    $facebook = $settings->facebook_url ?: 'https://www.facebook.com/RefugioParqueGastronomico';
    $tiktok = $settings->tiktok_url;
    $navItems = [
        ['label' => 'Restaurantes', 'route' => 'restaurants.index'],
        ['label' => 'Eventos', 'route' => 'events.index'],
        ['label' => 'Visítanos', 'route' => 'about'],
    ];
@endphp

<header id="rg-header" class="rg-header absolute inset-x-0 top-0 z-50 transition-[background,box-shadow] duration-500">
    <div class="rg-header-inner bg-gradient-to-b from-black/55 via-black/25 to-transparent">
        {{-- Layout hero (Elementor 6058: cols 42% / 16% / 42%) --}}
        <div class="rg-header-hero">
            <div class="rg-header-hero-row">
                <div class="rg-header-col rg-header-col--nav">
                    <nav class="rg-header-nav" aria-label="Principal">
                        @foreach($navItems as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="rg-nav-link rg-nav-bird font-display text-[15px] font-medium uppercase tracking-wide text-white/90 transition hover:text-white {{ request()->routeIs($item['route']) ? 'is-active' : '' }}"
                            >{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                </div>

                <div class="rg-header-col rg-header-col--logo">
                    <a href="{{ route('home') }}" class="rg-header-logo">
                        <img src="{{ $logo }}" alt="{{ $settings->site_name }}">
                    </a>
                </div>

                <div class="rg-header-col rg-header-col--actions">
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn-reserva-ghost rg-header-reserva">
                        ¡Reserva aquí!
                    </a>
                    <button type="button" class="rg-menu-toggle rg-menu-toggle--filled" @click="menuOpen = true" aria-label="Abrir menú">
                        <svg viewBox="0 0 58 58" fill="none" aria-hidden="true"><circle cx="29" cy="29" r="28.25" fill="currentColor"/><path d="M37.47 22.63H20.08a1.25 1.25 0 0 1 0-2.5H37.47a1.25 1.25 0 0 1 0 2.5Z" fill="#fff"/><path d="M41.49 30.66h-25a1.25 1.25 0 0 1 0-2.5h25a1.25 1.25 0 0 1 0 2.5Z" fill="#fff"/><path d="M37.47 38.68H20.08a1.25 1.25 0 0 1 0-2.5H37.47a1.25 1.25 0 0 1 0 2.5Z" fill="#fff"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Layout sticky (Elementor 7811) --}}
        <div class="rg-header-sticky">
            <a href="{{ route('home') }}" class="rg-sticky-logo" aria-label="{{ $settings->site_name }}">
                <img src="{{ $logo }}" alt="{{ $settings->site_name }}">
            </a>

            <div class="rg-sticky-right">
                <nav class="rg-sticky-nav" aria-label="Principal sticky">
                    @foreach($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="rg-sticky-nav-link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}"
                        >{{ $item['label'] }}</a>
                    @endforeach
                </nav>
                <a href="{{ route('restaurants.index') }}" class="btn-provoc btn-provoc--sticky">¿Qué te provoca?</a>
                <button type="button" class="rg-menu-toggle rg-sticky-menu-toggle" @click="menuOpen = true" aria-label="Abrir menú">
                    <svg viewBox="0 0 58 58" fill="none" aria-hidden="true"><circle cx="29" cy="29" r="28.25" stroke="currentColor" stroke-width="1.5"/><path d="M37.47 22.63H20.08a1.25 1.25 0 0 1 0-2.5H37.47a1.25 1.25 0 0 1 0 2.5Z" fill="currentColor"/><path d="M41.49 30.66h-25a1.25 1.25 0 0 1 0-2.5h25a1.25 1.25 0 0 1 0 2.5Z" fill="currentColor"/><path d="M37.47 38.68H20.08a1.25 1.25 0 0 1 0-2.5H37.47a1.25 1.25 0 0 1 0 2.5Z" fill="currentColor"/></svg>
                </button>
            </div>
        </div>
    </div>

</header>

{{-- Menú fullscreen split (popup 6685) — teleport body, z-index limpio --}}
<template x-teleport="body">
    <div
        class="rg-menu-overlay"
        x-show="menuOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-label="Menú"
        @keydown.escape.window="menuOpen = false"
        :class="{ 'is-open': menuOpen }"
        x-transition:enter="rg-menu-enter"
        x-transition:enter-start="rg-menu-enter-from"
        x-transition:enter-end="rg-menu-enter-to"
        x-transition:leave="rg-menu-leave"
        x-transition:leave-start="rg-menu-leave-from"
        x-transition:leave-end="rg-menu-leave-to"
    >
        <div class="rg-menu-panel">
            {{-- Una sola imagen a la izquierda --}}
            <div
                class="rg-menu-media"
                style="background-image: url('{{ asset('images/refugio/menu-pancakes.jpg') }}');"
                aria-hidden="true"
            >
                <div class="rg-menu-torn" aria-hidden="true"></div>
            </div>

            <div class="rg-menu-content">
                <div class="rg-menu-toolbar">
                    <a
                        href="{{ route('restaurants.index') }}"
                        class="btn-provoc-menu"
                        @click="menuOpen = false"
                    >¿Qué te provoca?</a>
                    <button type="button" class="rg-menu-close" @click="menuOpen = false" aria-label="Cerrar menú">
                        <span class="rg-menu-close-x" aria-hidden="true"></span>
                    </button>
                </div>

                <nav class="rg-menu-nav" aria-label="Menú principal">
                    @foreach($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="rg-menu-link"
                            @click="menuOpen = false"
                        >{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="rg-menu-footer">
                    <div class="rg-menu-footer-links">
                        <a href="{{ route('contact') }}" @click="menuOpen = false">Contacto</a>
                        <span aria-hidden="true">·</span>
                        <a href="{{ route('contact') }}" @click="menuOpen = false">Convocatorias</a>
                    </div>
                    <div class="rg-menu-socials">
                        <a href="{{ $instagram }}" target="_blank" rel="noopener" class="rg-menu-social" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="{{ $facebook }}" target="_blank" rel="noopener" class="rg-menu-social" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                        @if($tiktok)
                            <a href="{{ $tiktok }}" target="_blank" rel="noopener" class="rg-menu-social" aria-label="TikTok">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
