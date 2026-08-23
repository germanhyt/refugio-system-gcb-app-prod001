<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Restaurant;
use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Illuminate\Support\Str;

class SeoService
{
    public const PARK_ID = '#refugio';

    public const DEFAULT_LATITUDE = -12.0842658;

    public const DEFAULT_LONGITUDE = -76.9734978;

    public const DEFAULT_PRICE_RANGE = '$$';

    public const PARK_AMENITIES = [
        'Pet-friendly',
        'Estacionamiento',
        'Música en vivo',
        'Zona infantil (Bosque Mágico)',
    ];

    /**
     * JSON-LD del parque como FoodEstablishment.
     */
    public function organizationJsonLd(SiteSetting $settings, VisitInfo $visit): string
    {
        $sameAs = array_values(array_filter([
            $settings->instagram_url,
            $settings->facebook_url,
            $settings->tiktok_url,
            $settings->youtube_url,
            url('/'),
        ]));

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'FoodEstablishment',
            '@id' => url('/').self::PARK_ID,
            'name' => $settings->site_name ?: 'Refugio Gastronómico',
            'url' => url('/'),
            'priceRange' => self::DEFAULT_PRICE_RANGE,
            'telephone' => $this->normalizedPhone($visit->phone_reservations),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $visit->address ?: 'Av. Javier Prado Este 4492',
                'addressLocality' => 'Santiago de Surco',
                'addressRegion' => 'Lima',
                'addressCountry' => 'PE',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => self::DEFAULT_LATITUDE,
                'longitude' => self::DEFAULT_LONGITUDE,
            ],
            'hasMap' => $visit->mapEmbedUrl(),
            'openingHours' => $this->scheduleText($visit),
            'amenityFeature' => array_map(
                fn (string $name) => [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $name,
                    'value' => true,
                ],
                self::PARK_AMENITIES
            ),
            'sameAs' => $sameAs,
        ];

        if ($settings->slogan) {
            $data['slogan'] = $settings->slogan;
        }

        if ($description = $settings->seo_description) {
            $data['description'] = $description;
        }

        if ($image = $settings->getFirstMediaUrl('og_image') ?: $settings->getFirstMediaUrl('logo')) {
            $data['image'] = $image;
        }

        return $this->encode($data);
    }

    /**
     * JSON-LD de un concepto de restaurante (FoodEstablishment).
     */
    public function restaurantJsonLd(Restaurant $restaurant, SiteSetting $settings): string
    {
        $sameAs = array_values(array_filter([
            $restaurant->website_url,
            $restaurant->instagram_url,
            $restaurant->facebook_url,
            $restaurant->tiktok_url,
        ]));

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'FoodEstablishment',
            'name' => $restaurant->name,
            'url' => route('restaurants.show', $restaurant),
            'parentOrganization' => [
                '@id' => url('/').self::PARK_ID,
                'name' => $settings->site_name ?: 'Refugio Gastronómico',
            ],
            'servesCuisine' => $restaurant->categories->isNotEmpty()
                ? $restaurant->categories->pluck('name')->all()
                : ['Peruana'],
        ];

        if ($description = $this->plainText($restaurant->meta_description ?: $restaurant->short_description ?: $restaurant->description)) {
            $data['description'] = Str::limit($description, 300);
        }

        if ($restaurant->google_maps_url) {
            $data['hasMap'] = $restaurant->google_maps_url;
        }

        if ($restaurant->reservation_phone) {
            $data['telephone'] = $this->normalizedPhone($restaurant->reservation_phone);
        }

        if ($sameAs !== []) {
            $data['sameAs'] = $sameAs;
        }

        if ($menu = $restaurant->getFirstMediaUrl('menu_pdf')) {
            $data['menu'] = $menu;
        }

        if ($image = $restaurant->bannerImageUrl() ?: $restaurant->featuredImageUrl() ?: $restaurant->logoUrl()) {
            $data['image'] = $image;
        }

        return $this->encode($data);
    }

    /**
     * JSON-LD BreadcrumbList.
     *
     * @param  array<int, array{name: string, url: ?string}>  $trail
     */
    public function breadcrumbJsonLd(array $trail): string
    {
        $items = [];
        foreach ($trail as $index => $crumb) {
            $entry = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
            ];
            if (! empty($crumb['url'])) {
                $entry['item'] = $crumb['url'];
            }
            $items[] = $entry;
        }

        return $this->encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ]);
    }

    /**
     * JSON-LD FAQPage desde el array de página (config/static-pages o StaticPage).
     *
     * @param  array  $page  Array con 'blocks' (type=faq, items[question,answer])
     */
    public function faqJsonLd(array $page): string
    {
        $questions = [];
        foreach ($page['blocks'] ?? [] as $block) {
            if (($block['type'] ?? null) !== 'faq') {
                continue;
            }
            foreach ($block['items'] ?? [] as $item) {
                if (empty($item['question']) || empty($item['answer'])) {
                    continue;
                }
                $questions[] = [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ];
            }
        }

        if ($questions === []) {
            return '';
        }

        return $this->encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ]);
    }

    /**
     * JSON-LD Event.
     */
    public function eventJsonLd(Event $event): string
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'url' => route('events.show', $event),
            'location' => [
                '@type' => 'FoodEstablishment',
                '@id' => url('/').self::PARK_ID,
            ],
        ];

        if ($event->event_date) {
            $data['startDate'] = $event->event_date->format('Y-m-d');
        }

        if ($event->event_time) {
            $data['startTime'] = $event->event_time;
        }

        if ($description = $this->plainText($event->description)) {
            $data['description'] = Str::limit($description, 300);
        }

        if ($image = $event->getFirstMediaUrl('featured_image')) {
            $data['image'] = $image;
        }

        return $this->encode($data);
    }

    /**
     * @param  array<string, mixed>  $trail
     */
    public function breadcrumbScript(array $trail): string
    {
        $json = $this->breadcrumbJsonLd($trail);

        return $this->wrap($json);
    }

    public function script(string $json): string
    {
        return $this->wrap($json);
    }

    protected function wrap(string $json): string
    {
        if ($json === '') {
            return '';
        }

        return '<script type="application/ld+json">'.$json.'</script>';
    }

    protected function encode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?: '';
    }

    protected function scheduleText(VisitInfo $visit): string
    {
        $schedule = $visit->schedule ?? [];

        if (! is_array($schedule) || $schedule === []) {
            return '';
        }

        return implode(' · ', array_map(
            static fn (array $row) => trim((string) ($row['days'] ?? '')).' '.trim((string) ($row['hours'] ?? '')),
            $schedule
        ));
    }

    protected function normalizedPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 9) {
            return '+51 '.$digits;
        }

        return $phone ?: null;
    }

    protected function plainText(?string $value): string
    {
        $plain = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $plain));
    }
}
