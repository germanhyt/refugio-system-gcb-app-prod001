@props(['restaurant'])

@php
    $featured = $restaurant->getFirstMediaUrl('featured_image');
    $logo = $restaurant->getFirstMediaUrl('logo');
    $image = $featured ?: $logo;
@endphp

<a href="{{ route('restaurants.show', $restaurant) }}" class="rg-rest-card group">
    <div class="rg-rest-card-media">
        @if($image)
            <img
                src="{{ $image }}"
                alt="{{ $restaurant->name }}"
                class="rg-rest-card-image"
                loading="lazy"
            >
        @else
            <div class="rg-rest-card-placeholder" aria-hidden="true"></div>
        @endif

        @if($logo)
            <span
                class="rg-rest-card-logo"
                style="background-image: url('{{ $logo }}');"
                aria-hidden="true"
            ></span>
        @endif
    </div>

    <h3 class="rg-rest-card-title">{{ $restaurant->name }}</h3>
</a>
