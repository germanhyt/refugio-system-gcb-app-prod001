@props(['restaurant'])

@if($restaurant->showsDeliveryLogos())
    <aside class="rg-rest-detail-delivery-col" aria-label="Delivery disponible">
        <div class="rg-delivery-callout-head">
            <p class="rg-delivery-callout-title">Pide ahora</p>
            <span class="rg-delivery-callout-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                    <path d="M3 7h11v8H3V7Zm11 2h3l3 3v3h-2.1a2.5 2.5 0 0 1-4.8 0H11V9h3Zm5.5 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM7.5 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>
                </svg>
            </span>
        </div>

        <div class="rg-delivery-logos">
            @if($restaurant->delivery_rappi_enabled)
                @if(filled($restaurant->delivery_rappi_url))
                    <a
                        href="{{ $restaurant->delivery_rappi_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rg-delivery-circle rg-delivery-circle--rappi"
                        aria-label="Pedir en Rappi"
                    >
                        <img src="{{ asset('images/refugio/delivery-rappi.svg') }}" alt="">
                    </a>
                @else
                    <span class="rg-delivery-circle rg-delivery-circle--rappi" aria-label="Disponible en Rappi">
                        <img src="{{ asset('images/refugio/delivery-rappi.svg') }}" alt="">
                    </span>
                @endif
            @endif

            @if($restaurant->delivery_peya_enabled)
                @if(filled($restaurant->delivery_peya_url))
                    <a
                        href="{{ $restaurant->delivery_peya_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rg-delivery-circle rg-delivery-circle--peya"
                        aria-label="Pedir en PedidosYa"
                    >
                        <img src="{{ asset('images/refugio/delivery-peya.png') }}" alt="">
                    </a>
                @else
                    <span class="rg-delivery-circle rg-delivery-circle--peya" aria-label="Disponible en PedidosYa">
                        <img src="{{ asset('images/refugio/delivery-peya.png') }}" alt="">
                    </span>
                @endif
            @endif
        </div>
    </aside>
@endif
