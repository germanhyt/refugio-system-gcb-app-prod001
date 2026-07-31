@props(['blocks'])

<section
    class="relative overflow-hidden bg-cover bg-center py-[var(--section-gap)] text-white"
    style="background-image: url('{{ asset('images/refugio/bg_contacto-home.jpg') }}');"
>
    <div class="absolute inset-0 bg-black/45" aria-hidden="true"></div>
    <div class="container-refugio relative grid gap-5 tablet:grid-cols-3">
        @foreach(['evento', 'renta', 'contacto'] as $i => $type)
            @php($block = $blocks[$type] ?? null)
            @continue(! $block)
            <a
                href="{{ $block->link_url ?: route('contact') }}"
                class="group border border-white/15 bg-black/25 p-8 backdrop-blur-sm transition duration-500 hover:-translate-y-1 hover:bg-black/35"
                data-aos="fade-up"
                data-aos-delay="{{ $i * 100 }}"
            >
                <h3 class="font-display text-2xl font-semibold leading-snug tablet:text-[1.7rem]">
                    {{ $block->title }}
                    @if($block->highlighted_word)
                        <span class="text-peach">{{ $block->highlighted_word }}</span>
                    @endif
                </h3>
                @if($block->description)
                    <p class="mt-3 text-sm text-white/70">{{ $block->description }}</p>
                @endif
                <span class="mt-8 inline-flex font-accent text-xl text-peach transition group-hover:translate-x-1">
                    {{ $block->link_text ?: 'Más info' }} →
                </span>
            </a>
        @endforeach
    </div>
</section>
