<?php

namespace App\Services\Scraper;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpFetcher
{
    public function __construct(
        private readonly int $delayMs = 500,
    ) {}

    public function client(): PendingRequest
    {
        return Http::withHeaders([
            'User-Agent' => 'RefugioMigrator/1.0 (+https://refugiogastronomico.pe migration)',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ])->timeout(60)->retry(2, 1000);
    }

    public function get(string $url): string
    {
        usleep($this->delayMs * 1000);

        $response = $this->client()->get($url);

        if (! $response->successful()) {
            Log::channel('scraper')->warning('HTTP fetch failed', [
                'url' => $url,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException("Failed to fetch {$url} (HTTP {$response->status()})");
        }

        return $response->body();
    }

    public function getBinary(string $url): string
    {
        usleep($this->delayMs * 1000);

        $headers = [
            'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        ];

        if (str_contains($url, 'cdninstagram.com') || str_contains($url, 'instagram.com')) {
            $headers['Referer'] = 'https://www.instagram.com/';
        }

        $response = $this->client()->withHeaders($headers)->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("Failed to download {$url} (HTTP {$response->status()})");
        }

        return $response->body();
    }
}
