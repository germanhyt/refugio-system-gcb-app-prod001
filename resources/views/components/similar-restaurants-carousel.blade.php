@props(['restaurants'])

@if($restaurants->isNotEmpty())
    <div class="rg-similar-carousel" data-similar-carousel>
        <button type="button" class="rg-similar-nav rg-similar-nav--prev" aria-label="Anterior">
            <svg width="10" height="16" viewBox="0 0 10 16" fill="currentColor" aria-hidden="true">
                <path d="M8.7,15.3a1,1,0,0,1-.7-.3l-6-6a1,1,0,0,1,0-1.4l6-6a1,1,0,0,1,1.4,1.4L3.41,8l5.29,5.29a1,1,0,0,1-.7,1.71Z"/>
            </svg>
        </button>

        <div class="swiper rg-similar-swiper" data-similar-swiper>
            <div class="swiper-wrapper">
                @foreach($restaurants as $similar)
                    <div class="swiper-slide">
                        <x-restaurant-card :restaurant="$similar" />
                    </div>
                @endforeach
            </div>
        </div>

        <button type="button" class="rg-similar-nav rg-similar-nav--next" aria-label="Siguiente">
            <svg width="10" height="16" viewBox="0 0 10 16" fill="currentColor" aria-hidden="true">
                <path d="M1.3,15.3a1,1,0,0,1-.7-1.7L5.89,8,.6,2.71A1,1,0,0,1,2,.3l6,6a1,1,0,0,1,0,1.4l-6,6a1,1,0,0,1-.7.3Z"/>
            </svg>
        </button>
    </div>
@endif
