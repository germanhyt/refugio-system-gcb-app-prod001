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
                        @php
                            $digits = preg_replace('/\D+/', '', (string) $phone);
                            $whatsapp = strlen((string) $digits) === 9
                                ? 'https://wa.me/51'.$digits
                                : (str_starts_with((string) $digits, '51') ? 'https://wa.me/'.$digits : null);
                            $fallbackTel = preg_replace('/\s+/', '', (string) $phone);
                        @endphp
                        <a
                            href="{{ $whatsapp ?: 'tel:'.$fallbackTel }}"
                            class="rg-contact-block-link"
                            @if($whatsapp) target="_blank" rel="noopener noreferrer" @endif
                        >{{ $phone }}</a>
                    @endforeach
                </article>
            @endforeach
        </div>
    </div>
</section>
