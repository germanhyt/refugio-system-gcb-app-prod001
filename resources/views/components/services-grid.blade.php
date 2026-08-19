@props(['services'])

<div {{ $attributes->merge(['class' => 'rg-services-grid']) }}>
    @forelse($services as $service)
        @php
            $iconUrl = $service->getFirstMediaUrl('icon');
        @endphp
        <article class="rg-service-item" data-aos="fade-up">
            <div class="rg-service-icon" aria-hidden="true">
                @if($iconUrl)
                    <img src="{{ $iconUrl }}" alt="">
                @else
                    <x-service-icon :icon-key="$service->icon_key" :title="$service->title" />
                @endif
            </div>
            <h3 class="rg-service-title">{{ $service->title }}</h3>
            @if($service->description || $service->normalizedContactPhone())
                <p class="rg-service-desc">{!! $service->descriptionWithWhatsappLinks() !!}</p>
            @endif
        </article>
    @empty
        <p class="col-span-full py-8 text-center text-[#776f69]">Pronto publicaremos nuestros servicios.</p>
    @endforelse
</div>
