@props(['restaurant'])

@php
    $discounts = $restaurant->visibleCorporateDiscounts();
@endphp

@if($discounts->isNotEmpty())
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
