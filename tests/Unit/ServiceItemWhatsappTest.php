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
}
