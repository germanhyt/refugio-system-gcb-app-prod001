<?php

namespace App\Console\Commands;

use App\Services\Scraper\BlogScraper;
use App\Services\Scraper\EventScraper;
use App\Services\Scraper\HeroScraper;
use App\Services\Scraper\HttpFetcher;
use App\Services\Scraper\ImageDownloader;
use App\Services\Scraper\InstagramScraper;
use App\Services\Scraper\PageScraper;
use App\Services\Scraper\RestaurantScraper;
use App\Services\Scraper\SitemapParser;
use Illuminate\Console\Command;

class RefugioImportCommand extends Command
{
    protected $signature = 'refugio:import
                            {target? : restaurants|events|pages|hero|blog|instagram}
                            {--all : Import all content types}
                            {--discover : Only list discovered URLs}
                            {--force : Re-download images even if checksum matches}';

    protected $description = 'Import content from refugiogastronomico.pe into the CMS';

    public function handle(): int
    {
        $baseUrl = rtrim((string) config('refugio.source_url'), '/');
        $delay = (int) config('refugio.scraper_delay_ms', 500);
        $force = (bool) $this->option('force');

        $fetcher = new HttpFetcher($delay);
        $images = new ImageDownloader($fetcher);
        $sitemaps = new SitemapParser($fetcher, $baseUrl);

        if ($this->option('discover')) {
            return $this->discover($sitemaps);
        }

        $target = $this->argument('target');
        $runAll = $this->option('all') || $target === null;

        if (! $runAll && ! in_array($target, ['restaurants', 'events', 'pages', 'hero', 'blog', 'instagram'], true)) {
            $this->error('Target must be one of: restaurants, events, pages, hero, blog, instagram (or use --all / --discover).');

            return self::FAILURE;
        }

        $failures = 0;
        $summary = [];

        if ($runAll || $target === 'pages') {
            $this->info('Importing pages / visit info / settings...');
            $result = (new PageScraper($fetcher, $images, $baseUrl))->import($force);
            $summary['pages'] = $result;
            $failures += count($result['failures']);
            $this->line('  VisitInfo: '.($result['visit_info'] ? 'OK' : 'FAIL'));
            $this->line('  SiteSettings: '.($result['site_settings'] ? 'OK' : 'FAIL'));
        }

        if ($runAll || $target === 'hero') {
            $this->info('Importing hero slides...');
            $result = (new HeroScraper($fetcher, $images, $baseUrl))->import($force);
            $summary['hero'] = $result;
            $failures += $result['failed'];
            $this->line("  Imported: {$result['imported']} | Failed: {$result['failed']}");
        }

        if ($runAll || $target === 'restaurants') {
            $this->info('Importing restaurants...');
            $result = (new RestaurantScraper($fetcher, $sitemaps, $images, $baseUrl))->import($force);
            $summary['restaurants'] = $result;
            $failures += $result['failed'];
            $this->line("  Imported: {$result['imported']} | Failed: {$result['failed']}");
            if ($result['failures'] !== []) {
                foreach ($result['failures'] as $url) {
                    $this->warn("  - {$url}");
                }
            }
        }

        if ($runAll || $target === 'events') {
            $this->info('Importing events...');
            $result = (new EventScraper($fetcher, $sitemaps, $images, $baseUrl))->import($force);
            $summary['events'] = $result;
            $failures += $result['failed'];
            $this->line("  Imported: {$result['imported']} | Failed: {$result['failed']}");
            if ($result['failures'] !== []) {
                foreach ($result['failures'] as $url) {
                    $this->warn("  - {$url}");
                }
            }
        }

        if ($runAll || $target === 'blog') {
            $this->info('Importing blog foodies...');
            $result = (new BlogScraper($fetcher, $images, $baseUrl))->import($force);
            $summary['blog'] = $result;
            $failures += $result['failed'];
            $this->line("  Imported: {$result['imported']} | Failed: {$result['failed']}");
        }

        if ($runAll || $target === 'instagram') {
            $this->info('Importing Instagram posts...');
            $result = (new InstagramScraper($fetcher, $images, $baseUrl))->import($force);
            $summary['instagram'] = $result;
            $failures += $result['failed'];
            $this->line("  Imported: {$result['imported']} | Failed: {$result['failed']}");
        }

        $this->newLine();
        $this->info('Import summary');
        $this->table(
            ['Entity', 'Result'],
            collect($summary)->map(function ($result, $key) {
                if ($key === 'pages') {
                    return [$key, 'visit='.($result['visit_info'] ? 'ok' : 'fail').' settings='.($result['site_settings'] ? 'ok' : 'fail')];
                }

                return [$key, "imported={$result['imported']} failed={$result['failed']}"];
            })->values()->all()
        );

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function discover(SitemapParser $sitemaps): int
    {
        $this->info('Discovering URLs from sitemaps...');
        $data = $sitemaps->discover();

        $this->line('Restaurants ('.count($data['restaurants']).'):');
        foreach ($data['restaurants'] as $url) {
            $this->line("  {$url}");
        }

        $this->line('Events ('.count($data['events']).'):');
        foreach ($data['events'] as $url) {
            $this->line("  {$url}");
        }

        $this->line('Pages ('.count($data['pages']).'):');
        foreach ($data['pages'] as $url) {
            $this->line("  {$url}");
        }

        $this->newLine();
        $this->info(sprintf(
            'Total unique: %d restaurants, %d events, %d pages',
            count($data['restaurants']),
            count($data['events']),
            count($data['pages'])
        ));

        return self::SUCCESS;
    }
}
