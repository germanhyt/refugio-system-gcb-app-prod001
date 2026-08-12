<?php

namespace App\Services\Scraper;

use App\Models\HomeRestaurantFeature;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class RestaurantScraper
{
    public function __construct(
        private readonly HttpFetcher $fetcher,
        private readonly SitemapParser $sitemaps,
        private readonly ImageDownloader $images,
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{imported: int, failed: int, failures: list<string>}
     */
    public function import(bool $force = false): array
    {
        $urls = $this->sitemaps->discover()['restaurants'];
        $listingAssets = $this->parseListingAssets();
        $categoryMap = $this->fetchCategoryMap();

        $imported = 0;
        $failed = 0;
        $failures = [];

        foreach ($urls as $index => $url) {
            try {
                $slug = trim(Str::after($url, '/restaurantes/'), '/');
                if ($slug === '' || in_array($slug, ['page', 'feed'], true)) {
                    continue;
                }

                $html = $this->fetcher->get($url);
                $crawler = new Crawler($html);

                $name = $this->meta($crawler, 'og:title')
                    ?: $this->text($crawler, 'h1')
                    ?: Str::headline(str_replace('-', ' ', $slug));

                $description = $this->meta($crawler, 'og:description')
                    ?: $this->meta($crawler, 'description');

                $featuredUrl = $listingAssets[$slug]['featured']
                    ?? $this->meta($crawler, 'og:image');
                $logoUrl = $listingAssets[$slug]['logo'] ?? null;

                $mapsUrl = null;
                $crawler->filter('a[href*="google.com/maps"]')->each(function (Crawler $node) use (&$mapsUrl) {
                    $mapsUrl ??= $node->attr('href');
                });

                $whatsapp = null;
                $crawler->filter('a[href*="wa.link"], a[href*="api.whatsapp.com"], a[href*="wa.me"]')->each(function (Crawler $node) use (&$whatsapp) {
                    $whatsapp ??= $node->attr('href');
                });

                $rappiUrl = null;
                $crawler->filter('a[href*="rappi."], a[href*="rappi.com"]')->each(function (Crawler $node) use (&$rappiUrl) {
                    $rappiUrl ??= $node->attr('href');
                });

                $peyaUrl = null;
                $crawler->filter('a[href*="pedidosya."], a[href*="peya."]')->each(function (Crawler $node) use (&$peyaUrl) {
                    $peyaUrl ??= $node->attr('href');
                });

                $shortDescription = $description ? html_entity_decode(trim($description)) : null;

                $payload = [
                    'name' => html_entity_decode(trim($name)),
                    'description' => $shortDescription,
                    'google_maps_url' => $mapsUrl,
                    'whatsapp_url' => $whatsapp,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'meta_title' => html_entity_decode(trim($name)).' | Refugio Gastronómico',
                    'meta_description' => $shortDescription,
                ];

                $existing = Restaurant::query()->where('slug', $slug)->first();
                if (! $existing || blank($existing->short_description)) {
                    $payload['short_description'] = $shortDescription;
                }
                if ($rappiUrl) {
                    $payload['delivery_rappi_enabled'] = true;
                    $payload['delivery_rappi_url'] = $rappiUrl;
                }
                if ($peyaUrl) {
                    $payload['delivery_peya_enabled'] = true;
                    $payload['delivery_peya_url'] = $peyaUrl;
                }

                $restaurant = Restaurant::query()->updateOrCreate(
                    ['slug' => $slug],
                    $payload
                );

                foreach ([
                    ['url' => $featuredUrl, 'collection' => 'featured_image', 'prefix' => 'featured'],
                    ['url' => $logoUrl, 'collection' => 'logo', 'prefix' => 'logo'],
                ] as $mediaItem) {
                    if (! $mediaItem['url']) {
                        continue;
                    }

                    try {
                        $this->images->attachFromUrl(
                            $restaurant,
                            $mediaItem['url'],
                            $mediaItem['collection'],
                            "restaurants/{$slug}",
                            $mediaItem['prefix'],
                            $force
                        );
                    } catch (\Throwable $mediaException) {
                        Log::channel('scraper')->warning('Restaurant media skipped', [
                            'slug' => $slug,
                            'collection' => $mediaItem['collection'],
                            'error' => $mediaException->getMessage(),
                        ]);
                    }
                }

                $categorySlugs = $categoryMap[$slug] ?? [];
                if ($categorySlugs !== []) {
                    $categoryIds = RestaurantCategory::query()
                        ->whereIn('slug', $categorySlugs)
                        ->pluck('id')
                        ->all();
                    $restaurant->categories()->sync($categoryIds);
                }

                HomeRestaurantFeature::query()->updateOrCreate(
                    ['restaurant_id' => $restaurant->id],
                    [
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failures[] = $url;
                Log::channel('scraper')->error('Restaurant import failed', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'failed', 'failures');
    }

    /**
     * Logos and featured images from the archive grid markup.
     *
     * @return array<string, array{logo: ?string, featured: ?string}>
     */
    private function parseListingAssets(): array
    {
        $map = [];

        foreach (["{$this->baseUrl}/restaurantes/", "{$this->baseUrl}/restaurantes/page/2/"] as $listingUrl) {
            try {
                $html = $this->fetcher->get($listingUrl);
            } catch (\Throwable) {
                continue;
            }

            if (! preg_match_all(
                '#jet-listing-dynamic-post-\d+"[\s\S]*?background-image:url\("([^"]+)"\);[\s\S]*?href="https://refugiogastronomico\.pe/restaurantes/([^"/]+)/"[\s\S]*?(?:data-lazy-src="(https://refugiogastronomico\.pe/wp-content/uploads/[^"]+)"|src="(https://refugiogastronomico\.pe/wp-content/uploads/[^"]+)")#',
                $html,
                $matches,
                PREG_SET_ORDER
            )) {
                continue;
            }

            foreach ($matches as $match) {
                $slug = $match[2];
                if (in_array($slug, ['page', 'feed'], true)) {
                    continue;
                }

                $map[$slug] = [
                    'logo' => $match[1] ?: null,
                    'featured' => $match[3] ?: ($match[4] ?? null),
                ];
            }
        }

        return $map;
    }

    /**
     * slug => list of category slugs from WP REST.
     *
     * @return array<string, list<string>>
     */
    private function fetchCategoryMap(): array
    {
        try {
            $catsResponse = Http::timeout(30)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp-json/wp/v2/categoria-de-restaurante", [
                    'per_page' => 100,
                ]);

            $restResponse = Http::timeout(45)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp-json/wp/v2/restaurantes", [
                    'per_page' => 100,
                    '_fields' => 'slug,categoria-de-restaurante',
                ]);

            if (! $catsResponse->successful() || ! $restResponse->successful()) {
                return [];
            }

            $catById = collect($catsResponse->json())
                ->mapWithKeys(fn (array $cat) => [(int) $cat['id'] => (string) $cat['slug']]);

            // Ensure local category rows exist.
            foreach ($catsResponse->json() as $index => $cat) {
                RestaurantCategory::query()->updateOrCreate(
                    ['slug' => $cat['slug']],
                    [
                        'name' => html_entity_decode((string) $cat['name']),
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }

            $map = [];
            foreach ($restResponse->json() as $item) {
                $slug = (string) ($item['slug'] ?? '');
                if ($slug === '') {
                    continue;
                }

                $ids = $item['categoria-de-restaurante'] ?? [];
                $map[$slug] = collect($ids)
                    ->map(fn ($id) => $catById[(int) $id] ?? null)
                    ->filter()
                    ->values()
                    ->all();
            }

            return $map;
        } catch (\Throwable $e) {
            Log::channel('scraper')->warning('Restaurant category sync failed', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function meta(Crawler $crawler, string $property): ?string
    {
        $nodes = $crawler->filter("meta[property='{$property}'], meta[name='{$property}']");
        if ($nodes->count() === 0) {
            return null;
        }

        return $nodes->first()->attr('content');
    }

    private function text(Crawler $crawler, string $selector): ?string
    {
        $nodes = $crawler->filter($selector);
        if ($nodes->count() === 0) {
            return null;
        }

        return trim($nodes->first()->text(''));
    }
}
