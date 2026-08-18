{{-- Banner verde: clearance bajo logo (170px) + título centrado --}}
@once('rg-page-hero-layout')
    <style>
        .rg-visit-hero.rg-visit-hero--birds-dark,
        .rg-contact-hero.rg-contact-hero--birds-dark {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 420px !important;
            padding: 11.5rem 1rem 4rem !important;
            box-sizing: border-box !important;
        }

        .rg-visit-hero.rg-visit-hero--birds-dark > .container-refugio,
        .rg-contact-hero.rg-contact-hero--birds-dark > .container-refugio {
            position: relative !important;
            z-index: 2 !important;
            width: 100% !important;
            text-align: center !important;
        }

        .rg-visit-hero--birds-dark .rg-visit-hero-deco,
        .rg-contact-hero--birds-dark .rg-visit-hero-deco {
            position: absolute !important;
            z-index: 1 !important;
        }

        .rg-visit-hero-title,
        .rg-contact-hero-title {
            width: 100% !important;
            margin: 0 auto !important;
            padding: 1.5rem 0 1rem !important;
            text-align: center !important;
        }

        .rg-visit-hero-title-lead {
            display: block !important;
            margin-bottom: 0.65rem !important;
        }

        .rg-visit-hero-title-main,
        .rg-contact-hero-subtitle {
            width: 100% !important;
            text-align: center !important;
        }

        .rg-contact-hero-subtitle {
            margin-top: 1.25rem !important;
        }

        @media (min-width: 768px) {
            .rg-visit-hero.rg-visit-hero--birds-dark,
            .rg-contact-hero.rg-contact-hero--birds-dark {
                min-height: 480px !important;
                padding: 13rem 1rem 4.5rem !important;
            }

            .rg-visit-hero-title,
            .rg-contact-hero-title {
                padding-top: 2rem !important;
                padding-bottom: 1.25rem !important;
            }
        }

        @media (max-width: 1024px) {
            .rg-visit-hero.rg-visit-hero--birds-dark {
                min-height: 240px !important;
                padding: 5.5rem 1rem 1.75rem !important;
            }

            .rg-visit-hero.rg-visit-hero--birds-dark .rg-visit-hero-title {
                padding-top: 0.35rem !important;
                padding-bottom: 0.25rem !important;
            }
        }
    </style>
@endonce
