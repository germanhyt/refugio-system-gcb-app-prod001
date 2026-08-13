@props(['restaurant'])

@php
    $discountImage = $restaurant->exclusiveDiscountImageUrl();
    $showBadge = $restaurant->showsExclusiveDiscount();
@endphp

@if($showBadge)
    <div class="rg-rest-discount-wrap" @if($discountImage) x-data="{ open: false }" @endif>
        @if($discountImage)
            <button
                type="button"
                class="rg-rest-discount-flag rg-rest-discount-flag--clickable"
                title="Ver"
                aria-label="Ver descuentos exclusivos"
                @click="open = true"
            >
                <span class="rg-rest-discount-check" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.5 9.2 16.5 19 7.5"/>
                    </svg>
                </span>
                <span class="rg-rest-discount-flag-label">Descuentos exclusivos</span>
            </button>

            <template x-teleport="body">
                <div
                    class="rg-discount-modal"
                    x-show="open"
                    x-cloak
                    x-transition.opacity
                    @keydown.escape.window="open = false"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Descuentos exclusivos de {{ $restaurant->name }}"
                >
                    <div class="rg-discount-modal-backdrop" @click="open = false"></div>
                    <div class="rg-discount-modal-dialog" @click.stop>
                        <button type="button" class="rg-discount-modal-close" title="Cerrar" aria-label="Cerrar" @click="open = false">×</button>
                        <img src="{{ $discountImage }}" alt="Descuentos exclusivos de {{ $restaurant->name }}">
                    </div>
                </div>
            </template>
        @else
            <div class="rg-rest-discount-flag" aria-label="Descuentos exclusivos">
                <span class="rg-rest-discount-check" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.5 9.2 16.5 19 7.5"/>
                    </svg>
                </span>
                <p class="rg-rest-discount-flag-label">Descuentos exclusivos</p>
            </div>
        @endif
    </div>
@elseif($restaurant->showsCorporateDiscountDetails())
    @php
        $discounts = $restaurant->visibleCorporateDiscounts();
    @endphp
    <section class="rg-rest-discounts" aria-label="Descuentos exclusivos">
        <h2 class="rg-rest-panel-title">Descuentos exclusivos</h2>
        <ul class="rg-rest-discounts-list">
            @foreach($discounts as $discount)
                @php
                    $isUpcoming = ($discount['status'] ?? 'current') === 'upcoming';
                    $startsAt = filled($discount['starts_at'] ?? null)
                        ? \Illuminate\Support\Carbon::parse($discount['starts_at'], 'America/Lima')
                        : null;
                    $endsAt = filled($discount['ends_at'] ?? null)
                        ? \Illuminate\Support\Carbon::parse($discount['ends_at'], 'America/Lima')
                        : null;
                @endphp
                <li @class(['rg-rest-discounts-item', 'rg-rest-discounts-item--upcoming' => $isUpcoming])>
                    <div class="rg-rest-discounts-head">
                        <p class="rg-rest-discounts-name">{{ $discount['title'] }}</p>
                        <span class="rg-rest-discounts-badge">{{ $isUpcoming ? 'Próximamente' : 'Vigente' }}</span>
                    </div>
                    @if(filled($discount['description'] ?? null))
                        <p class="rg-rest-discounts-copy">{{ $discount['description'] }}</p>
                    @endif
                    @if($isUpcoming && $startsAt)
                        <p class="rg-rest-discounts-until">Desde el {{ $startsAt->translatedFormat('d/m/Y') }}</p>
                    @elseif($endsAt)
                        <p class="rg-rest-discounts-until">Válido hasta el {{ $endsAt->translatedFormat('d/m/Y') }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
@endif
