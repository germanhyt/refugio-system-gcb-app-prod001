@props(['features', 'cornerDeco' => null])

<section @class(['rg-logo-carousel', 'rg-section--decorated' => filled($cornerDeco)]) {{ $attributes }}>
    @if($cornerDeco)
        <x-section-corner-deco :position="$cornerDeco" />
    @endif
    <div class="container-refugio relative z-10">
        <h2 class="rg-logo-carousel-title" data-aos="fade-up">Nuestros restaurantes</h2>
    </div>

    @if($features->isNotEmpty())
        <div class="rg-logo-carousel-track" data-logo-carousel data-aos="fade-up">
            <div class="rg-logo-carousel-row">
                @foreach([1, 2] as $loopCopy)
                    @foreach($features as $feature)
                        @php
                            $restaurant = $feature->restaurant;
                            $logo = $restaurant?->logoUrl()
                                ?: asset('images/refugio/logo-v2.svg');
                        @endphp
                        @if($restaurant)
                            <a
                                href="{{ route('restaurants.show', $restaurant) }}"
                                class="rg-logo-carousel-item"
                                aria-label="{{ $restaurant->name }}"
                            >
                                <img src="{{ $logo }}" alt="{{ $restaurant->name }}" loading="lazy">
                            </a>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
</section>
