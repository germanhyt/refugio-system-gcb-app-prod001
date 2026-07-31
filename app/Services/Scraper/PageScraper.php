<?php

namespace App\Services\Scraper;

use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class PageScraper
{
    public function __construct(
        private readonly HttpFetcher $fetcher,
        private readonly ImageDownloader $images,
        private readonly string $baseUrl = 'https://refugiogastronomico.pe',
    ) {}

    /**
     * @return array{visit_info: bool, site_settings: bool, failures: list<string>}
     */
    public function import(bool $force = false): array
    {
        $failures = [];
        $visitOk = false;
        $settingsOk = false;

        try {
            $html = $this->fetcher->get("{$this->baseUrl}/contacto/");
            $crawler = new Crawler($html);

            $address = $this->findTextContaining($crawler, 'Javier Prado')
                ?? 'Av. Javier Prado Este 4492 – Santiago de Surco';

            $email = null;
            if (preg_match('/[a-z0-9._%+-]+@refugiogastronomico\.pe/i', $html, $m)) {
                $email = strtolower($m[0]);
            }

            $phoneEvents = null;
            $phoneReservations = null;
            if (preg_match('/994\s*848\s*723/', $html, $m)) {
                $phoneEvents = preg_replace('/\s+/', '', $m[0]);
            }
            if (preg_match('/991\s*318\s*720/', $html, $m)) {
                $phoneReservations = preg_replace('/\s+/', '', $m[0]);
            }

            $schedule = $this->parseSchedule($html);

            VisitInfo::query()->updateOrCreate(
                ['id' => 1],
                [
                    'address' => html_entity_decode(trim(preg_replace('/\s+/', ' ', $address))),
                    'schedule' => $schedule,
                    'phone_reservations' => $phoneReservations ?: '991318720',
                    'phone_events' => $phoneEvents ?: '994848723',
                    'email' => $email ?: 'hola@refugiogastronomico.pe',
                    'pedestrian_access' => 'Ingreso peatonal por Av. Manuel Olguín',
                    'vehicle_access' => 'Ingreso vehicular por Av. Javier Prado Este 4492',
                    'amenities' => [
                        'Pet Friendly',
                        '3 horas de estacionamiento gratis con S/50 de consumo',
                    ],
                ]
            );
            $visitOk = true;
        } catch (\Throwable $e) {
            $failures[] = 'contacto';
            Log::channel('scraper')->error('VisitInfo import failed', ['error' => $e->getMessage()]);
        }

        try {
            $home = $this->fetcher->get("{$this->baseUrl}/");
            $crawler = new Crawler($home);

            $settings = SiteSetting::current();
            $settings->fill([
                'site_name' => 'Refugio Gastronómico',
                'slogan' => 'Juntos todo sabe mejor',
                'whatsapp_url' => $this->firstHref($crawler, 'a[href*="wa.link"], a[href*="api.whatsapp.com"]') ?: $settings->whatsapp_url,
                'instagram_url' => $this->firstHref($crawler, 'a[href*="instagram.com"]') ?: $settings->instagram_url,
                'facebook_url' => $this->firstHref($crawler, 'a[href*="facebook.com"]') ?: $settings->facebook_url,
                'tiktok_url' => $this->firstHref($crawler, 'a[href*="tiktok.com"]') ?: $settings->tiktok_url,
                'seo_title' => $this->meta($crawler, 'og:title') ?: $settings->seo_title,
                'seo_description' => $this->meta($crawler, 'og:description') ?: $settings->seo_description,
            ])->save();

            $logo = $crawler->filter('img.custom-logo, img.no-lazy.custom-logo')->count()
                ? $crawler->filter('img.custom-logo, img.no-lazy.custom-logo')->first()->attr('src')
                : 'https://refugiogastronomico.pe/wp-content/uploads/2022/11/logo-v2.svg';

            $this->images->attachFromUrl(
                $settings,
                $logo,
                'logo',
                'settings',
                'logo',
                $force
            );

            $ogImage = $this->meta($crawler, 'og:image');
            if ($ogImage) {
                $this->images->attachFromUrl(
                    $settings,
                    $ogImage,
                    'og_image',
                    'settings',
                    'og',
                    $force
                );
            }

            $settingsOk = true;
        } catch (\Throwable $e) {
            $failures[] = 'site_settings';
            Log::channel('scraper')->error('SiteSetting import failed', ['error' => $e->getMessage()]);
        }

        try {
            $about = $this->fetcher->get("{$this->baseUrl}/nosotros/");
            $crawler = new Crawler($about);
            $aboutText = $this->text($crawler, 'h1');
            VisitInfo::query()->whereKey(1)->update([
                'about_content' => $aboutText ?: 'Conoce más sobre nosotros',
            ]);
        } catch (\Throwable $e) {
            $failures[] = 'nosotros';
            Log::channel('scraper')->warning('About page import failed', ['error' => $e->getMessage()]);
        }

        return [
            'visit_info' => $visitOk,
            'site_settings' => $settingsOk,
            'failures' => $failures,
        ];
    }

    /**
     * @return list<array{days: string, hours: string}>
     */
    private function parseSchedule(string $html): array
    {
        $schedule = [];

        if (preg_match('/Domingo a Mi[eé]rcoles[^0-9]*([0-9].*?pm)/iu', $html, $m)) {
            $schedule[] = ['days' => 'Dom–Mié', 'hours' => trim(html_entity_decode($m[1]))];
        }
        if (preg_match('/Jueves[^0-9]*([0-9].*?am)/iu', $html, $m)) {
            $schedule[] = ['days' => 'Jue', 'hours' => trim(html_entity_decode($m[1]))];
        }
        if (preg_match('/Viernes y S[aá]bado[^0-9]*([0-9].*?am)/iu', $html, $m)) {
            $schedule[] = ['days' => 'Vie–Sáb', 'hours' => trim(html_entity_decode($m[1]))];
        }

        if ($schedule === []) {
            return [
                ['days' => 'Dom–Mié', 'hours' => '7am–10pm'],
                ['days' => 'Jue', 'hours' => '7am–12am'],
                ['days' => 'Vie–Sáb', 'hours' => '7am–1am'],
            ];
        }

        return $schedule;
    }

    private function findTextContaining(Crawler $crawler, string $needle): ?string
    {
        $found = null;
        $crawler->filter('body *')->each(function (Crawler $node) use (&$found, $needle) {
            if ($found) {
                return;
            }
            $text = trim($node->text(''));
            if ($text !== '' && str_contains($text, $needle) && strlen($text) < 120) {
                $found = $text;
            }
        });

        return $found;
    }

    private function firstHref(Crawler $crawler, string $selector): ?string
    {
        $nodes = $crawler->filter($selector);
        if ($nodes->count() === 0) {
            return null;
        }

        return $nodes->first()->attr('href');
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
