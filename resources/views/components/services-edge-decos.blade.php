@once
    <style>
        .rg-services-preview,
        .rg-services-page {
            position: relative !important;
            overflow: visible !important;
        }
        .rg-services-edge-decos {
            position: absolute !important;
            inset: 0 !important;
            z-index: 12 !important;
            pointer-events: none !important;
        }
        .rg-services-deco {
            position: absolute !important;
            display: block !important;
            pointer-events: none !important;
        }
        .rg-services-deco--plants-left {
            left: 0 !important;
            top: -0.35rem !important;
            width: 170px !important;
            max-width: 28vw !important;
            height: auto !important;
            opacity: 0.55 !important;
        }
        .rg-services-deco--plants-right {
            left: auto !important;
            right: 0 !important;
            top: -0.35rem !important;
            width: 170px !important;
            max-width: 28vw !important;
            height: auto !important;
            transform: scaleX(-1) !important;
            opacity: 0.55 !important;
        }
        .rg-services-deco--platana {
            left: 0 !important;
            bottom: 0 !important;
            width: 340px !important;
            max-width: 44vw !important;
            height: auto !important;
        }
        .rg-services-deco--birds {
            left: auto !important;
            right: 1.25rem !important;
            bottom: 1rem !important;
            width: 180px !important;
            max-width: 28vw !important;
            height: auto !important;
        }
        @media (max-width: 640px) {
            .rg-services-deco--plants-left,
            .rg-services-deco--plants-right { width: 96px !important; opacity: 0.4 !important; }
            .rg-services-deco--platana { width: 210px !important; }
            .rg-services-deco--birds { width: 120px !important; right: 0.7rem !important; }
        }
    </style>
@endonce

<div class="rg-services-edge-decos" aria-hidden="true">
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-services-deco rg-services-deco--plants-left"
        style="position:absolute;left:0;top:-6px;width:170px;height:auto;z-index:12;opacity:0.55;"
    >
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-services-deco rg-services-deco--plants-right"
        style="position:absolute;right:0;top:-6px;left:auto;width:170px;height:auto;z-index:12;opacity:0.55;transform:scaleX(-1);"
    >
    <img
        src="{{ asset('images/refugio/decorator-platana-inferior-izquierda.png') }}"
        alt=""
        class="rg-services-deco rg-services-deco--platana"
        style="position:absolute;left:0;bottom:0;width:340px;height:auto;z-index:12;"
    >
    <img
        src="{{ asset('images/refugio/decorator-pajaros-rojos.png') }}"
        alt=""
        class="rg-services-deco rg-services-deco--birds"
        style="position:absolute;right:20px;bottom:16px;left:auto;width:180px;height:auto;z-index:12;"
    >
</div>
