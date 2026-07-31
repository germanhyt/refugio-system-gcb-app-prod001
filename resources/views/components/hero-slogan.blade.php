@php
    $hotspots = [
        ['image' => 'hotspot-1.jpg', 'text' => 'Disfruta de espacios impresionantes', 'url' => route('about'), 'rotate' => '-8deg'],
        ['image' => 'hotspot-2.jpg', 'text' => 'La mejor propuesta gastronómica', 'url' => route('restaurants.index'), 'rotate' => '0deg'],
        ['image' => 'hotspot-3.jpg', 'text' => 'Escapa de la rutina', 'url' => route('events.index'), 'rotate' => '6deg'],
    ];
@endphp

<section class="rg-hero-slogan relative overflow-hidden pb-10 pt-6 tablet:pb-16" data-aos="fade">
    <div
        class="pointer-events-none absolute inset-0 bg-[top_center] bg-no-repeat"
        style="background-image: url('{{ asset('images/refugio/home-bg-2.jpg') }}');"
        aria-hidden="true"
    ></div>

    <div
        class="relative mx-auto mb-16 mt-2 h-10 max-w-[1440px] bg-cover bg-center bg-no-repeat tablet:mb-28 tablet:h-14"
        style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
        aria-hidden="true"
    ></div>

    <div class="container-refugio relative mb-16 text-center tablet:mb-24" data-aos="fade-up">
        <h2 class="font-display text-[2rem] font-medium uppercase leading-tight text-primary tablet:text-[2.8rem]">
            <span class="mb-1 block font-sans text-[1.6rem] font-normal normal-case text-primary tablet:text-[2.5rem]">Juntos todo</span>
            <span class="block uppercase">Sabe mejor</span>
        </h2>
    </div>

    <div class="container-refugio relative mx-auto mb-14 max-w-[1300px]">
        <div class="grid gap-6 tablet:grid-cols-3 tablet:gap-2 desktop:gap-0">
            @foreach($hotspots as $i => $spot)
                <a
                    href="{{ $spot['url'] }}"
                    class="rg-hotspot group relative mx-auto block w-full max-w-[380px]"
                    style="transform: rotate({{ $spot['rotate'] }});"
                    data-aos="fade-up"
                    data-aos-delay="{{ $i * 100 }}"
                >
                    <img
                        src="{{ asset('images/refugio/'.$spot['image']) }}"
                        alt="{{ $spot['text'] }}"
                        class="w-full object-cover shadow-sm"
                        style="aspect-ratio: 683/1024; max-height: 520px;"
                        loading="lazy"
                    >
                    <span class="rg-hotspot-btn" aria-hidden="true">+</span>
                    <span class="rg-hotspot-tip">{{ $spot['text'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="container-refugio relative text-center" data-aos="fade-up">
        <a href="{{ route('about') }}" class="btn-conocenos">Conócenos</a>
    </div>
</section>

<section
    class="h-14 bg-auto bg-center bg-no-repeat tablet:h-[135px]"
    style="background-image: url('{{ asset('images/refugio/divisor-fondo-verde_home.png') }}');"
    aria-hidden="true"
></section>
