<?php

namespace App\Support;

use Spatie\MediaLibrary\HasMedia;

class PublicMedia
{
    public static function sync(HasMedia $model, string $collection, ?string $path): bool
    {
        if (! is_string($path) || $path === '' || ! is_file($path)) {
            return false;
        }

        $model->clearMediaCollection($collection);
        $model->addMedia($path)
            ->preservingOriginal()
            ->toMediaCollection($collection);

        return true;
    }

    public static function syncPublic(HasMedia $model, string $collection, mixed $relativePath): bool
    {
        if (! is_string($relativePath) || $relativePath === '') {
            return false;
        }

        return self::sync($model, $collection, public_path($relativePath));
    }
}
