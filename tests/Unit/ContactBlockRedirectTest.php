<?php

namespace Tests\Unit;

use App\Models\ContactBlock;
use Tests\TestCase;

class ContactBlockRedirectTest extends TestCase
{
    public function test_phones_and_emails_become_links_when_redirect_is_on(): void
    {
        $block = new ContactBlock([
            'title' => 'Reservas',
            'emails' => ['hola@refugio.pe'],
            'phones' => ['991 318 720'],
            'redirects_enabled' => true,
        ]);

        $channels = $block->contactChannels();

        $this->assertSame('mailto:hola@refugio.pe', $channels[0]['href']);
        $this->assertSame('https://wa.me/51991318720', $channels[1]['href']);
        $this->assertTrue($channels[1]['external']);
    }

    public function test_phones_and_emails_stay_plain_text_when_redirect_is_off(): void
    {
        $block = new ContactBlock([
            'title' => 'Reservas',
            'emails' => ['hola@refugio.pe'],
            'phones' => ['991 318 720'],
            'redirects_enabled' => false,
        ]);

        $channels = $block->contactChannels();

        $this->assertSame('hola@refugio.pe', $channels[0]['label']);
        $this->assertNull($channels[0]['href']);
        $this->assertSame('991 318 720', $channels[1]['label']);
        $this->assertNull($channels[1]['href']);
    }
}
