@props(['restaurant'])

@php
    $logo = $restaurant->getFirstMediaUrl('logo');
    $food = $restaurant->getFirstMediaUrl('featured_image');
    $hasHover = filled($logo) && filled($food);
@endphp

<a
    href="{{ route('restaurants.show', $restaurant) }}"
    class="rg-rest-card group {{ $hasHover ? '' : 'rg-rest-card--static' }}"
>
    <div class="rg-rest-card-media">
        @if($food)
            <img
                src="{{ $food }}"
                alt="{{ $restaurant->name }}"
                class="rg-rest-card-image rg-rest-card-food"
                loading="lazy"
            >
        @endif

        @if($logo)
            <div class="rg-rest-card-logo-view" aria-hidden="true">
                <img src="{{ $logo }}" alt="">
            </div>
        @elseif(! $food)
            <div class="rg-rest-card-placeholder" aria-hidden="true"></div>
        @endif
    </div>

    <h3 class="rg-rest-card-title">{{ $restaurant->name }}</h3>
</a>
