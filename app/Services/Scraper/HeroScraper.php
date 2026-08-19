<?php

namespace App\Services\Scraper;

use App\Models\HeroSlide;
use Illuminate\Support\Facades\Log;

class HeroScraper
{
    public function __construct(
        private readonly HttpFetcher $fetcher,
        private readonly ImageDownloader $images,
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{imported: int, failed: int, failures: list<string>}
     */
    public function import(bool $force = false): array
    {
        $imported = 0;
        $failed = 0;
        $failures = [];

        try {
            $html = $this->fetcher->get("{$this->baseUrl}/");

            $backgrounds = [];
            if (preg_match_all('#rev-slidebg[^>]*data-lazyload="([^"]+)"#i', $html, $matches)) {
                foreach ($matches[1] as $bg) {
                    $backgrounds[] = $this->images->normalizeUrl($bg);
                }
            }

            if ($backgrounds === []) {
                $backgrounds = [
                    'https://refugiogastronomico.pe/wp-content/uploads/2023/02/fondohome.jpg',
                    'https://refugiogastronomico.pe/wp-content/uploads/2024/06/1.png',
                ];
            }

            $slides = array_slice($backgrounds, 0, 3);

            if (! $force) {
                // Upsert by sort_order to stay idempotent.
            } else {
                HeroSlide::query()->delete();
            }

            foreach ($slides as $index => $background) {
                $slide = HeroSlide::query()->updateOrCreate(
                    ['sort_order' => $index + 1],
                    [
                        'title' => "¡DE TODO,\nPARA TODOS!",
                        'subtitle' => null,
                        'description' => null,
                        'cta_text' => null,
                        'cta_url' => null,
                        'is_active' => true,
                    ]
                );

                $this->images->attachFromUrl(
                    $slide,
                    $background,
                    'background_image',
                    'hero/'.($index + 1),
                    'background',
                    $force
                );

                $imported++;
            }
        } catch (\Throwable $e) {
            $failed++;
            $failures[] = $this->baseUrl.'/';
            Log::channel('scraper')->error('Hero import failed', ['error' => $e->getMessage()]);
        }

        return compact('imported', 'failed', 'failures');
    }
}
