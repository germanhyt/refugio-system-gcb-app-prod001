<?php

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class ImageDownloader
{
    public function __construct(
        private readonly HttpFetcher $fetcher,
    ) {}

    public function attachFromUrl(
        HasMedia $model,
        string $url,
        string $collection,
        string $directory,
        string $filenamePrefix = 'media',
        bool $force = false,
    ): ?string {
        $url = $this->normalizeUrl($url);
        if ($url === '') {
            return null;
        }

        $extension = $this->guessExtension($url);
        $relativePath = trim($directory, '/').'/'.$filenamePrefix.'.'.$extension;
        $disk = Storage::disk('public');

        $existing = $model->getFirstMedia($collection);
        if ($existing && ! $force) {
            $storedChecksum = $existing->getCustomProperty('source_checksum');
            if ($storedChecksum) {
                // Skip re-download if media already attached with checksum.
                return $existing->getPathRelativeToRoot();
            }
        }

        $binary = $this->fetcher->getBinary($url);
        $checksum = md5($binary);

        if ($existing && ! $force && $existing->getCustomProperty('source_checksum') === $checksum) {
            return $existing->getPathRelativeToRoot();
        }

        if ($disk->exists($relativePath) && ! $force) {
            $existingBinary = $disk->get($relativePath);
            if (md5($existingBinary) === $checksum) {
                if (! $existing) {
                    $model
                        ->addMedia($disk->path($relativePath))
                        ->preservingOriginal()
                        ->withCustomProperties([
                            'source_url' => $url,
                            'source_checksum' => $checksum,
                        ])
                        ->toMediaCollection($collection);
                }

                return $relativePath;
            }
        }

        $disk->put($relativePath, $binary);

        $model->clearMediaCollection($collection);
        $model
            ->addMedia($disk->path($relativePath))
            ->preservingOriginal()
            ->withCustomProperties([
                'source_url' => $url,
                'source_checksum' => $checksum,
            ])
            ->toMediaCollection($collection);

        return $relativePath;
    }

    public function normalizeUrl(string $url): string
    {
        $url = trim(html_entity_decode($url));
        if ($url === '' || Str::startsWith($url, 'data:')) {
            return '';
        }

        if (Str::startsWith($url, '//')) {
            return 'https:'.$url;
        }

        if (Str::startsWith($url, '/')) {
            return 'https://refugiogastronomico.pe'.$url;
        }

        return $url;
    }

    private function guessExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'], true)
            ? ($ext === 'jpeg' ? 'jpg' : $ext)
            : 'jpg';
    }
}
