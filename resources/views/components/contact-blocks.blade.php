@props(['blocks', 'cornerDeco' => null])

<section id="contacto" @class(['rg-contact-blocks', 'rg-section--decorated' => filled($cornerDeco)]) {{ $attributes }}>
    @if($cornerDeco)
        <x-section-corner-deco :position="$cornerDeco" />
    @endif
    <div class="container-refugio relative z-10">
        <h2 class="rg-contact-blocks-title" data-aos="fade-up">¿Dudas? ¡Contáctanos!</h2>

        <div class="rg-contact-blocks-grid">
            @foreach($blocks as $block)
                <article class="rg-contact-block" data-aos="fade-up">
                    <x-contact-icon :title="$block->title" />
                    <h3 class="rg-contact-block-title">{{ $block->title }}</h3>
                    @if($block->body)
                        <p class="rg-contact-block-body">{{ $block->body }}</p>
                    @endif
                    @foreach($block->contactChannels() as $channel)
                        @if($channel['href'])
                            <a
                                href="{{ $channel['href'] }}"
                                class="rg-contact-block-link"
                                @if($channel['external']) target="_blank" rel="noopener noreferrer" @endif
                            >{{ $channel['label'] }}</a>
                        @else
                            <span class="rg-contact-block-link rg-contact-block-link--static">{{ $channel['label'] }}</span>
                        @endif
                    @endforeach
                </article>
            @endforeach
        </div>
    </div>
</section>
