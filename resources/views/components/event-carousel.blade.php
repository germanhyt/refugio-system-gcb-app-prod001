@props(['events'])

<section class="rg-events-section relative z-[1] -mt-24 overflow-hidden pb-16 pt-32 tablet:-mt-[150px] tablet:pb-20 tablet:pt-52">
    <div
        class="pointer-events-none absolute inset-0 bg-[top_center] bg-cover bg-no-repeat"
        style="background-image: url('{{ asset('images/refugio/bg_eventos-home.png') }}');"
        aria-hidden="true"
    ></div>

    <div class="container-refugio relative">
        <div class="rg-events-header mb-10 tablet:mb-12">
            <h2 class="rg-events-title" data-aos="fade-up">
                Eventos <span>&amp; actividades</span>
            </h2>
            <div class="rg-events-nav">
                <button type="button" class="events-prev rg-events-arrow" aria-label="Anterior">
                    <svg viewBox="0 0 180 180" fill="none" aria-hidden="true"><path d="M119 47.3166C119 48.185 118.668 48.9532 118.003 49.6212L78.8385 89L118.003 128.379C118.668 129.047 119 129.815 119 130.683C119 131.552 118.668 132.32 118.003 132.988L113.021 137.998C112.356 138.666 111.592 139 110.729 139C109.865 139 109.101 138.666 108.436 137.998L61.9966 91.3046C61.3322 90.6366 61 89.8684 61 89C61 88.1316 61.3322 87.3634 61.9966 86.6954L108.436 40.002C109.101 39.334 109.865 39 110.729 39C111.592 39 112.356 39.334 113.021 40.002L118.003 45.012C118.668 45.68 119 46.4482 119 47.3166Z" fill="currentColor"/></svg>
                </button>
                <button type="button" class="events-next rg-events-arrow" aria-label="Siguiente">
                    <svg viewBox="0 0 180 180" fill="none" aria-hidden="true"><path d="M61 47.3166C61 48.185 61.3322 48.9532 61.9966 49.6212L101.161 89L61.9966 128.379C61.3322 129.047 61 129.815 61 130.683C61 131.552 61.3322 132.32 61.9966 132.988L66.9788 137.998C67.6432 138.666 68.4075 139 69.271 139C70.1345 139 70.8988 138.666 71.5632 137.998L118.003 91.3046C118.667 90.6366 119 89.8684 119 89C119 88.1316 118.667 87.3634 118.003 86.6954L71.5632 40.002C70.8988 39.334 70.1345 39 69.271 39C68.4075 39 67.6432 39.334 66.9788 40.002L61.9966 45.012C61.3322 45.68 61 46.4482 61 47.3166Z" fill="currentColor"/></svg>
                </button>
            </div>
        </div>

        <div class="swiper events-swiper" data-events-swiper data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($events as $event)
                    <div class="swiper-slide !h-auto">
                        <x-event-card :event="$event" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
