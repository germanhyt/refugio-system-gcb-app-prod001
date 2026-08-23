<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SeoTest extends TestCase
{
    public function test_home_emits_park_json_ld(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('"@type": "FoodEstablishment"', $html);
        $this->assertStringContainsString('"@id"', $html);
        $this->assertStringContainsString('#refugio', $html);
        $this->assertStringContainsString('Refugio Gastronómico', $html);
        $this->assertStringContainsString('"@type": "PostalAddress"', $html);
        $this->assertStringContainsString('Av. Javier Prado Este 4492', $html);
        $this->assertStringContainsString('Santiago de Surco', $html);
        $this->assertStringContainsString('"@type": "GeoCoordinates"', $html);
        $this->assertStringContainsString('-12.0842658', $html);
        $this->assertStringContainsString('"@type": "LocationFeatureSpecification"', $html);
        $this->assertStringContainsString('Pet-friendly', $html);
        $this->assertStringContainsString('"sameAs"', $html);
        $this->assertStringContainsString('instagram.com', $html);
    }

    public function test_home_json_ld_is_valid_json(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString(
            'application/ld+json',
            $html,
            'Expected at least one JSON-LD script block on home'
        );

        $blocks = [];
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $blocks);

        $this->assertNotEmpty($blocks[1], 'No JSON-LD script blocks found on home');

        foreach ($blocks[1] as $json) {
            $decoded = json_decode(trim($json), true);
            $this->assertNotNull(
                $decoded,
                'A JSON-LD block on home is not valid JSON: '.substr($json, 0, 120)
            );
        }
    }

    public function test_restaurant_detail_emits_food_establishment_and_breadcrumb(): void
    {
        $restaurant = Restaurant::query()->active()->first();
        $this->assertNotNull($restaurant, 'Seeded active restaurant is required for this test');

        $html = $this->get('/restaurantes/'.$restaurant->slug)->getContent();

        $this->assertStringContainsString('"@type": "FoodEstablishment"', $html);
        $this->assertStringContainsString('"name": "'.$restaurant->name.'"', $html);
        $this->assertStringContainsString('"parentOrganization"', $html);
        $this->assertStringContainsString('#refugio', $html);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $html);
        $this->assertStringContainsString('"@type": "ListItem"', $html);
        $this->assertStringContainsString('Restaurantes', $html);
    }

    public function test_faq_page_emits_faqpage_schema(): void
    {
        $html = $this->get('/preguntas-frecuentes')->getContent();

        $this->assertStringContainsString('"@type": "FAQPage"', $html);
        $this->assertStringContainsString('"@type": "Question"', $html);
        $this->assertStringContainsString('"@type": "Answer"', $html);
        $this->assertStringContainsString('¿Cuál es el horario de atención?', $html);
    }

    public function test_every_public_page_has_canonical_and_twitter_cards(): void
    {
        $pages = ['/', '/restaurantes', '/eventos', '/servicios', '/nosotros', '/preguntas-frecuentes'];

        foreach ($pages as $path) {
            $html = $this->get($path)->getContent();

            $this->assertStringContainsString(
                '<link rel="canonical"',
                $html,
                "Missing canonical on {$path}"
            );
            $this->assertStringContainsString(
                'twitter:card',
                $html,
                "Missing twitter:card on {$path}"
            );
            $this->assertStringContainsString(
                'twitter:title',
                $html,
                "Missing twitter:title on {$path}"
            );
            $this->assertStringContainsString(
                'twitter:description',
                $html,
                "Missing twitter:description on {$path}"
            );
        }
    }

    public function test_sitemap_command_generates_xml_and_route_serves_it(): void
    {
        $path = public_path('sitemap.xml');
        if (is_file($path)) {
            File::delete($path);
        }

        try {
            $exit = Artisan::call('refugio:sitemap');

            $this->assertSame(0, $exit, 'refugio:sitemap command failed');
            $this->assertFileExists($path, 'Sitemap file was not generated');

            $contents = file_get_contents($path);
            $this->assertStringContainsString('<?xml', $contents);
            $this->assertStringContainsString('<urlset', $contents);
            $this->assertStringContainsString('<url>', $contents);
            $this->assertStringContainsString('/restaurantes', $contents);

            $response = $this->get('/sitemap.xml');

            $response->assertOk();
            $response->assertHeader('Content-Type', 'application/xml');
            $this->assertStringContainsString('<urlset', $response->getContent());
        } finally {
            if (is_file($path)) {
                File::delete($path);
            }
        }
    }

    public function test_llms_txt_is_served(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString('Refugio Gastronómico', $body);
        $this->assertStringContainsString('parque gastronómico', $body);
        $this->assertStringContainsString('Av. Javier Prado Este 4492', $body);
    }

    public function test_robots_txt_references_sitemap_and_allows_ai_crawlers(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Sitemap:', $body);
        $this->assertStringContainsString('sitemap.xml', $body);
        // Asegura que no se bloquean crawlers de IA
        $this->assertStringNotContainsString('Disallow: /', $body);
    }
}
