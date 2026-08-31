@props(['services', 'cornerDeco' => null])

<section class="rg-services-preview rg-section--decorated" {{ $attributes }}>
    <x-services-edge-decos />
    <div class="container-refugio relative z-[1]">
        <div class="rg-services-preview-head" data-aos="fade-up">
            <h2 class="rg-services-preview-title">Conoce nuestros servicios</h2>
        </div>

        <x-services-grid :services="$services" class="rg-services-preview-grid" />

        <div class="rg-services-preview-cta" data-aos="fade-up">
            <a href="{{ route('services.index') }}" class="rg-btn-outline" data-rg-track="click_services_more" data-rg-label="Ver más servicios">Ver más</a>
        </div>
    </div>
</section>
