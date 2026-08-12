@props(['images', 'title' => 'Nuestro espacio'])

@if($images->isNotEmpty())
    <section class="rg-about-gallery" {{ $attributes }}>
        <div class="container-refugio">
            <div class="rg-about-gallery-head" data-aos="fade-up">
                <h2 class="rg-about-gallery-title">{{ $title }}</h2>
            </div>

            <div class="rg-about-gallery-wrap" data-about-gallery data-aos="fade-up" data-aos-delay="60">
                <div class="swiper rg-about-gallery-swiper" data-about-gallery-swiper>
                    <div class="swiper-wrapper">
                        @foreach($images as $image)
                            <div class="swiper-slide">
                                <div class="rg-about-gallery-slide">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="rg-about-gallery-nav rg-about-gallery-nav--prev" aria-label="Anterior">
                    <svg width="10" height="16" viewBox="0 0 10 16" fill="currentColor" aria-hidden="true">
                        <path d="M8.7,15.3a1,1,0,0,1-.7-.3l-6-6a1,1,0,0,1,0-1.4l6-6a1,1,0,0,1,1.4,1.4L3.41,8l5.29,5.29a1,1,0,0,1-.7,1.71Z"/>
                    </svg>
                </button>

                <button type="button" class="rg-about-gallery-nav rg-about-gallery-nav--next" aria-label="Siguiente">
                    <svg width="10" height="16" viewBox="0 0 10 16" fill="currentColor" aria-hidden="true">
                        <path d="M1.3,15.3a1,1,0,0,1-.7-1.7L5.89,8,.6,2.71A1,1,0,0,1,2,.3l6,6a1,1,0,0,1,0,1.4l-6,6a1,1,0,0,1-.7.3Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>
@endif
