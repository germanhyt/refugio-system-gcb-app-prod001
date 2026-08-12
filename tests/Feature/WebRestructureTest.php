<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventOffer;
use Tests\TestCase;

class WebRestructureTest extends TestCase
{
    public function test_home_shows_sdd_sections_without_blog_or_instagram(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('¡Hola! Somos Refugio Gastronómico.', false);
        $response->assertSee('Nuestros restaurantes', false);
        $response->assertSee('Conoce nuestros servicios', false);
        $response->assertSee('decorator-platana-inferior-izquierda.png', false);
        $response->assertSee('decorator-pajaros-rojos.png', false);
        $response->assertSee('rg-services-deco--plants-left', false);
        $response->assertSee('¿Nos visitas?', false);
        $response->assertSee('¿Dudas? ¡Contáctanos!', false);
        $response->assertDontSee('instagram-feed', false);
        $response->assertDontSee('blog-foodies', false);
    }

    public function test_header_nav_matches_sdd(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Nosotros', false);
        $response->assertSee('Restaurantes', false);
        $response->assertSee('Eventos', false);
        $response->assertSee('¡Reserva aquí!', false);
        $response->assertSee('logo-v1-base.svg', false);
        $response->assertSee('logo-v2-white.svg', false);
        $response->assertDontSee('>Visítanos<', false);
    }

    public function test_events_index_shows_offer_cards(): void
    {
        $this->assertGreaterThanOrEqual(4, EventOffer::query()->count());

        $response = $this->get('/eventos');

        $response->assertOk();
        $response->assertSee('¡Somos el refugio de tu diversión!', false);
        $response->assertSee('Shows Musicales', false);
        $response->assertSee('Show para niños', false);
        $response->assertSee('Organiza tu evento con nosotros', false);
        $response->assertSee('Organiza tu fiesta infantil', false);
        $response->assertSee('¿Nos visitas?', false);
    }

    public function test_legacy_event_slug_still_works(): void
    {
        $event = Event::query()->firstOrCreate(
            ['slug' => 'legacy-show-test'],
            [
                'title' => 'Legacy Show',
                'event_date' => now()->addWeek()->toDateString(),
                'is_active' => true,
            ]
        );

        $this->get('/eventos/'.$event->slug)->assertOk();
    }

    public function test_services_and_footer_stubs_return_ok(): void
    {
        $this->get('/servicios')
            ->assertOk()
            ->assertSee('Nuestros servicios', false)
            ->assertSee('decorator-platana-inferior-izquierda.png', false)
            ->assertSee('decorator-pajaros-rojos.png', false)
            ->assertSee('rg-services-deco--plants-left', false);
        $this->get('/preguntas-frecuentes')->assertOk()->assertSee('¿Cuál es el horario de atención?', false);
        $this->get('/reglamento-pet-friendly')->assertOk()->assertSee('Zona pet friendly', false);
        $this->get('/politica-de-estacionamiento')->assertOk()->assertSee('3 horas gratis', false);
        $this->get('/descuentos-u-lima')->assertOk()->assertSee('10% de descuento', false);
        $this->get('/blog')->assertOk();
    }

    public function test_restaurant_detail_supports_delivery_and_similares(): void
    {
        $restaurant = \App\Models\Restaurant::query()->active()->first();
        $this->assertNotNull($restaurant);

        $response = $this->get('/restaurantes/'.$restaurant->slug);

        $response->assertOk();
        $response->assertSee($restaurant->name, false);
    }

    public function test_restaurant_detail_shows_active_discounts_and_social_links(): void
    {
        $restaurant = \App\Models\Restaurant::query()->create([
            'name' => 'Local Test Ficha',
            'slug' => 'local-test-ficha-'.uniqid(),
            'is_active' => true,
            'corporate_discount_mode' => \App\Models\Restaurant::DISCOUNT_DETAILS,
            'instagram_url' => 'https://www.instagram.com/refugiogastronomico.pe/',
            'facebook_url' => null,
            'tiktok_url' => null,
            'whatsapp_url' => 'https://wa.me/51991318720',
            'corporate_discounts' => [
                [
                    'title' => 'Club El Comercio',
                    'description' => '15% en toda la carta',
                    'is_active' => true,
                    'starts_at' => now('America/Lima')->subDay()->toDateString(),
                    'ends_at' => now('America/Lima')->addMonth()->toDateString(),
                ],
                [
                    'title' => 'Convenio vencido',
                    'description' => 'No debe verse',
                    'is_active' => true,
                    'starts_at' => now('America/Lima')->subYear()->toDateString(),
                    'ends_at' => now('America/Lima')->subDay()->toDateString(),
                ],
                [
                    'title' => 'Convenio inactivo',
                    'is_active' => false,
                ],
            ],
        ]);

        try {
            $response = $this->get('/restaurantes/'.$restaurant->slug);

            $response->assertOk();
            $response->assertSee('Descuentos corporativos', false);
            $response->assertSee('Vigente', false);
            $response->assertSee('Club El Comercio', false);
            $response->assertSee('15% en toda la carta', false);
            $response->assertDontSee('Convenio vencido', false);
            $response->assertDontSee('Convenio inactivo', false);
            $response->assertSee('https://www.instagram.com/refugiogastronomico.pe/', false);
            $response->assertSee('https://wa.me/51991318720', false);
            $response->assertDontSee('Posición en el parque', false);
        } finally {
            $restaurant->delete();
        }
    }

    public function test_legal_and_mirrored_pages_return_ok_with_fallback(): void
    {
        $this->get('/terminos-y-condiciones')->assertOk()->assertSee('Términos y condiciones', false);
        $this->get('/politica-privacidad')->assertOk()->assertSee('Políticas de privacidad', false);
        $this->get('/politicas-de-privacidad')->assertOk();
        $this->get('/libro-de-reclamaciones')->assertOk()->assertSee('Libro de reclamaciones', false);
        $this->get('/contacto')->assertOk()->assertSee('¿Dudas? ¡Contáctanos!', false)->assertSee('Envíanos un mensaje', false);
        $this->get('/convocatoria')->assertOk();
    }
}
