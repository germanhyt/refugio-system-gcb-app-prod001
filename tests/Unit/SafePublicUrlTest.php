<?php

namespace Tests\Unit;

use App\Support\SafePublicUrl;
use Tests\TestCase;

class SafePublicUrlTest extends TestCase
{
    public function test_allows_http_https_relative_and_hash(): void
    {
        $this->assertSame('https://wa.link/abc', SafePublicUrl::href('https://wa.link/abc', '/fallback'));
        $this->assertSame('http://example.com', SafePublicUrl::href('http://example.com', '/fallback'));
        $this->assertSame('/#contacto', SafePublicUrl::href('/#contacto', '/fallback'));
        $this->assertSame('#contacto', SafePublicUrl::href('#contacto', '/fallback'));
    }

    public function test_rejects_javascript_and_blank_urls(): void
    {
        $this->assertSame('/fallback', SafePublicUrl::href('javascript:alert(1)', '/fallback'));
        $this->assertSame('/fallback', SafePublicUrl::href('data:text/html,hi', '/fallback'));
        $this->assertSame('/fallback', SafePublicUrl::href('   ', '/fallback'));
        $this->assertSame('/fallback', SafePublicUrl::href(null, '/fallback'));
    }
}
