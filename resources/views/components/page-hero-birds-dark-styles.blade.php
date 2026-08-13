{{-- Fondo pájaros: public/images/refugio/bg-pagajaras-negro-1.png --}}
@once('rg-page-hero-birds-dark')
    <style>
        .rg-visit-hero.rg-visit-hero--birds-dark,
        .rg-contact-hero.rg-contact-hero--birds-dark,
        .rg-static-hero.rg-static-hero--birds-dark {
            position: relative !important;
            isolation: isolate !important;
            background-color: #1e4d33 !important;
            background-image: none !important;
            overflow: hidden !important;
        }

        .rg-visit-hero.rg-visit-hero--birds-dark::before,
        .rg-contact-hero.rg-contact-hero--birds-dark::before,
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

        .rg-contact-hero--birds-dark .rg-contact-hero-overlay {
            display: none !important;
        }
    </style>
@endonce
