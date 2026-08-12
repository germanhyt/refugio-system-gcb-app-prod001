@props(['slides'])

@php
    $fallbackBg = asset('images/refugio/fondohome.jpg');
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
            @forelse($slides as $slide)
                @php
                    $poster = $slide->getFirstMediaUrl('background_image') ?: $fallbackBg;
                    $videoUrl = $slide->getFirstMediaUrl('background_video');
                    $isVideo = $slide->isVideo() && filled($videoUrl);
                    $hasCopy = filled($slide->title)
                        || filled($slide->subtitle)
                        || (filled($slide->cta_text) && filled($slide->cta_url));
                @endphp
                <div class="swiper-slide relative overflow-hidden">
                    <div class="absolute inset-0">
                        @if($isVideo)
                            <video
                                class="h-full w-full object-cover"
                                autoplay
                                muted
                                loop
                                playsinline
                                poster="{{ $poster }}"
                            >
                                <source src="{{ $videoUrl }}" type="video/mp4">
                            </video>
                        @else
                            <img
                                src="{{ $poster }}"
                                alt="{{ $slide->title ?: 'Refugio Gastronómico' }}"
                                class="hero-kenburns h-full w-full object-cover"
                                @if($loop->first) fetchpriority="high" @endif
                            >
                        @endif
                        <div class="rg-hero-overlay absolute inset-0"></div>
                    </div>
                    @if($hasCopy)
                        <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
                            <div class="flex max-w-4xl flex-col items-center" data-hero-title>
                                @if($slide->subtitle)
                                    <p class="rg-hero-cms-subtitle" data-hero-subtitle>{{ $slide->subtitle }}</p>
                                @endif
                                @if($slide->title)
                                    <h1 class="rg-hero-cms-title">{{ $slide->title }}</h1>
                                @endif
                                @if($slide->cta_text && $slide->cta_url)
                                    <div class="rg-hero-ctas mt-8" data-hero-cta>
                                        <a href="{{ $slide->cta_url }}" class="rg-btn-outline rg-btn-outline--light">
                                            {{ $slide->cta_text }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="swiper-slide relative overflow-hidden">
                    <div class="absolute inset-0">
                        <img
                            src="{{ $fallbackBg }}"
                            alt="Refugio Gastronómico"
                            class="hero-kenburns h-full w-full object-cover"
                            fetchpriority="high"
                        >
                        <div class="rg-hero-overlay absolute inset-0"></div>
                    </div>
                    <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
                        <div class="flex max-w-4xl flex-col items-center" data-hero-title>
                            <h1 class="rg-hero-cms-title">Refugio Gastronómico</h1>
                        </div>
                    </div>
                </div>
            @endforelse
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
