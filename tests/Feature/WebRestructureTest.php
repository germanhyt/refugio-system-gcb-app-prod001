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
        $response->assertSee('id="contacto"', false);
        $response->assertSee('DE TODO', false);
        $response->assertSee('PARA TODOS', false);
        $response->assertSee('Espacios publicitarios y comerciales', false);
        $response->assertSee('¡Conversemos:', false);
        $response->assertSee('991 318 720', false);
        $response->assertSee('wa.me/51991318720', false);
        $response->assertDontSee('instagram-feed', false);
        $response->assertDontSee('blog-foodies', false);
        $response->assertDontSee('images/refugio/fondohome.jpg', false);

        $settings = \App\Models\SiteSetting::current();
        if ($settings->googleTagManagerId() !== '') {
            $response->assertSee($settings->googleTagManagerId(), false);
            $response->assertSee('googletagmanager.com/gtm.js', false);
        }
        if ($settings->googleAnalyticsId() !== '') {
            $response->assertSee($settings->googleAnalyticsId(), false);
        }
        $response->assertSee('data-rg-track="click_reserva"', false);
        $response->assertSee('data-page="home"', false);
    }

    public function test_header_nav_matches_sdd(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Nosotros', false);
        $response->assertSee('Restaurantes', false);
        $response->assertSee('Eventos', false);
        $response->assertSee('¡Reserva aquí!', false);
        $response->assertSee('logo-v2-white.svg', false);
        $response->assertDontSee('>Visítanos<', false);
        $response->assertDontSee('href="'.url('/contacto').'"', false);
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
        $response->assertSee('https://www.instagram.com/p/DbecrIhgVxo/?img_index=1', false);
        $response->assertSee('https://wa.link/nxbse6', false);
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
            ->assertSee('Tópico', false)
            ->assertSee('Atención de primeros auxilios', false)
            ->assertSee('decorator-platana-inferior-izquierda.png', false)
            ->assertSee('decorator-pajaros-rojos.png', false)
            ->assertSee('rg-services-deco--plants-left', false);
        $this->get('/preguntas-frecuentes')->assertOk()->assertSee('¿Cuál es el horario de atención?', false);
        $this->get('/reglamento-pet-friendly')->assertOk()->assertSee('Zona pet friendly', false);
        $this->get('/politica-de-estacionamiento')->assertOk()->assertSee('3 horas gratis', false);
        $ulima = $this->get('/descuentos-u-lima');
        $pdf = \App\Models\SiteSetting::current()->ulimaDiscountsPdfUrl();
        if (filled($pdf)) {
            $ulima->assertRedirect($pdf);
        } else {
            $ulima->assertOk()->assertSee('10% de descuento', false);
        }
        $this->get('/')->assertSee('ULIMA-DESCUENTOS', false);
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
            $response->assertSee('Descuentos exclusivos', false);
            $response->assertSee('Vigente', false);
            $response->assertSee('Club El Comercio', false);
            $response->assertSee('15% en toda la carta', false);
            $response->assertDontSee('Convenio vencido', false);
            $response->assertDontSee('Convenio inactivo', false);
            $response->assertSee('https://www.instagram.com/refugiogastronomico.pe/', false);
            $response->assertSee('Reserva', false);
            $response->assertSee('https://wa.me/51991318720', false);
            $response->assertDontSee('Posición en el parque', false);
        } finally {
            $restaurant->delete();
        }
    }

    public function test_restaurant_detail_exclusive_discount_opens_with_ver_title(): void
    {
        $response = $this->get('/restaurantes/ahumare');

        $response->assertOk();
        $response->assertSee('Descuentos exclusivos', false);
        $response->assertSee('title="Ver"', false);
        $response->assertSee('Posición en el parque', false);
    }

    public function test_la_22_shows_park_map(): void
    {
        $restaurant = \App\Models\Restaurant::query()->where('slug', 'la-22-sangucheria')->first();
        $this->assertNotNull($restaurant);
        $this->assertNotEmpty($restaurant->parkPositionImageUrl());

        $this->get('/restaurantes/la-22-sangucheria')
            ->assertOk()
            ->assertSee('Posición en el parque', false)
            ->assertSee($restaurant->parkPositionImageUrl(), false);
    }

    public function test_complaint_book_uses_configured_banner(): void
    {
        $settings = \App\Models\SiteSetting::current();
        $banner = $settings->pageHeroBannerUrl('complaints');

        $response = $this->get('/libro-de-reclamaciones')->assertOk();
        $response->assertSee('Libro de reclamaciones', false);

        if ($banner) {
            $response->assertSee($banner, false);
        }
    }

    public function test_legal_and_mirrored_pages_return_ok_with_fallback(): void
    {
        $this->get('/terminos-y-condiciones')->assertOk()->assertSee('Términos y condiciones', false);
        $this->get('/politica-privacidad')->assertOk()->assertSee('Políticas de privacidad', false);
        $this->get('/politicas-de-privacidad')->assertOk();
        $this->get('/libro-de-reclamaciones')->assertOk()->assertSee('Libro de reclamaciones', false);
        // /contacto ahora redirige 301 a / (contacto fusionado en home, preserva SEO del WP origen)
        $this->get('/contacto')->assertRedirect('/');
        $this->get('/convocatoria')->assertOk();
    }

    public function test_legacy_wp_urls_redirect_to_new_laravel_urls(): void
    {
        // Slugs de restaurante que cambiaron
        $this->get('/restaurantes/curich')->assertRedirect('/restaurantes/cremoladas-curich');
        $this->get('/restaurantes/cavenecia-2')->assertRedirect('/restaurantes/cavenecia');
        $this->get('/restaurantes/bros')->assertRedirect('/restaurantes/broaster-bros');
        $this->get('/restaurantes/sisa')->assertRedirect('/restaurantes/sisa-coffee-wine');
        $this->get('/restaurantes/puerto-mancora')->assertRedirect('/restaurantes/barrio-mancora');
        $this->get('/restaurantes/saltao')->assertRedirect('/restaurantes/saltao-wok-food');
        $this->get('/restaurantes/anticuching')->assertRedirect('/restaurantes/anticuchos-anticuching');
        $this->get('/restaurantes/la-limanesa')->assertRedirect('/restaurantes/limanesas');

        // Restaurantes retirados → listado
        $this->get('/restaurantes/la-choza-de-la-anaconda')->assertRedirect('/restaurantes');
        $this->get('/restaurantes/caja-china-criolla')->assertRedirect('/restaurantes');

        // Páginas estáticas fusionadas
        $this->get('/gracias-contacto')->assertRedirect('/');
        $this->get('/plantilla')->assertRedirect('/');
        $this->get('/convocatorias')->assertRedirect('/convocatoria');
        $this->get('/convoctaria')->assertRedirect('/convocatoria');

        // Etiquetas del WP → listado de restaurantes
        $this->get('/etiquetas-restaurant/cafeteria')->assertRedirect('/restaurantes');

        // Eventos pasados → índice
        $this->get('/eventos/la-banda-del-chino')->assertRedirect('/eventos');
    }

    public function test_page_banners_follow_hero_titles(): void
    {
        $settings = \App\Models\SiteSetting::current();

        $this->get('/restaurantes')
            ->assertOk()
            ->assertSee('¿Qué te provoca hoy?', false);

        if ($restaurantsBanner = $settings->pageHeroBannerUrl('restaurants')) {
            $this->get('/restaurantes')->assertSee($restaurantsBanner, false);
        }

        $this->get('/servicios')
            ->assertOk()
            ->assertSee('Nuestros servicios', false);

        if ($servicesBanner = $settings->pageHeroBannerUrl('services')) {
            $this->get('/servicios')->assertSee($servicesBanner, false);
        }

        $this->get('/eventos')->assertOk();

        if ($eventsBanner = $settings->pageHeroBannerUrl('events')) {
            $this->get('/eventos')->assertSee($eventsBanner, false);
        }

        if ($aboutBanner = $settings->pageHeroBannerUrl('about')) {
            $this->get('/nosotros')
                ->assertSee($aboutBanner, false)
                ->assertDontSee('rg-visit-hero-photo', false);
        }
    }

    public function test_restaurant_listing_uses_logo_and_dish_assets(): void
    {
        $restaurant = \App\Models\Restaurant::query()->where('slug', 'ahumare')->first();
        $this->assertNotNull($restaurant);
        $this->assertNotEmpty($restaurant->logoUrl());
        $this->assertNotEmpty($restaurant->featuredImageUrl());

        $this->get('/restaurantes')
            ->assertOk()
            ->assertSee($restaurant->logoUrl(), false);

        $this->get('/restaurantes/ahumare')
            ->assertOk()
            ->assertSee($restaurant->bannerImageUrl() ?: $restaurant->featuredImageUrl(), false);
    }

    public function test_retired_restaurants_are_not_public(): void
    {
        $this->get('/restaurantes')
            ->assertOk()
            ->assertDontSee('La Choza de la Anaconda', false)
            ->assertDontSee('Caja China', false);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('La Choza de la Anaconda', false)
            ->assertDontSee('Caja China Criolla', false);

        $this->get('/restaurantes/la-choza-de-la-anaconda')->assertNotFound();
        $this->get('/restaurantes/caja-china-criolla')->assertNotFound();
    }

    public function test_about_hero_is_compact_and_centered(): void
    {
        $about = $this->get('/nosotros');
        $about->assertOk();
        $about->assertSee('container-refugio relative z-10 text-center', false);
        $about->assertSee('position: absolute !important', false);
        $about->assertSee('rg-visit-hero-deco', false);
        $about->assertSee('¿Quiénes', false);
        $about->assertSee('Somos?', false);
        $about->assertSee('¡Hola! Somos Refugio Gastronómico.', false);
        $about->assertSee('TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.', false);
        $about->assertDontSee('&lt;p&gt;TODO', false);
        $about->assertDontSee('<p>TODO LO QUE', false);
        $about->assertSee('maps.google.com/maps', false);
        $about->assertSee('data-visit-map', false);
        $about->assertDontSee('rg-visit-us-card" data-aos', false);
    }

    public function test_complaint_book_notifies_configured_recipients(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->from('/libro-de-reclamaciones')->post('/libro-de-reclamaciones', [
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'first_name' => 'Ana',
            'last_name' => 'Prueba',
            'department' => 'Lima',
            'address' => 'Av. Javier Prado Este 4492',
            'phone' => '991318720',
            'email' => 'ana-reclamacion-test@example.com',
            'product_description' => 'Consumo en un local del Refugio Gastronómico.',
            'claim_type' => 'Queja',
            'claim_detail' => 'La atención no coincidió con lo esperado en el local.',
            'consumer_request' => 'Solicito una respuesta formal sobre el incidente.',
        ]);

        $response->assertRedirect();

        try {
            \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ComplaintBookReceived::class, function ($mail) {
                return $mail->hasTo('leilah@gcb.pe')
                    && $mail->hasTo('mario@refugiogastronomico.pe')
                    && $mail->hasTo('nataly@gcb.pe');
            });

            $this->assertDatabaseHas('complaint_book_entries', [
                'email' => 'ana-reclamacion-test@example.com',
                'claim_type' => 'Queja',
            ]);
        } finally {
            \App\Models\ComplaintBookEntry::query()
                ->where('email', 'ana-reclamacion-test@example.com')
                ->delete();
        }
    }

    public function test_restaurant_index_orders_by_category_when_showing_all(): void
    {
        $response = $this->get('/restaurantes');
        $response->assertOk();

        $html = $response->getContent();
        $barPos = strpos($html, 'Refugio Bar');
        $cafePos = strpos($html, 'Tortas Gaby');
        $internationalPos = strpos($html, 'Ahumare');
        $peruvianPos = strpos($html, 'Anticuching');

        $this->assertNotFalse($barPos);
        $this->assertNotFalse($cafePos);
        $this->assertNotFalse($internationalPos);
        $this->assertNotFalse($peruvianPos);
        $this->assertTrue($barPos < $cafePos);
        $this->assertTrue($cafePos < $internationalPos);
        $this->assertTrue($internationalPos < $peruvianPos);
    }
}
