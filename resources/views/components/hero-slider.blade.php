@props(['slides'])

@php
    $slideCount = $slides->count();
    $showNav = $slideCount > 1;
@endphp

<section class="rg-hero relative min-h-screen overflow-hidden bg-[#1a1210]" id="top">
    <div
        class="swiper hero-swiper h-screen w-full"
        data-hero-swiper
        data-slide-count="{{ $slideCount }}"
    >
        <div class="swiper-wrapper">
            @foreach($slides as $slide)
                @php
                    $imageUrl = $slide->getFirstMediaUrl('background_image');
                    $videoUrl = $slide->getFirstMediaUrl('background_video');
                    $isVideo = $slide->isVideo() && filled($videoUrl);
                    $overlayHtml = $slide->overlayHtml();
                @endphp
                <div class="swiper-slide relative overflow-hidden">
                    <div class="absolute inset-0 bg-[#1a1210]">
                        @if($isVideo)
                            <video
                                class="h-full w-full object-cover"
                                autoplay
                                muted
                                loop
                                playsinline
                                @if(filled($imageUrl)) poster="{{ $imageUrl }}" @endif
                            >
                                <source src="{{ $videoUrl }}" type="{{ str_ends_with(strtolower($videoUrl), '.webm') ? 'video/webm' : 'video/mp4' }}">
                            </video>
                        @elseif(filled($imageUrl))
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $slide->overlayText() ?: 'Refugio Gastronómico' }}"
                                class="hero-kenburns h-full w-full object-cover"
                                @if($loop->first) fetchpriority="high" @endif
                            >
                        @endif
                        <div class="rg-hero-overlay absolute inset-0"></div>
                    </div>
                    <div class="relative z-10 flex h-full rg-hero-tagline-wrap">
                        <h1 class="rg-hero-tagline" data-hero-title>{!! $overlayHtml !!}</h1>
                    </div>
                </div>
            @endforeach
        </div>

        @if($showNav)
            <div class="swiper-pagination !bottom-16"></div>
            <div class="swiper-button-prev hero-nav-prev" aria-label="Anterior"></div>
            <div class="swiper-button-next hero-nav-next" aria-label="Siguiente"></div>
        @endif
    </div>

    <div
        class="rg-hero-leaves pointer-events-none absolute inset-x-0 bottom-0 z-20 h-12 bg-[length:auto_100%] bg-bottom bg-repeat-x tablet:h-16"
        style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
        aria-hidden="true"
    ></div>
</section>
