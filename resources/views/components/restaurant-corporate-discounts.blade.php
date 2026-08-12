@props(['restaurant'])

@if($restaurant->showsCorporateDiscountBadge())
    <div class="rg-rest-discount-flag" aria-label="Descuentos corporativos">
        <span class="rg-rest-discount-check" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12.5 9.2 16.5 19 7.5"/>
            </svg>
        </span>
        <p class="rg-rest-discount-flag-label">Descuentos corporativos</p>
    </div>
@elseif($restaurant->showsCorporateDiscountDetails())
    @php
        $discounts = $restaurant->visibleCorporateDiscounts();
    @endphp
    <section class="rg-rest-discounts" aria-label="Descuentos corporativos">
        <h2 class="rg-rest-panel-title">Descuentos corporativos</h2>
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
