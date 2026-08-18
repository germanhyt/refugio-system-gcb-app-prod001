@props(['settings', 'visitInfo' => null])

@php
    $primary = config('navigation.primary', []);
    $secondary = config('navigation.footer_secondary', []);
    $legal = config('navigation.footer_legal', []);
    $facebook = $settings->facebook_url ?: 'https://www.facebook.com/RefugioParqueGastronomico';
    $tiktok = $settings->tiktok_url ?: 'https://www.tiktok.com/@refugiogastronomico.pe';
@endphp

<footer class="rg-footer">
    <div
        class="rg-footer-edge"
        style="background-image: url('{{ asset('images/refugio/borde-1-footer.png') }}');"
        aria-hidden="true"
    ></div>

    <div class="rg-footer-body">
        <div class="container-refugio">
            <div class="rg-footer-grid rg-footer-grid--three">
                <div class="rg-footer-col">
                    <h2 class="rg-footer-title">Explora</h2>
                    <ul class="rg-footer-nav">
                        @foreach($primary as $item)
                            <li><a href="{{ route($item['route']) }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="rg-footer-col">
                    <h2 class="rg-footer-title">Más información</h2>
                    <ul class="rg-footer-nav">
                        @foreach($secondary as $item)
                            @php
                                $isUlima = ($item['route'] ?? null) === 'static.ulima';
                                $ulimaPdf = $isUlima ? $settings->ulimaDiscountsPdfUrl() : null;
                                $href = $ulimaPdf ?: route($item['route']);
                                $openBlank = filled($ulimaPdf);
                            @endphp
                            <li>
                                <a
                                    href="{{ $href }}"
                                    @if($openBlank) target="_blank" rel="noopener noreferrer" @endif
                                >{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rg-footer-col">
                    <h2 class="rg-footer-title">Legal</h2>
                    <ul class="rg-footer-legal">
                        @foreach($legal as $item)
                            <li><a href="{{ route($item['route']) }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                    <div class="rg-footer-social">
                        @if($settings->instagram_url)
                            <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="Instagram">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if($settings->youtube_url)
                            <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="YouTube">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        @endif
                        @if($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="Facebook">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                        @endif
                        @if($tiktok)
                            <a href="{{ $tiktok }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="TikTok">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <p class="rg-footer-copy">© {{ date('Y') }} Refugio • diseñado por GCB</p>
        </div>
    </div>
</footer>
