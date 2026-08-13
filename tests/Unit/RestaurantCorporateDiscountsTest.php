<?php

namespace Tests\Unit;

use App\Models\Restaurant;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RestaurantCorporateDiscountsTest extends TestCase
{
    public function test_only_current_active_discounts_are_returned(): void
    {
        $today = Carbon::parse('2026-08-12', 'America/Lima')->startOfDay();

        $restaurant = new Restaurant([
            'corporate_discounts' => [
                [
                    'title' => 'Vigente sin fechas',
                    'is_active' => true,
                ],
                [
                    'title' => 'Vigente con rango',
                    'is_active' => true,
                    'starts_at' => '2026-08-01',
                    'ends_at' => '2026-12-31',
                ],
                [
                    'title' => 'Aún no empieza',
                    'is_active' => true,
                    'starts_at' => '2026-08-13',
                ],
                [
                    'title' => 'Ya venció',
                    'is_active' => true,
                    'ends_at' => '2026-08-11',
                ],
                [
                    'title' => 'Apagado',
                    'is_active' => false,
                    'starts_at' => '2026-01-01',
                    'ends_at' => '2026-12-31',
                ],
                [
                    'title' => '',
                    'is_active' => true,
                ],
            ],
        ]);

        $titles = $restaurant->activeCorporateDiscounts($today)->pluck('title')->all();

        $this->assertSame(['Vigente sin fechas', 'Vigente con rango'], $titles);
        $this->assertSame(
            ['Vigente sin fechas', 'Vigente con rango', 'Aún no empieza'],
            $restaurant->visibleCorporateDiscounts($today)->pluck('title')->all()
        );
        $this->assertSame(
            'upcoming',
            $restaurant->visibleCorporateDiscounts($today)->firstWhere('title', 'Aún no empieza')['status']
        );
    }

    public function test_social_links_omit_empty_urls(): void
    {
        $restaurant = new Restaurant([
            'website_url' => 'https://caveneciasteakhouse.com',
            'instagram_url' => 'https://instagram.com/marca',
            'facebook_url' => '  ',
            'tiktok_url' => null,
            'whatsapp_url' => 'https://wa.me/51900000000',
        ]);

        $keys = array_column($restaurant->socialLinks(), 'key');

        $this->assertSame(['website', 'instagram'], $keys);
        $this->assertTrue($restaurant->hasSocialLinks());
        $this->assertTrue($restaurant->hasReservationWhatsapp());
        $this->assertSame('https://wa.me/51900000000', $restaurant->reservationWhatsappUrl());
    }

    public function test_reservation_whatsapp_is_built_from_phone_when_url_is_empty(): void
    {
        $restaurant = new Restaurant([
            'reservation_phone' => '939010993',
            'whatsapp_url' => null,
        ]);

        $this->assertSame('https://wa.me/51939010993', $restaurant->reservationWhatsappUrl());
        $this->assertFalse($restaurant->hasSocialLinks());
    }

    public function test_facebook_link_is_ignored_when_it_is_not_facebook(): void
    {
        $restaurant = new Restaurant([
            'facebook_url' => 'https://wa.link/ltbwxk',
            'instagram_url' => 'https://www.instagram.com/ahu.mare/',
        ]);

        $this->assertSame(['instagram'], array_column($restaurant->socialLinks(), 'key'));
    }

    public function test_badge_discount_mode_does_not_require_details(): void
    {
        $restaurant = new Restaurant([
            'corporate_discount_mode' => Restaurant::DISCOUNT_BADGE,
            'corporate_discounts' => [],
        ]);

        $this->assertTrue($restaurant->showsCorporateDiscountBadge());
        $this->assertFalse($restaurant->showsCorporateDiscountDetails());
    }

    public function test_exclusive_discount_image_falls_back_to_public_asset(): void
    {
        $restaurant = new Restaurant([
            'slug' => 'ahumare',
            'corporate_discount_mode' => Restaurant::DISCOUNT_BADGE,
        ]);

        $this->assertTrue($restaurant->showsExclusiveDiscount());
        $this->assertStringContainsString('AHUMARE', (string) $restaurant->exclusiveDiscountImageUrl());
        $this->assertStringContainsString('MAPA WEB AHUMARE', (string) $restaurant->parkPositionImageUrl());
        $this->assertStringContainsString('ahumare.png', (string) $restaurant->logoUrl());
        $this->assertStringContainsString('plato-1-ahumare.jpg', (string) $restaurant->featuredImageUrl());
    }

    public function test_detail_copy_hides_duplicate_short_and_paragraphs(): void
    {
        $copy = 'Nos encanta la comida hecha con cariño.';

        $restaurant = new Restaurant([
            'short_description' => $copy,
            'description' => '<p>'.$copy.'</p><p>'.$copy.'</p>',
        ]);

        $this->assertNull($restaurant->detailLeadText());
        $this->assertSame('<p>'.$copy.'</p>', $restaurant->detailBodyHtml());
    }
}
