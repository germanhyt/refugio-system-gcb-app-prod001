<?php

namespace Tests\Unit;

use App\Http\Middleware\StripTrailingSlash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StripTrailingSlashTest extends TestCase
{
    public function test_redirects_trailing_slash_to_non_slash_with_301(): void
    {
        $request = Request::create('/nosotros/', 'GET');

        $response = $this->runMiddleware($request);

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertStringEndsWith('/nosotros', $response->headers->get('Location'));
    }

    public function test_preserves_query_string_when_redirecting(): void
    {
        $request = Request::create('/restaurantes/?foo=bar', 'GET');

        $response = $this->runMiddleware($request);

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertStringEndsWith('/restaurantes?foo=bar', $response->headers->get('Location'));
    }

    public function test_does_not_touch_root_path(): void
    {
        $request = Request::create('/', 'GET');

        $response = $this->runMiddleware($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('next', $response->getContent());
    }

    public function test_does_not_touch_path_without_trailing_slash(): void
    {
        $request = Request::create('/nosotros', 'GET');

        $response = $this->runMiddleware($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('next', $response->getContent());
    }

    public function test_ignores_non_get_methods(): void
    {
        $request = Request::create('/restaurantes/', 'POST');

        $response = $this->runMiddleware($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('next', $response->getContent());
    }

    private function runMiddleware(Request $request): Response
    {
        $middleware = new StripTrailingSlash();

        return $middleware->handle($request, function () {
            return response('next', 200);
        });
    }
}
