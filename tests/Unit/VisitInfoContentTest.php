<?php

namespace Tests\Unit;

use App\Models\VisitInfo;
use Tests\TestCase;

class VisitInfoContentTest extends TestCase
{
    public function test_hola_copy_strips_rich_editor_html_and_splits_headline(): void
    {
        $visit = new VisitInfo([
            'about_content' => '<p>TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR. Refugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio.</p>',
        ]);

        $copy = $visit->holaCopy();

        $this->assertSame(VisitInfo::DEFAULT_HOLA_HEADLINE, $copy['headline']);
        $this->assertStringStartsWith('Refugio Gastronómico es el punto de encuentro', $copy['body']);
        $this->assertStringNotContainsString('<p>', $copy['headline']);
        $this->assertStringNotContainsString('<p>', $copy['body']);
    }

    public function test_hola_copy_keeps_two_plain_paragraphs(): void
    {
        $visit = new VisitInfo([
            'about_content' => VisitInfo::composeAboutContent('TITULAR UNO.', 'Párrafo descriptivo.'),
        ]);

        $this->assertSame([
            'headline' => 'TITULAR UNO.',
            'body' => 'Párrafo descriptivo.',
        ], $visit->holaCopy());
    }

    public function test_map_embed_url_extracts_iframe_src_and_defaults_when_empty(): void
    {
        $iframe = '<iframe src="https://www.google.com/maps?q=-12.0842658,-76.9734978&z=16" width="600"></iframe>';

        $this->assertSame(
            'https://www.google.com/maps?q=-12.0842658,-76.9734978&z=16&output=embed',
            VisitInfo::normalizeMapEmbedUrl($iframe)
        );
        $this->assertSame(VisitInfo::DEFAULT_MAP_EMBED_URL, VisitInfo::normalizeMapEmbedUrl(null));
        $this->assertSame(VisitInfo::DEFAULT_MAP_EMBED_URL, VisitInfo::normalizeMapEmbedUrl(''));
    }
}
