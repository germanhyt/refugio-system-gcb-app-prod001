<?php

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MirroredPageScraper
{
    /** @var array<string, string> */
    private const PAGE_MAP = [
        'terminos-y-condiciones' => 'terminos-y-condiciones',
        'politica-privacidad' => 'politica-privacidad',
        'politicas-de-privacidad' => 'politica-privacidad',
        'libro-de-reclamaciones' => 'libro-de-reclamaciones',
        'convocatoria' => 'convocatorias',
        'convocatorias' => 'convocatorias',
        'convoctaria' => 'convocatorias',
        'contacto' => 'contacto',
    ];

    /** @var array<string, string> */
    private const TITLE_MAP = [
        'terminos-y-condiciones' => 'Términos y condiciones',
        'politica-privacidad' => 'Políticas de privacidad',
        'libro-de-reclamaciones' => 'Libro de reclamaciones',
        'convocatorias' => 'Convocatorias',
        'contacto' => 'Contacto',
    ];

    private bool $originUnreachable = false;

    public function __construct(
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string, is_fallback?: bool}
     */
    public function get(string $alias): array
    {
        $alias = trim(Str::lower($alias), '/');
        $remoteSlug = self::PAGE_MAP[$alias] ?? null;

        if ($remoteSlug === null) {
            return $this->fallback('pagina');
        }

        $cacheKey = "mirrored_page:{$remoteSlug}";
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && ($cached['content'] ?? '') !== '') {
            return $this->rewritePublicContactEmail($cached);
        }

        // Avoid slow remote calls during automated tests.
        if (app()->runningUnitTests()) {
            return $this->rewritePublicContactEmail($this->fallback($remoteSlug));
        }

        // Post-cutover guard: si el origen es este mismo sitio (p.ej. tras migrar el
        // dominio del WordPress al Laravel), scrapear sería recursivo. Servir
        // fallback y no arriesgar una cascada de requests contra nosotros mismos.
        if ($this->isSelfReferencing()) {
            return $this->rewritePublicContactEmail($this->fallback($remoteSlug));
        }

        // Circuit breaker: if origin recently timed out, serve fallback immediately.
        if (Cache::get('mirrored_page:origin_down')) {
            return $this->rewritePublicContactEmail($this->fallback($remoteSlug));
        }

        $payload = $this->fetch($remoteSlug);

        if ($payload !== null && trim(strip_tags($payload['content'] ?? '')) !== '') {
            Cache::put($cacheKey, $payload, now()->addHours(6));

            return $this->rewritePublicContactEmail($payload);
        }

        // Never cache failures: remote site may recover later.
        return $this->rewritePublicContactEmail($this->fallback($remoteSlug));
    }

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string}|null
     */
    private function fetch(string $remoteSlug): ?array
    {
        $fromRest = $this->fetchFromRest($remoteSlug);
        if ($fromRest !== null) {
            return $fromRest;
        }

        // If the origin host is unreachable, skip the HTML attempt.
        if ($this->originUnreachable) {
            return null;
        }

        return $this->fetchFromHtml($remoteSlug);
    }

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string}|null
     */
    private function fetchFromRest(string $remoteSlug): ?array
    {
        try {
            $url = "{$this->baseUrl}/wp-json/wp/v2/pages?slug=".rawurlencode($remoteSlug).'&status=publish&_embed=1';
            $response = $this->http()->get($url);

            if (! $response->successful()) {
                return null;
            }

            $items = $response->json();
            if (! is_array($items) || $items === []) {
                return null;
            }

            $item = $items[0];
            $rawTitle = (string) data_get($item, 'title.rendered', self::TITLE_MAP[$remoteSlug] ?? Str::headline($remoteSlug));
            $rawContent = (string) data_get($item, 'content.rendered', '');
            $sourceUrl = (string) data_get($item, 'link', "{$this->baseUrl}/{$remoteSlug}/");

            $title = html_entity_decode(strip_tags($rawTitle), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $content = $this->sanitizeHtml($rawContent);
            $heroImage = data_get($item, '_embedded.wp:featuredmedia.0.source_url');
            if (! is_string($heroImage) || $heroImage === '') {
                $heroImage = null;
            }

            return [
                'title' => $title !== '' ? $title : (self::TITLE_MAP[$remoteSlug] ?? Str::headline($remoteSlug)),
                'content' => $content,
                'source_url' => $sourceUrl,
                'remote_slug' => $remoteSlug,
                'hero_image' => $heroImage,
            ];
        } catch (\Throwable $e) {
            $this->markOriginUnreachable($e);
            Log::warning('Mirrored page REST fetch failed', [
                'slug' => $remoteSlug,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string}|null
     */
    private function fetchFromHtml(string $remoteSlug): ?array
    {
        try {
            $url = "{$this->baseUrl}/{$remoteSlug}/";
            $response = $this->http()->get($url);

            if (! $response->successful()) {
                return null;
            }

            $html = $response->body();

            $title = self::TITLE_MAP[$remoteSlug] ?? Str::headline($remoteSlug);
            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $candidate = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($candidate !== '') {
                    $title = $candidate;
                }
            }

            $content = '';
            $patterns = [
                '/<main[^>]*>(.*?)<\/main>/is',
                '/<article[^>]*>(.*?)<\/article>/is',
                '/<div[^>]*class="[^"]*entry-content[^"]*"[^>]*>(.*?)<\/div>/is',
                '/<body[^>]*>(.*?)<\/body>/is',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $html, $m)) {
                    $content = $m[1];
                    break;
                }
            }

            if ($content === '') {
                return null;
            }

            $heroImage = null;
            if (preg_match('/property=["\']og:image["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
                $heroImage = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            return [
                'title' => $title,
                'content' => $this->sanitizeHtml($content),
                'source_url' => $url,
                'remote_slug' => $remoteSlug,
                'hero_image' => $heroImage,
            ];
        } catch (\Throwable $e) {
            $this->markOriginUnreachable($e);
            Log::warning('Mirrored page HTML fetch failed', [
                'slug' => $remoteSlug,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function markOriginUnreachable(\Throwable $e): void
    {
        $message = Str::lower($e->getMessage());
        if (str_contains($message, 'timed out')
            || str_contains($message, 'curl error 28')
            || str_contains($message, 'could not resolve')
            || str_contains($message, 'failed to connect')
            || str_contains($message, 'connection refused')) {
            $this->originUnreachable = true;
            Cache::put('mirrored_page:origin_down', true, now()->addMinutes(10));
        }
    }

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string, is_fallback: bool}
     */
    private function fallback(string $remoteSlug): array
    {
        $title = self::TITLE_MAP[$remoteSlug] ?? Str::headline($remoteSlug);

        $content = match ($remoteSlug) {
            'contacto' => '<p>Estamos para ayudarte. Completa el formulario y te contactaremos pronto.</p>',
            'convocatorias' => '<p>¿Quieres ser parte de Refugio Gastronómico? Cuéntanos sobre tu marca o propuesta.</p>',
            'libro-de-reclamaciones' => '<p>Conforme a lo establecido por el Código de Protección y Defensa del Consumidor, ponemos a tu disposición el Libro de Reclamaciones.</p><p>Completa el formulario con tus datos y el detalle de tu queja o reclamo. Te responderemos en un plazo no mayor a 30 días calendario.</p>',
            'terminos-y-condiciones' => '<p>Estos términos y condiciones regulan el uso del sitio web y los servicios de Refugio Gastronómico.</p><p>El contenido definitivo se sincronizará cuando el origen esté disponible. Si necesitas una copia formal, escríbenos a <a href="mailto:leilah@gcb.pe">leilah@gcb.pe</a>.</p>',
            'politica-privacidad' => '<p>En Refugio Gastronómico protegemos tus datos personales conforme a la normativa vigente.</p><p>El texto completo de la política se sincronizará cuando el origen esté disponible. Para ejercer tus derechos ARCO, contáctanos a <a href="mailto:leilah@gcb.pe">leilah@gcb.pe</a>.</p>',
            default => '<p>Contenido temporalmente no disponible. Intenta nuevamente en unos minutos.</p>',
        };

        return $this->rewritePublicContactEmail([
            'title' => $title,
            'content' => $content,
            'source_url' => url('/'.$remoteSlug),
            'remote_slug' => $remoteSlug,
            'hero_image' => null,
            'is_fallback' => true,
        ]);
    }

    /**
     * @param  array{title?: string, content?: string, source_url?: string, remote_slug?: string, hero_image?: ?string, is_fallback?: bool}  $payload
     * @return array{title?: string, content?: string, source_url?: string, remote_slug?: string, hero_image?: ?string, is_fallback?: bool}
     */
    private function rewritePublicContactEmail(array $payload): array
    {
        if (isset($payload['content'])) {
            $payload['content'] = str_ireplace(
                'hola@refugiogastronomico.pe',
                'leilah@gcb.pe',
                (string) $payload['content']
            );
        }

        return $payload;
    }

    private function isSelfReferencing(): bool
    {
        $baseHost = parse_url($this->baseUrl, PHP_URL_HOST);
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $baseHost !== null
            && $appHost !== null
            && strcasecmp((string) $baseHost, (string) $appHost) === 0;
    }

    private function http()
    {
        // Short timeout: these pages must not hang the public site when the source is down.
        return Http::withHeaders([
            'User-Agent' => 'RefugioSite/1.0 (+local mirror)',
            'Accept' => 'text/html,application/json,*/*;q=0.8',
        ])->timeout(4)->connectTimeout(2)->retry(0, 0);
    }

    private function sanitizeHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html) ?? $html;
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/<form\b[^>]*>.*?<\/form>/is', '', $html) ?? $html;
        $html = preg_replace('/<(header|footer|nav|aside)\b[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $html = preg_replace('/<h1\b[^>]*>.*?<\/h1>/is', '', $html) ?? $html;
        $html = preg_replace('/<(div|span)[^>]*class="[^"]*elementor-widget-button[^"]*"[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $html = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/is', '', $html) ?? $html;

        $html = preg_replace_callback('/\s(href|src)=["\']([^"\']+)["\']/i', function (array $m): string {
            $attr = strtolower($m[1]);
            $value = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = $this->rewriteUrl($value);

            return " {$attr}=\"".e($value)."\"";
        }, $html) ?? $html;

        $html = preg_replace('/<(p|div)[^>]*>\s*(?:&nbsp;|\s|<br\s*\/?>)*\s*<\/\1>/i', '', $html) ?? $html;
        $html = preg_replace('/\n{3,}/', "\n\n", $html) ?? $html;

        return trim($html);
    }

    private function rewriteUrl(string $url): string
    {
        $normalized = preg_replace('/^https?:\/\/(www\.)?refugiogastronomico\.pe/i', '', $url) ?? $url;
        $normalized = str_starts_with($normalized, '/') ? $normalized : $url;
        $path = trim((string) parse_url($normalized, PHP_URL_PATH), '/');

        if ($path === '') {
            return $normalized;
        }

        return match ($path) {
            'terminos-y-condiciones' => '/terminos-y-condiciones',
            'politica-privacidad', 'politicas-de-privacidad' => '/politica-privacidad',
            'libro-de-reclamaciones' => '/libro-de-reclamaciones',
            'convocatorias', 'convocatoria' => '/convocatoria',
            'contacto' => '/contacto',
            default => $normalized,
        };
    }
}
