<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'refugio:sitemap';

    protected $description = 'Generate the public sitemap.xml for crawlers and AI engines';

    public function handle(): int
    {
        $urls = $this->collectUrls();

        $xml = $this->buildXml($urls);

        File::put(public_path('sitemap.xml'), $xml);

        $this->info(sprintf('Sitemap generated with %d URLs at public/sitemap.xml', count($urls)));

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{loc: string, lastmod: ?string}>
     */
    protected function collectUrls(): array
    {
        $urls = [];
        $now = Carbon::now()->toDateString();

        $staticRoutes = [
            ['url' => url('/'), 'lastmod' => $now],
            ['url' => route('restaurants.index'), 'lastmod' => $now],
            ['url' => route('events.index'), 'lastmod' => $now],
            ['url' => route('services.index'), 'lastmod' => $now],
            ['url' => route('about'), 'lastmod' => $now],
            ['url' => route('static.faq'), 'lastmod' => $now],
            ['url' => route('static.pet-friendly'), 'lastmod' => $now],
            ['url' => route('static.parking'), 'lastmod' => $now],
            ['url' => route('blog.index'), 'lastmod' => $now],
        ];

        foreach ($staticRoutes as $route) {
            $urls[] = ['loc' => $route['url'], 'lastmod' => $route['lastmod']];
        }

        foreach ($this->activeRestaurants() as $restaurant) {
            $urls[] = [
                'loc' => route('restaurants.show', $restaurant),
                'lastmod' => $restaurant->updated_at?->toDateString(),
            ];
        }

        foreach ($this->activeEvents() as $event) {
            $urls[] = [
                'loc' => route('events.show', $event),
                'lastmod' => $event->updated_at?->toDateString(),
            ];
        }

        foreach ($this->activeBlogPosts() as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post),
                'lastmod' => $post->updated_at?->toDateString(),
            ];
        }

        return $urls;
    }

    /**
     * @return list<Restaurant>
     */
    protected function activeRestaurants(): array
    {
        return Restaurant::query()->where('is_active', true)->orderBy('id')->get()->all();
    }

    /**
     * @return list<Event>
     */
    protected function activeEvents(): array
    {
        return Event::query()->where('is_active', true)->orderBy('id')->get()->all();
    }

    /**
     * @return list<BlogPost>
     */
    protected function activeBlogPosts(): array
    {
        if (! class_exists(BlogPost::class)) {
            return [];
        }

        return BlogPost::query()->where('is_active', true)->orderBy('id')->get()->all();
    }

    /**
     * @param  array<int, array{loc: string, lastmod: ?string}>  $urls
     */
    protected function buildXml(array $urls): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.$this->escape($url['loc']).'</loc>';
            if (! empty($url['lastmod'])) {
                $lines[] = '    <lastmod>'.$url['lastmod'].'</lastmod>';
            }
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
