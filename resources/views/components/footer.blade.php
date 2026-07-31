@props(['settings', 'visitInfo' => null])

@php
    $visit = $visitInfo ?? \App\Models\VisitInfo::current();
    $scheduleLines = collect($visit->schedule ?? [])->map(fn ($row) => ($row['days'] ?? '').' - '.($row['hours'] ?? ''))->filter()->all();
    if ($scheduleLines === []) {
        $scheduleLines = [
            'Domingo a Miércoles - 8 am a 10 pm',
            'Jueves - 8 am a 1 am',
            'Viernes y Sábado - 8 am a 3 am',
        ];
    }
    $address = $visit->address ?: 'Av. Javier Prado Este 4492 - Santiago de Surco';
@endphp

<footer class="rg-footer">
    <div
        class="rg-footer-edge"
        style="background-image: url('{{ asset('images/refugio/borde-1-footer.png') }}');"
        aria-hidden="true"
    ></div>

    <div class="rg-footer-body">
        <div class="container-refugio">
            <div class="rg-footer-grid">
                {{-- Columna Visítanos --}}
                <div class="rg-footer-visit">
                    <h2 class="rg-footer-title">Visítanos</h2>
                    <ul class="rg-footer-info">
                        <li>
                            <span class="rg-footer-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <path d="M16 18C15.0111 18 14.0444 17.7068 13.2222 17.1574C12.3999 16.6079 11.759 15.8271 11.3806 14.9134C11.0022 13.9998 10.9031 12.9945 11.0961 12.0246C11.289 11.0546 11.7652 10.1637 12.4645 9.46447C13.1637 8.76521 14.0546 8.289 15.0246 8.09608C15.9945 7.90315 16.9998 8.00217 17.9134 8.3806C18.8271 8.75904 19.6079 9.39991 20.1574 10.2222C20.7068 11.0444 21 12.0111 21 13C20.9984 14.3256 20.4711 15.5964 19.5338 16.5338C18.5964 17.4711 17.3256 17.9984 16 18ZM16 10C15.4067 10 14.8266 10.1759 14.3333 10.5056C13.8399 10.8352 13.4554 11.3038 13.2284 11.852C13.0013 12.4001 12.9419 13.0033 13.0576 13.5853C13.1734 14.1672 13.4591 14.7018 13.8787 15.1213C14.2982 15.5409 14.8328 15.8266 15.4147 15.9424C15.9967 16.0581 16.5999 15.9987 17.1481 15.7716C17.6962 15.5446 18.1648 15.1601 18.4944 14.6667C18.8241 14.1734 19 13.5933 19 13C18.9992 12.2046 18.6829 11.442 18.1204 10.8796C17.558 10.3171 16.7954 10.0008 16 10Z" fill="#729F9F"/>
                                    <path d="M16 30L7.56401 20.051C7.44679 19.9016 7.33079 19.7513 7.21601 19.6C5.77499 17.7018 4.99652 15.3832 5.00001 13C5.00001 10.0826 6.15894 7.28473 8.22184 5.22183C10.2847 3.15893 13.0826 2 16 2C18.9174 2 21.7153 3.15893 23.7782 5.22183C25.8411 7.28473 27 10.0826 27 13C27.0035 15.3821 26.2254 17.6996 24.785 19.597L24.784 19.6C24.784 19.6 24.484 19.994 24.439 20.047L16 30ZM8.81201 18.395C8.81401 18.395 9.04601 18.703 9.09901 18.769L16 26.908L22.91 18.758C22.954 18.703 23.188 18.393 23.189 18.392C24.3662 16.8411 25.0023 14.947 25 13C25 10.6131 24.0518 8.32387 22.364 6.63604C20.6761 4.94821 18.387 4 16 4C13.6131 4 11.3239 4.94821 9.63605 6.63604C7.94822 8.32387 7.00001 10.6131 7.00001 13C6.99791 14.9482 7.63479 16.8434 8.81301 18.395H8.81201Z" fill="#729F9F"/>
                                </svg>
                            </span>
                            <span>{{ $address }}</span>
                        </li>
                        <li>
                            <span class="rg-footer-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <path d="M12.3333 26.667H9.33333C7.91885 26.667 6.56229 26.1051 5.5621 25.1049C4.5619 24.1047 4 22.7481 4 21.3337V9.33366C4 7.91917 4.5619 6.56262 5.5621 5.56242C6.56229 4.56223 7.91885 4.00033 9.33333 4.00033H22C23.4145 4.00033 24.771 4.56223 25.7712 5.56242C26.7714 6.56262 27.3333 7.91917 27.3333 9.33366V13.3337M11 2.66699V5.33366M20.3333 2.66699V5.33366M4 10.667H27.3333M24 20.8577L22 22.8577" stroke="#729F9F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22.0002 29.3343C25.6821 29.3343 28.6668 26.3495 28.6668 22.6676C28.6668 18.9857 25.6821 16.001 22.0002 16.001C18.3183 16.001 15.3335 18.9857 15.3335 22.6676C15.3335 26.3495 18.3183 29.3343 22.0002 29.3343Z" stroke="#729F9F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span>
                                @foreach($scheduleLines as $line)
                                    {{ $line }}@if(! $loop->last)<br>@endif
                                @endforeach
                            </span>
                        </li>
                    </ul>
                    <img
                        src="{{ asset('images/refugio/hojas-footer.png') }}"
                        alt=""
                        class="rg-footer-leaves"
                        loading="lazy"
                        aria-hidden="true"
                    >
                </div>

                {{-- Columna nav + legal --}}
                <div class="rg-footer-right">
                    <div class="rg-footer-cols">
                        <ul class="rg-footer-nav">
                            <li><a href="{{ route('about') }}">Nosotros</a></li>
                            <li><a href="{{ route('contact') }}">Convocatorias</a></li>
                            <li><a href="{{ route('about') }}">Visítanos</a></li>
                        </ul>
                        <div class="rg-footer-legal-wrap">
                            <ul class="rg-footer-legal">
                                <li><a href="https://refugiogastronomico.pe/terminos-y-condiciones/" target="_blank" rel="noopener">Términos y condiciones</a></li>
                                <li><a href="https://refugiogastronomico.pe/politica-privacidad/" target="_blank" rel="noopener">Políticas de privacidad</a></li>
                                <li><a href="https://refugiogastronomico.pe/libro-de-reclamaciones/" target="_blank" rel="noopener">Libro de reclamaciones</a></li>
                            </ul>
                            <div class="rg-footer-social">
                                @if($settings->instagram_url)
                                    <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="Instagram">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                @endif
                                @if($settings->facebook_url)
                                    <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="Facebook">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                    </a>
                                @endif
                                @if($settings->tiktok_url)
                                    <a href="{{ $settings->tiktok_url }}" target="_blank" rel="noopener" class="rg-footer-social-icon" aria-label="TikTok">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="rg-footer-copy">© {{ date('Y') }} Refugio • diseñado por GCB</p>
        </div>
    </div>
</footer>
