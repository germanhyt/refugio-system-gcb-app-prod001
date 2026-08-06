<?php

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Cache;
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

    public function __construct(
        private readonly HttpFetcher $fetcher,
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string}|null
     */
    public function get(string $alias): ?array
    {
        $alias = trim(Str::lower($alias), '/');
        $remoteSlug = self::PAGE_MAP[$alias] ?? null;

        if ($remoteSlug === null) {
            return null;
        }

        $cacheKey = "mirrored_page:{$remoteSlug}";

        return Cache::remember($cacheKey, now()->addHours(6), fn () => $this->fetch($remoteSlug));
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

        return $this->fetchFromHtml($remoteSlug);
    }

    /**
     * @return array{title: string, content: string, source_url: string, remote_slug: string, hero_image: ?string}|null
     */
    private function fetchFromRest(string $remoteSlug): ?array
    {
        try {
            $url = "{$this->baseUrl}/wp-json/wp/v2/pages?slug=".rawurlencode($remoteSlug).'&status=publish&_embed=1';
            $json = $this->fetcher->get($url);
            $items = json_decode($json, true);

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
                $heroImage = $this->fetchOgImage("{$this->baseUrl}/{$remoteSlug}/");
            }

            return [
                'title' => $title !== '' ? $title : (self::TITLE_MAP[$remoteSlug] ?? Str::headline($remoteSlug)),
                'content' => $content,
                'source_url' => $sourceUrl,
                'remote_slug' => $remoteSlug,
                'hero_image' => is_string($heroImage) ? $heroImage : null,
            ];
        } catch (\Throwable) {
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
            $html = $this->fetcher->get($url);

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
        } catch (\Throwable) {
            return null;
        }
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
        // Title lives in the page hero; drop duplicated headings from WP content.
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

    private function fetchOgImage(string $url): ?string
    {
        try {
            $html = $this->fetcher->get($url);
            if (preg_match('/property=["\']og:image["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
                return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        } catch (\Throwable) {
            // ignore
        }

        return null;
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
            'politica-privacidad' => '/politica-privacidad',
            'libro-de-reclamaciones' => '/libro-de-reclamaciones',
            'convocatorias' => '/convocatoria',
            'contacto' => '/contacto',
            default => $normalized,
        };
    }
}
