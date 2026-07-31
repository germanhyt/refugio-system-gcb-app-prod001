<?php

namespace App\Services\Scraper;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class EventScraper
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
        $listingMeta = $this->parseListingMeta();
        $urls = $this->sitemaps->discover()['events'];

        $imported = 0;
        $failed = 0;
        $failures = [];

        foreach ($urls as $url) {
            try {
                $slug = trim(Str::after($url, '/eventos/'), '/');
                if ($slug === '') {
                    continue;
                }

                $html = $this->fetcher->get($url);
                $crawler = new Crawler($html);

                $title = $this->meta($crawler, 'og:title')
                    ?: $this->text($crawler, 'h1')
                    ?: Str::headline(str_replace('-', ' ', $slug));

                $description = $this->meta($crawler, 'og:description');
                $image = $this->meta($crawler, 'og:image');

                $eventDate = $listingMeta[$slug]['date']
                    ?? $this->fallbackDateFromModified($crawler)
                    ?? now()->toDateString();

                $event = Event::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => html_entity_decode(trim($title)),
                        'event_date' => $eventDate,
                        'description' => $description ? html_entity_decode(trim($description)) : null,
                        'is_active' => true,
                    ]
                );

                if ($image) {
                    $this->images->attachFromUrl(
                        $event,
                        $image,
                        'featured_image',
                        "events/{$slug}",
                        'featured',
                        $force
                    );
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failures[] = $url;
                Log::channel('scraper')->error('Event import failed', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'failed', 'failures');
    }

    /**
     * @return array<string, array{day: string, number: string, date: string}>
     */
    private function parseListingMeta(): array
    {
        $html = $this->fetcher->get("{$this->baseUrl}/eventos/");
        $meta = [];

        // Cards expose day abbr + number near the event title/link.
        if (preg_match_all(
            '#>(Jue|Sáb|Vie|Lun|Mar|Mié|Dom)<.*?>(\d{1,2})</.*?href="https://refugiogastronomico\.pe/eventos/([^"/]+)/"#s',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            $year = (int) config('refugio.events_year', now()->year);
            $month = (int) config('refugio.events_month', now()->month);

            foreach ($matches as $match) {
                $day = (int) $match[2];
                $slug = $match[3];
                try {
                    $date = Carbon::createFromDate($year, $month, $day)->toDateString();
                } catch (\Throwable) {
                    $date = now()->toDateString();
                }

                $meta[$slug] = [
                    'day' => $match[1],
                    'number' => $match[2],
                    'date' => $date,
                ];
            }
        }

        return $meta;
    }

    private function fallbackDateFromModified(Crawler $crawler): ?string
    {
        $nodes = $crawler->filter('time[datetime]');
        if ($nodes->count() === 0) {
            return null;
        }

        try {
            return Carbon::parse($nodes->first()->attr('datetime'))->toDateString();
        } catch (\Throwable) {
            return null;
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
