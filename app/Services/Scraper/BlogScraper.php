<?php

namespace App\Services\Scraper;

use App\Models\BlogPost;
use Carbon\Carbon;
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

        $discovered = $this->discoverFromHome() ?: self::FALLBACK_POSTS;
        $bySlug = [];

        foreach ($discovered as $index => $post) {
            $slug = $this->slugFromUrl($post['url'], $post['title']);
            $bySlug[$slug] = [
                'url' => $post['url'],
                'title' => $post['title'],
                'author' => $post['author'],
                'rating' => $post['rating'],
                'image' => $post['image'] ?? '',
                'sort_order' => $index,
                'is_featured' => true,
            ];
        }

        foreach ($this->discoverFromRest() as $rest) {
            $slug = $rest['slug'];
            if (! isset($bySlug[$slug])) {
                $bySlug[$slug] = [
                    'url' => $rest['link'],
                    'title' => $rest['title'],
                    'author' => null,
                    'rating' => null,
                    'image' => $rest['image'] ?? '',
                    'sort_order' => 100 + count($bySlug),
                    'is_featured' => false,
                ];
            } else {
                if (empty($bySlug[$slug]['image']) && ! empty($rest['image'])) {
                    $bySlug[$slug]['image'] = $rest['image'];
                }
            }
        }

        foreach ($bySlug as $slug => $seed) {
            try {
                $detail = $this->fetchDetail($slug, $seed['url']);

                $model = BlogPost::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $detail['title'] ?: $seed['title'],
                        'author_handle' => $seed['author'],
                        'category' => $detail['category'] ?: 'Sin categoría',
                        'rating' => $seed['rating'],
                        'external_url' => $seed['url'],
                        'excerpt' => $detail['excerpt'],
                        'body' => $detail['body'],
                        'sort_order' => $seed['sort_order'],
                        'is_active' => true,
                        'is_featured' => $seed['is_featured'],
                        'published_at' => $detail['published_at'] ?? now()->subDays(max(1, $seed['sort_order'])),
                    ]
                );

                $image = $detail['image'] ?: ($seed['image'] ?? '');
                if ($image !== '') {
                    $this->images->attachFromUrl(
                        $model,
                        $image,
                        'featured_image',
                        'blog/'.$slug,
                        'cover',
                        $force
                    );
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failures[] = $seed['url'];
                Log::channel('scraper')->error('Blog import failed', [
                    'url' => $seed['url'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return compact('imported', 'failed', 'failures');
    }

    /**
     * @return array{title: string, excerpt: ?string, body: ?string, category: ?string, published_at: ?Carbon, image: ?string}
     */
    private function fetchDetail(string $slug, string $url): array
    {
        $fromRest = $this->fetchRestPostBySlug($slug);
        if ($fromRest !== null) {
            return $fromRest;
        }

        return $this->scrapeDetailHtml($url);
    }

    /**
     * @return array{title: string, excerpt: ?string, body: ?string, category: ?string, published_at: ?Carbon, image: ?string}|null
     */
    private function fetchRestPostBySlug(string $slug): ?array
    {
        try {
            $json = $this->fetcher->get("{$this->baseUrl}/wp-json/wp/v2/posts?slug=".rawurlencode($slug).'&_embed=1');
            $posts = json_decode($json, true);
            if (! is_array($posts) || $posts === []) {
                return null;
            }

            return $this->mapRestPost($posts[0]);
        } catch (\Throwable $e) {
            Log::channel('scraper')->warning('Blog REST detail failed', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return list<array{slug: string, title: string, link: string, image: ?string}>
     */
    private function discoverFromRest(): array
    {
        $out = [];

        try {
            for ($page = 1; $page <= 3; $page++) {
                $json = $this->fetcher->get("{$this->baseUrl}/wp-json/wp/v2/posts?per_page=20&page={$page}&_embed=1&status=publish");
                $posts = json_decode($json, true);
                if (! is_array($posts) || $posts === []) {
                    break;
                }

                foreach ($posts as $post) {
                    $mapped = $this->mapRestPost($post);
                    $out[] = [
                        'slug' => (string) ($post['slug'] ?? ''),
                        'title' => $mapped['title'],
                        'link' => (string) ($post['link'] ?? ''),
                        'image' => $mapped['image'],
                    ];
                }

                if (count($posts) < 20) {
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::channel('scraper')->warning('Blog REST list failed', ['error' => $e->getMessage()]);
        }

        return array_values(array_filter($out, fn ($p) => $p['slug'] !== '' && $p['link'] !== ''));
    }

    /**
     * @param  array<string, mixed>  $post
     * @return array{title: string, excerpt: ?string, body: ?string, category: ?string, published_at: ?Carbon, image: ?string}
     */
    private function mapRestPost(array $post): array
    {
        $title = html_entity_decode(strip_tags((string) data_get($post, 'title.rendered', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $excerptHtml = (string) data_get($post, 'excerpt.rendered', '');
        $bodyHtml = (string) data_get($post, 'content.rendered', '');
        $excerpt = trim(html_entity_decode(strip_tags($excerptHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $category = null;

        $terms = data_get($post, '_embedded.wp:term.0', []);
        if (is_array($terms)) {
            foreach ($terms as $term) {
                if (($term['taxonomy'] ?? '') === 'category' && ($term['slug'] ?? '') !== 'sin-categoria') {
                    $category = (string) ($term['name'] ?? null);
                    break;
                }
            }
            if ($category === null && isset($terms[0]['name'])) {
                $category = (string) $terms[0]['name'];
            }
        }

        $image = data_get($post, '_embedded.wp:featuredmedia.0.source_url');
        $published = data_get($post, 'date');

        return [
            'title' => $title,
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'body' => $this->sanitizeWpHtml($bodyHtml),
            'category' => $category,
            'published_at' => $published ? Carbon::parse($published) : null,
            'image' => is_string($image) ? $image : null,
        ];
    }

    /**
     * @return array{title: string, excerpt: ?string, body: ?string, category: ?string, published_at: ?Carbon, image: ?string}
     */
    private function scrapeDetailHtml(string $url): array
    {
        $html = $this->fetcher->get($url);

        $title = null;
        if (preg_match('/<h1[^>]*class="[^"]*elementor-heading-title[^"]*"[^>]*>(.*?)<\/h1>/is', $html, $m)
            || preg_match('/property="og:title" content="([^"]+)"/i', $html, $m)
            || preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        $publishedAt = null;
        if (preg_match('/property="article:published_time" content="([^"]+)"/i', $html, $m)) {
            $publishedAt = Carbon::parse($m[1]);
        }

        $image = null;
        if (preg_match('/property="og:image" content="([^"]+)"/i', $html, $m)) {
            $image = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $body = null;
        if (preg_match('/elementor-widget-theme-post-content[\s\S]*?<div class="elementor-widget-container">(.*?)<\/div>\s*<\/div>/is', $html, $m)) {
            $body = $this->sanitizeWpHtml($m[1]);
        } elseif (preg_match('/<div class="entry-content"[^>]*>(.*?)<\/div>/is', $html, $m)) {
            $body = $this->sanitizeWpHtml($m[1]);
        }

        $excerpt = null;
        if (is_string($body) && $body !== '') {
            $excerpt = Str::limit(trim(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 220);
        }

        return [
            'title' => $title ?? '',
            'excerpt' => $excerpt,
            'body' => $body,
            'category' => 'Sin categoría',
            'published_at' => $publishedAt,
            'image' => $image,
        ];
    }

    private function sanitizeWpHtml(string $html): ?string
    {
        $html = trim($html);
        if ($html === '') {
            return null;
        }

        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html) ?? $html;
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;

        // Prefer real image URLs from lazy-load attributes / noscript.
        $html = preg_replace_callback('/<img\b([^>]*)>/i', function (array $match): string {
            $attrs = $match[1];
            $src = null;
            foreach (['data-lazy-src', 'data-src', 'src'] as $attr) {
                if (preg_match('/'.$attr.'=["\']([^"\']+)["\']/i', $attrs, $m)) {
                    $candidate = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (! str_starts_with($candidate, 'data:image')) {
                        $src = $candidate;
                        break;
                    }
                }
            }
            if ($src === null) {
                return '';
            }

            $alt = '';
            if (preg_match('/alt=["\']([^"\']*)["\']/i', $attrs, $m)) {
                $alt = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            return '<img src="'.e($src).'" alt="'.e($alt).'" loading="lazy">';
        }, $html) ?? $html;

        // Drop MetaSlider chrome; keep resolved images.
        $html = preg_replace('/<div[^>]*class="[^"]*ml-slider[^"]*"[^>]*>.*?<\/div>\s*<\/div>\s*<\/div>/is', '', $html) ?? $html;
        $html = preg_replace('/<ul[^>]*class=["\']slides["\'][^>]*>.*?<\/ul>/is', '', $html) ?? $html;

        $html = preg_replace('/<(p|div)[^>]*>\s*(?:&nbsp;|\s|<br\s*\/?>)*\s*<\/\1>/i', '', $html) ?? $html;
        $html = preg_replace('/\s+/u', ' ', $html) ?? $html;
        $html = preg_replace('/>\s+</', '><', $html) ?? $html;

        // Restore readable block spacing for paragraphs/headings/images.
        $html = preg_replace('/<\/(p|h[1-6]|figure|ul|ol|blockquote)>/i', '</$1>'."\n", $html) ?? $html;
        $html = preg_replace('/<img /i', "\n<img ", $html) ?? $html;

        $html = trim($html);

        return $html !== '' ? $html : null;
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

    private function slugFromUrl(string $url, string $title): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $slug = $path !== '' ? Str::of($path)->afterLast('/')->__toString() : '';

        return $slug !== '' ? $slug : Str::slug($title);
    }
}
