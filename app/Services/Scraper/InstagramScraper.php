<?php

namespace App\Services\Scraper;

use App\Models\InstagramPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstagramScraper
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
        } catch (\Throwable $e) {
            Log::channel('scraper')->error('Instagram home fetch failed', ['error' => $e->getMessage()]);

            return ['imported' => 0, 'failed' => 1, 'failures' => ['home']];
        }

        $posts = $this->parseSmashBalloon($html);

        foreach ($posts as $index => $post) {
            try {
                $model = InstagramPost::query()->updateOrCreate(
                    ['external_id' => $post['external_id']],
                    [
                        'permalink' => $post['permalink'],
                        'media_type' => $post['media_type'],
                        'caption' => $post['caption'],
                        'likes_count' => $post['likes_count'],
                        'comments_count' => $post['comments_count'],
                        'source_image_url' => $post['image_url'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]
                );

                if ($post['image_url'] !== '') {
                    try {
                        $this->images->attachFromUrl(
                            $model,
                            $post['image_url'],
                            'image',
                            'instagram/'.$post['external_id'],
                            'thumb',
                            $force
                        );
                    } catch (\Throwable $e) {
                        // CDN URLs expire; keep the post without local media.
                        Log::channel('scraper')->warning('IG image download failed', [
                            'id' => $post['external_id'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failures[] = $post['permalink'];
                Log::channel('scraper')->error('Instagram import failed', [
                    'url' => $post['permalink'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'failed', 'failures');
    }

    /**
     * @return list<array{external_id: string, permalink: string, media_type: string, caption: string, likes_count: int, comments_count: int, image_url: string}>
     */
    private function parseSmashBalloon(string $html): array
    {
        $posts = [];

        if (! preg_match_all(
            '/id="(sbi_\d+)"[\s\S]*?data-url="(https:\/\/www\.instagram\.com\/[^"]+)"[\s\S]*?data-full-res="([^"]+)"[\s\S]*?<span\s+class="sbi_caption">([\s\S]*?)<\/span>[\s\S]*?<span\s+class="sbi_likes"[^>]*>[\s\S]*?<\/svg>\s*(\d+)[\s\S]*?<span\s+class="sbi_comments"[^>]*>[\s\S]*?<\/svg>\s*(\d+)/u',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            // Softer fallback: id + permalink + full-res only
            if (! preg_match_all(
                '/id="(sbi_\d+)"[\s\S]{0,4000}?data-url="(https:\/\/www\.instagram\.com\/[^"]+)"[\s\S]{0,2000}?data-full-res="([^"]+)"/u',
                $html,
                $matches,
                PREG_SET_ORDER
            )) {
                return [];
            }

            foreach ($matches as $index => $m) {
                $permalink = html_entity_decode($m[2]);
                $posts[] = [
                    'external_id' => $m[1],
                    'permalink' => $permalink,
                    'media_type' => str_contains($permalink, '/reel/') ? 'reel' : 'image',
                    'caption' => '',
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'image_url' => html_entity_decode($m[3]),
                ];
            }

            return array_slice($posts, 0, 12);
        }

        foreach ($matches as $m) {
            $permalink = html_entity_decode($m[2]);
            $caption = trim(html_entity_decode(strip_tags(str_replace('<br>', "\n", $m[4]))));
            $posts[] = [
                'external_id' => $m[1],
                'permalink' => $permalink,
                'media_type' => str_contains($permalink, '/reel/') ? 'reel' : 'image',
                'caption' => Str::limit($caption, 500),
                'likes_count' => (int) $m[5],
                'comments_count' => (int) $m[6],
                'image_url' => html_entity_decode($m[3]),
            ];
        }

        return array_slice($posts, 0, 12);
    }
}
