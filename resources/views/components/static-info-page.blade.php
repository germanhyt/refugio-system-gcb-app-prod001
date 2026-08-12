@props(['page'])

{{-- Fondo pájaros: public/images/refugio/bg-pagajaras-negro-1.png --}}
@once
    <style>
        .rg-static-hero.rg-static-hero--birds-dark {
            position: relative !important;
            isolation: isolate !important;
            background-color: #1e4d33 !important;
            background-image: none !important;
            overflow: hidden !important;
        }
        .rg-static-hero.rg-static-hero--birds-dark::before {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            z-index: 0 !important;
            pointer-events: none !important;
            background-image: url('{{ asset('images/refugio/bg-pagajaras-negro-1.png') }}') !important;
            background-repeat: no-repeat !important;
            background-position: center top !important;
            background-size: cover !important;
            opacity: 0.55 !important;
        }
        .rg-static-hero {
            min-height: 360px !important;
            padding: 8.75rem 0 5.5rem !important;
        }
        .rg-static-hero-title {
            padding-block: 0.85rem !important;
        }
    </style>
@endonce
<section class="rg-static-hero rg-static-hero--birds-dark">
    <div class="rg-static-hero-overlay" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="rg-static-hero-title">{{ $page['title'] }}</h1>
        @if(! empty($page['intro']))
            <p class="rg-static-hero-intro">{{ $page['intro'] }}</p>
        @endif
    </div>
    <div
        class="rg-static-hero-divider"
        style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
        aria-hidden="true"
    ></div>
</section>

<section class="rg-static-content">
    <div class="container-refugio rg-static-content-inner">
        @foreach($page['blocks'] ?? [] as $block)
            @switch($block['type'])
                @case('highlight')
                    <article class="rg-static-highlight" data-aos="fade-up">
                        <h2 class="rg-static-block-title">{{ $block['title'] }}</h2>
                        <p class="rg-static-block-body">{{ $block['body'] }}</p>
                    </article>
                    @break

                @case('list')
                    <article class="rg-static-list-block" data-aos="fade-up">
                        <h2 class="rg-static-block-title">{{ $block['title'] }}</h2>
                        <ul class="rg-static-list">
                            @foreach($block['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                    @break

                @case('faq')
                    <div class="rg-static-faq" data-aos="fade-up">
                        @foreach($block['items'] as $item)
                            <details class="rg-static-faq-item">
                                <summary>{{ $item['question'] }}</summary>
                                <p>{{ $item['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                    @break

                @case('cards')
                    <div class="rg-static-cards" data-aos="fade-up">
                        @foreach($block['items'] as $card)
                            <article class="rg-static-card">
                                <h3 class="rg-static-card-title">{{ $card['title'] }}</h3>
                                <p class="rg-static-card-body">{{ $card['body'] }}</p>
                            </article>
                        @endforeach
                    </div>
                    @break
            @endswitch
        @endforeach
    </div>
</section>

<x-visit-us />
