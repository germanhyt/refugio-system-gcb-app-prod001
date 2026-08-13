@props(['features'])

@php
    $collageLeft = [
        ['image' => 'collage-1.jpg', 'rotate' => '4deg', 'align' => 'right', 'width' => '50%'],
        ['image' => 'collage-2.jpg', 'rotate' => '-3deg', 'align' => 'left', 'width' => '70%', 'overlap' => true],
        ['image' => 'collage-3.jpg', 'rotate' => '-2deg', 'align' => 'right', 'width' => '70%', 'overlap' => true],
    ];
    $collageRight = [
        ['image' => 'collage-4.jpg', 'rotate' => '2deg', 'align' => 'left', 'width' => '70%'],
        ['image' => 'collage-5.jpg', 'rotate' => '3deg', 'align' => 'right', 'width' => '70%', 'overlap' => true],
        ['image' => 'collage-6.jpg', 'rotate' => '-4deg', 'align' => 'left', 'width' => '85%', 'overlap' => true],
    ];
@endphp

<section class="rg-restaurants-section relative overflow-hidden">
    <div
        class="relative bg-cover bg-center pb-32 pt-12 tablet:pb-56 tablet:pt-14"
        style="background-image: linear-gradient(180deg, #236869 0%, rgba(35,104,105,0.76) 100%), url('{{ asset('images/refugio/fondo.jpg') }}');"
    >
        <div class="container-refugio">
            {{-- Collage 3 columnas --}}
            <div class="rg-restaurants-collage mb-16 grid items-center gap-6 tablet:mb-20 tablet:grid-cols-[30%_40%_30%] tablet:gap-0">
                {{-- Columna izquierda --}}
                <div class="rg-collage-col hidden flex-col gap-0 tablet:flex">
                    @foreach($collageLeft as $photo)
                        <figure
                            class="rg-collage-photo {{ ! empty($photo['overlap']) ? '-mt-10' : '' }}"
                            style="transform: rotate({{ $photo['rotate'] }}); text-align: {{ $photo['align'] }};"
                        >
                            <img
                                src="{{ asset('images/refugio/'.$photo['image']) }}"
                                alt=""
                                style="width: {{ $photo['width'] }};"
                                loading="lazy"
                            >
                        </figure>
                    @endforeach
                </div>

                {{-- Centro --}}
                <div
                    class="rg-restaurants-center relative z-[1] mx-auto flex min-h-[300px] w-full max-w-md flex-col items-center justify-center bg-center bg-no-repeat px-6 py-12 text-center tablet:min-h-[420px] tablet:max-w-none tablet:bg-[length:min(92%,520px)] tablet:px-10"
                    style="background-image: url('{{ asset('images/refugio/bg-2_home.png') }}');"
                    data-aos="fade-up"
                >
                    <h2 class="font-sans text-2xl font-normal leading-snug text-white tablet:text-[2.2rem]">¡Conoce nuestros</h2>
                    <h2 class="mt-1 font-display text-4xl font-medium uppercase leading-none text-white tablet:text-[2.8rem]">restaurantes!</h2>
                    <a href="{{ route('restaurants.index') }}" class="btn-conocelos mt-6">Conócelos</a>
                </div>

                {{-- Columna derecha --}}
                <div class="rg-collage-col hidden flex-col gap-0 tablet:flex">
                    @foreach($collageRight as $photo)
                        <figure
                            class="rg-collage-photo {{ ! empty($photo['overlap']) ? '-mt-10' : '' }}"
                            style="transform: rotate({{ $photo['rotate'] }}); text-align: {{ $photo['align'] }};"
                        >
                            <img
                                src="{{ asset('images/refugio/'.$photo['image']) }}"
                                alt=""
                                style="width: {{ $photo['width'] }};"
                                loading="lazy"
                            >
                        </figure>
                    @endforeach
                </div>
            </div>

            {{-- Móvil: una imagen destacada --}}
            <div class="mb-8 tablet:hidden" data-aos="fade-up">
                <figure class="rg-collage-photo mx-auto max-w-xs" style="transform: rotate(-2deg);">
                    <img src="{{ asset('images/refugio/collage-2.jpg') }}" alt="" class="w-full" loading="lazy">
                </figure>
            </div>

            {{-- Carrusel de logos --}}
            <div class="rg-logos-swiper swiper" data-logos-swiper data-aos="fade-up">
                <div class="swiper-wrapper">
                    @foreach($features as $feature)
                        @php($restaurant = $feature->restaurant)
                        @continue(! $restaurant)
                        @php($logo = $restaurant->logoUrl() ?: $restaurant->featuredImageUrl())
                        @continue(! $logo)
                        <div class="swiper-slide">
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="rg-logo-slide">
                                <img src="{{ $logo }}" alt="{{ $restaurant->name }}" loading="lazy">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
