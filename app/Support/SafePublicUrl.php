<?php

namespace App\Support;

class SafePublicUrl
{
    public const RULE = ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/|#).+/i'];

    public static function href(?string $url, string $fallback): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return $fallback;
        }

        if (preg_match('~^(https?://|/|#)~i', $url) === 1) {
            return $url;
        }

        return $fallback;
    }
}
