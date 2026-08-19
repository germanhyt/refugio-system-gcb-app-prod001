<?php

namespace Tests\Unit;

use App\Models\ServiceItem;
use Tests\TestCase;

class ServiceItemWhatsappTest extends TestCase
{
    public function test_phone_numbers_in_description_become_whatsapp_links(): void
    {
        $service = new ServiceItem([
            'title' => 'WhatsApp Refugio',
            'description' => '¡Conversemos: 991 318 720!',
        ]);

        $html = $service->descriptionWithWhatsappLinks();

        $this->assertStringContainsString('wa.me/51991318720', $html);
        $this->assertStringContainsString('991 318 720', $html);
        $this->assertStringContainsString('Hola%20Refugio', $html);
        $this->assertStringNotContainsString('wa.me/513', $html);
    }

    public function test_contact_phone_field_links_number_inside_description(): void
    {
        $service = new ServiceItem([
            'title' => 'WhatsApp Refugio',
            'description' => '¡Conversemos: 991 318 720!',
            'contact_phone' => '991 318 720',
            'whatsapp_message' => 'Hola, quiero reservar',
        ]);

        $html = $service->descriptionWithWhatsappLinks();

        $this->assertStringContainsString('wa.me/51991318720', $html);
        $this->assertStringContainsString('text=Hola%2C%20quiero%20reservar', $html);
        $this->assertStringContainsString('¡Conversemos:', $html);
    }

    public function test_contact_phone_is_appended_when_missing_from_description(): void
    {
        $service = new ServiceItem([
            'title' => 'Catering',
            'description' => 'Llevamos el sabor a tu evento',
            'contact_phone' => '994848723',
        ]);

        $html = $service->descriptionWithWhatsappLinks();

        $this->assertStringContainsString('Llevamos el sabor a tu evento', $html);
        $this->assertStringContainsString('wa.me/51994848723', $html);
        $this->assertStringContainsString('994848723', $html);
    }
}
