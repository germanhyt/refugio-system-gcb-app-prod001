<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use Tests\TestCase;

class SiteSettingAnalyticsTest extends TestCase
{
    public function test_stored_measurement_ids_win_over_env_fallback(): void
    {
        config([
            'analytics.gtm_id' => 'GTM-FROMENV',
            'analytics.ga4_id' => 'G-FROMENV00',
        ]);

        $settings = new SiteSetting([
            'google_tag_manager_id' => 'gtm-m8ctgv79',
            'google_analytics_id' => 'g-4fcned6qvr',
        ]);

        $this->assertSame('GTM-M8CTGV79', $settings->googleTagManagerId());
        $this->assertSame('G-4FCNED6QVR', $settings->googleAnalyticsId());
    }

    public function test_blank_stored_ids_fall_back_to_config(): void
    {
        config([
            'analytics.gtm_id' => 'GTM-FALLBACK1',
            'analytics.ga4_id' => 'G-FALLBACK01',
        ]);

        $settings = new SiteSetting([
            'google_tag_manager_id' => '  ',
            'google_analytics_id' => null,
        ]);

        $this->assertSame('GTM-FALLBACK1', $settings->googleTagManagerId());
        $this->assertSame('G-FALLBACK01', $settings->googleAnalyticsId());
    }

    public function test_invalid_measurement_ids_are_ignored(): void
    {
        config([
            'analytics.gtm_id' => '',
            'analytics.ga4_id' => 'not-a-ga4-id',
        ]);

        $settings = new SiteSetting([
            'google_tag_manager_id' => '<script>alert(1)</script>',
            'google_analytics_id' => 'UA-123456-1',
        ]);

        $this->assertSame('', $settings->googleTagManagerId());
        $this->assertSame('', $settings->googleAnalyticsId());
    }
}
