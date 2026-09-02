<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    private function requiresDatabase(): void
    {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Throwable) {
            $this->markTestSkipped('MySQL no disponible');
        }
    }

    public function test_home_sends_baseline_security_headers(): void
    {
        $this->requiresDatabase();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertSame('', (string) $response->headers->get('X-Powered-By'));
    }

    public function test_robots_disallows_admin(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertNotFalse($robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
    }

    public function test_public_form_routes_use_throttle(): void
    {
        foreach (['newsletter.store', 'info.store', 'legal.complaints.store'] as $name) {
            $middleware = Route::getRoutes()->getByName($name)?->gatherMiddleware() ?? [];

            $this->assertContains('throttle:public-forms', $middleware, $name);
        }
    }

    public function test_newsletter_rejects_filled_honeypot(): void
    {
        $this->requiresDatabase();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->from('/')->post('/newsletter', [
            'name' => 'Bot',
            'email' => 'honeypot-'.uniqid('', true).'@example.com',
            'website' => 'https://spam.test',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertSame(0, NewsletterSubscriber::query()->where('email', 'like', 'honeypot-%')->count());
    }

    public function test_newsletter_is_rate_limited(): void
    {
        $this->requiresDatabase();
        Cache::flush();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $prefix = 'rl-'.uniqid('', true);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/newsletter', [
                'name' => 'Rate',
                'email' => $prefix.'+'.$i.'@example.com',
                'website' => '',
            ]);
        }

        $blocked = $this->post('/newsletter', [
            'name' => 'Rate',
            'email' => $prefix.'+overflow@example.com',
            'website' => '',
        ]);

        $blocked->assertStatus(429);

        NewsletterSubscriber::query()->where('email', 'like', $prefix.'%')->delete();
    }

    public function test_editors_cannot_open_site_settings(): void
    {
        $editor = new User(['role' => User::ROLE_EDITOR]);
        $this->actingAs($editor);

        $this->assertFalse(ManageSiteSettings::canAccess());

        $admin = new User(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $this->assertTrue(ManageSiteSettings::canAccess());
    }
}
