<?php

namespace App\Services\Scraper;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogScraper
{
    /** @var list<array{url: string, title: string, author: string, rating: float, image: string}> */
    private const FALLBACK_POSTS = [
        [
            'url' => 'https://refugiogastronomico.pe/saltao-wok-food/',
            'title' => 'Saltao Wok Food',
            'author' => '@KOCINAS',
            'rating' => 3.9,
            'image' => 'https://refugiogastronomico.pe/wp-content/uploads/2022/07/SALTAO_WOK_FOOD_WEB_1-1024x536.jpg',
        ],
        [
            'url' => 'https://refugiogastronomico.pe/somos-ana-un-lindo-concepto-detras-de-deliciosos-platos/',
            'title' => 'Somos Ana: Un lindo concepto detrás de deliciosos platos.',
            'author' => '@basicfoodielima',
            'rating' => 4.7,
            'image' => 'https://refugiogastronomico.pe/wp-content/uploads/2022/05/ANA-Blog-1-1024x536.jpg',
        ],
        [
            'url' => 'https://refugiogastronomico.pe/sapiens-un-steakhouse-donde-el-plato-principal-es-la-innovacion/',
            'title' => 'Sapiens, un steakhouse donde el plato principal es la innovación.',
            'author' => '@foodieperuanaporelmundo',
            'rating' => 4.6,
            'image' => 'https://refugiogastronomico.pe/wp-content/uploads/2022/02/1-Blog-1024x536.jpg',
        ],
        [
            'url' => 'https://refugiogastronomico.pe/la-gloria-una-de-las-cocinas-con-mas-historia-de-lima/',
            'title' => 'La Gloria, una de las cocinas con más historia de Lima.',
            'author' => '@foodieperuanaporelmundo',
            'rating' => 4.0,
            'image' => 'https://refugiogastronomico.pe/wp-content/uploads/2022/02/1-20-1024x536.jpg',
        ],
    ];

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

        $posts = $this->discoverFromHome() ?: self::FALLBACK_POSTS;

        foreach ($posts as $index => $post) {
            try {
                $slug = Str::slug(parse_url($post['url'], PHP_URL_PATH) ?: $post['title']);
                $model = BlogPost::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $post['title'],
                        'author_handle' => $post['author'],
                        'category' => 'Sin categoría',
                        'rating' => $post['rating'],
                        'external_url' => $post['url'],
                        'sort_order' => $index,
                        'is_active' => true,
                        'is_featured' => true,
                        'published_at' => now()->subDays(count($posts) - $index),
                    ]
                );

                if (! empty($post['image'])) {
                    $this->images->attachFromUrl(
                        $model,
                        $post['image'],
                        'featured_image',
                        'blog/'.$slug,
                        'cover',
                        $force
                    );
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failures[] = $post['url'];
                Log::channel('scraper')->error('Blog import failed', [
                    'url' => $post['url'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'failed', 'failures');
    }

    /**
     * @return list<array{url: string, title: string, author: string, rating: float, image: string}>|null
     */
    private function discoverFromHome(): ?array
    {
        try {
            $html = $this->fetcher->get("{$this->baseUrl}/");
        } catch (\Throwable $e) {
            Log::channel('scraper')->warning('Blog home fetch failed', ['error' => $e->getMessage()]);

            return null;
        }

        $posts = [];
        if (preg_match_all(
            '/<span class="elementor-heading-title[^"]*">(@[\w.]+)<\/span>[\s\S]{0,2000}?<a href="(https:\/\/refugiogastronomico\.pe\/[^"]+\/)"[^>]*>([\s\S]*?)<\/a>[\s\S]{0,1500}?(?:title="([\d.]+) out of 5"|([\d.]+)\s*\/\s*5)/u',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $m) {
                $url = html_entity_decode($m[2]);
                $title = trim(html_entity_decode(strip_tags($m[3])));
                $rating = (float) ($m[4] ?: $m[5]);
                $image = $this->findImageNear($html, $url) ?? $this->fallbackImageFor($url);

                $posts[] = [
                    'url' => $url,
                    'title' => $title,
                    'author' => $m[1],
                    'rating' => $rating,
                    'image' => $image ?? '',
                ];
            }
        }

        return $posts !== [] ? $posts : null;
    }

    private function findImageNear(string $html, string $url): ?string
    {
        $pos = strpos($html, $url);
        if ($pos === false) {
            return null;
        }

        $chunk = substr($html, max(0, $pos - 5000), 5000);
        if (preg_match_all('/(?:data-src|src)="(https:\/\/refugiogastronomico\.pe\/wp-content\/uploads\/[^"]+\.(?:jpg|jpeg|png|webp)[^"]*)"/i', $chunk, $m)) {
            return html_entity_decode(end($m[1]));
        }

        return null;
    }

    private function fallbackImageFor(string $url): ?string
    {
        foreach (self::FALLBACK_POSTS as $post) {
            if ($post['url'] === $url) {
                return $post['image'];
            }
        }

        return null;
    }
}
