@props(['posts'])

@php
    $settings = \App\Models\SiteSetting::current();
    $instagramUrl = $settings->instagram_url ?: 'https://www.instagram.com/refugiogastronomico.pe/';
    $facebookUrl = $settings->facebook_url ?: 'https://www.facebook.com/RefugioParqueGastronomico';
@endphp

<section class="rg-social-section relative overflow-hidden py-16 tablet:py-24">
    <div
        class="pointer-events-none absolute inset-0 bg-[top_center] bg-cover bg-no-repeat"
        style="background-image: url('{{ asset('images/refugio/bg_social-home.png') }}');"
        aria-hidden="true"
    ></div>

    <div class="container-refugio relative">
        <div class="rg-social-header mb-10 tablet:mb-14" data-aos="fade-up">
            <h2 class="rg-social-title">Síguenos y</h2>
            <div class="rg-social-icons">
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="rg-social-icon rg-social-icon--ig" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener" class="rg-social-icon rg-social-icon--fb" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </a>
            </div>
            <h2 class="rg-social-title">únete al club</h2>
        </div>

        <div class="swiper rg-ig-swiper" data-ig-swiper data-aos="fade-up">
            <div class="swiper-wrapper">
                @forelse($posts as $post)
                    @php
                        $localImg = $post->getFirstMediaUrl('image');
                        $remoteImg = $post->source_image_url;
                        $img = $localImg ?: $remoteImg;
                        $isVideo = in_array($post->media_type, ['reel', 'video'], true);
                        $caption = \Illuminate\Support\Str::limit(strip_tags($post->caption ?: ''), 50);
                        $embedUrl = $post->permalink ? rtrim($post->permalink, '/').'/embed/' : null;
                    @endphp
                    <div class="swiper-slide">
                        <article class="rg-ig-card">
                            <div class="rg-ig-photo">
                                @if($localImg)
                                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="group block h-full w-full">
                                        <img
                                            src="{{ $localImg }}"
                                            alt="{{ $caption ?: 'Publicación de Instagram' }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                            loading="lazy"
                                        >
                                        @if($isVideo)
                                            <span class="rg-ig-play" aria-hidden="true">
                                                <svg viewBox="0 0 448 512" fill="currentColor"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
                                            </span>
                                        @endif
                                    </a>
                                @elseif($embedUrl)
                                    {{-- Embed oficial: Instagram bloquea hotlink del CDN (403); el embed sí carga media/video --}}
                                    <iframe
                                        src="{{ $embedUrl }}"
                                        class="rg-ig-embed {{ $isVideo ? 'rg-ig-embed--reel' : 'rg-ig-embed--post' }}"
                                        title="{{ $caption ?: 'Instagram' }}"
                                        loading="lazy"
                                        allowtransparency="true"
                                        allow="encrypted-media; clipboard-write"
                                        scrolling="no"
                                    ></iframe>
                                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="rg-ig-embed-link" aria-label="Abrir en Instagram"></a>
                                @elseif($remoteImg)
                                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="group block h-full w-full">
                                        <img
                                            src="{{ $remoteImg }}"
                                            alt="{{ $caption ?: 'Publicación de Instagram' }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                            referrerpolicy="no-referrer"
                                            onerror="this.closest('.rg-ig-photo').classList.add('is-broken')"
                                        >
                                        <div class="rg-ig-fallback">
                                            <span>Ver en Instagram</span>
                                        </div>
                                        @if($isVideo)
                                            <span class="rg-ig-play" aria-hidden="true">
                                                <svg viewBox="0 0 448 512" fill="currentColor"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
                                            </span>
                                        @endif
                                    </a>
                                @else
                                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="rg-ig-fallback is-visible">
                                        <span>Ver en Instagram</span>
                                    </a>
                                @endif
                            </div>

                            @if($caption)
                                <p class="rg-ig-caption">{{ $caption }}</p>
                            @endif

                            <div class="rg-ig-meta">
                                <span class="rg-ig-stat">
                                    <svg viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M414.9 24C361.8 24 312 65.7 288 89.3 264 65.7 214.2 24 161.1 24 70.3 24 16 76.9 16 165.5c0 72.6 66.8 133.3 69.2 135.4l187 180.8c8.8 8.5 22.8 8.5 31.6 0l186.7-180.2c2.7-2.7 69.5-63.5 69.5-136C560 76.9 505.7 24 414.9 24z"/></svg>
                                    {{ $post->likes_count }}
                                </span>
                                <span class="rg-ig-stat">
                                    <svg viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M576 240c0 115-129 208-288 208-48.3 0-93.9-8.6-133.9-23.8-40.3 31.2-89.8 50.3-142.4 55.7-5.2.6-10.2-2.8-11.5-7.7-1.3-5 2.7-8.1 6.6-11.8 19.3-18.4 42.7-32.8 51.9-94.6C21.9 330.9 0 287.3 0 240 0 125.1 129 32 288 32s288 93.1 288 208z"/></svg>
                                    {{ $post->comments_count }}
                                </span>
                            </div>
                        </article>
                    </div>
                @empty
                    <p class="col-span-full py-8 text-center text-muted">Próximamente feed de Instagram.</p>
                @endforelse
            </div>

            @if($posts->count() > 0)
                <button type="button" class="ig-prev rg-ig-arrow" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="ig-next rg-ig-arrow" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            @endif
        </div>

        @if($posts->count() > 0)
            <div class="mt-10 text-center" data-aos="fade-up">
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="btn-outline">Load More</a>
            </div>
        @endif
    </div>
</section>
