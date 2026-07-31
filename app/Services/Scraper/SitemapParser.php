<?php

namespace App\Services\Scraper;

use Illuminate\Support\Str;

class SitemapParser
{
    public function __construct(
        private readonly HttpFetcher $fetcher,
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{restaurants: string[], events: string[], pages: string[]}
     */
    public function discover(): array
    {
        $restaurants = $this->urlsFromSitemap("{$this->baseUrl}/restaurantes-sitemap.xml", '/restaurantes/');
        $events = $this->urlsFromSitemap("{$this->baseUrl}/eventos-sitemap.xml", '/eventos/');
        $pages = $this->urlsFromSitemap("{$this->baseUrl}/page-sitemap.xml");

        $restaurants = array_values(array_filter(
            array_unique($restaurants),
            fn (string $url) => ! Str::endsWith(rtrim($url, '/'), '/restaurantes')
                && ! Str::contains($url, ['/page/', '/feed/'])
        ));

        $events = array_values(array_filter(
            array_unique($events),
            fn (string $url) => ! Str::endsWith(rtrim($url, '/'), '/eventos')
                && ! Str::contains($url, ['/page/', '/feed/'])
        ));

        $wantedPages = [
            'contacto',
            'nosotros',
            'convocatorias',
            'politica-privacidad',
            'terminos-y-condiciones',
            'libro-de-reclamaciones',
        ];

        $pages = array_values(array_filter(
            array_unique($pages),
            function (string $url) use ($wantedPages) {
                $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');

                return in_array($path, $wantedPages, true);
            }
        ));

        sort($restaurants);
        sort($events);
        sort($pages);

        return compact('restaurants', 'events', 'pages');
    }

    /**
     * @return list<string>
     */
    private function urlsFromSitemap(string $sitemapUrl, ?string $mustContain = null): array
    {
        $xml = $this->fetcher->get($sitemapUrl);
        $urls = [];

        if (preg_match_all('#<loc>(.*?)</loc>#i', $xml, $matches)) {
            foreach ($matches[1] as $loc) {
                $loc = html_entity_decode(trim($loc));
                if ($mustContain !== null && ! str_contains($loc, $mustContain)) {
                    continue;
                }
                $urls[] = $loc;
            }
        }

        return $urls;
    }
}
