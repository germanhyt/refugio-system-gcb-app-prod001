@props(['blocks', 'cornerDeco' => null])

<section @class(['rg-contact-blocks', 'rg-section--decorated' => filled($cornerDeco)]) {{ $attributes }}>
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
                    @foreach(($block->emails ?? []) as $email)
                        <a href="mailto:{{ $email }}" class="rg-contact-block-link">{{ $email }}</a>
                    @endforeach
                    @foreach(($block->phones ?? []) as $phone)
                        @php $tel = preg_replace('/\s+/', '', (string) $phone); @endphp
                        <a href="tel:{{ $tel }}" class="rg-contact-block-link">{{ $phone }}</a>
                    @endforeach
                </article>
            @endforeach
        </div>
    </div>
</section>
