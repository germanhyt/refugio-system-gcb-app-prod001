<?php

namespace Tests\Unit;

use App\Services\SeoService;
use Tests\TestCase;

class SeoServiceTest extends TestCase
{
    private SeoService $seo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seo = new SeoService();
    }

    public function test_breadcrumb_json_ld_is_valid_and_ordered(): void
    {
        $json = $this->seo->breadcrumbJsonLd([
            ['name' => 'Inicio', 'url' => 'https://refugiogastronomico.pe/'],
            ['name' => 'Restaurantes', 'url' => 'https://refugiogastronomico.pe/restaurantes'],
            ['name' => 'Cavenecia', 'url' => 'https://refugiogastronomico.pe/restaurantes/cavenecia'],
        ]);

        $data = json_decode($json, true);

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('BreadcrumbList', $data['@type']);
        $this->assertCount(3, $data['itemListElement']);

        $this->assertSame(1, $data['itemListElement'][0]['position']);
        $this->assertSame('Inicio', $data['itemListElement'][0]['name']);
        $this->assertSame('https://refugiogastronomico.pe/', $data['itemListElement'][0]['item']);

        $this->assertSame(3, $data['itemListElement'][2]['position']);
        $this->assertSame('Cavenecia', $data['itemListElement'][2]['name']);
    }

    public function test_breadcrumb_without_url_omits_item(): void
    {
        $json = $this->seo->breadcrumbJsonLd([
            ['name' => 'Inicio', 'url' => 'https://refugiogastronomico.pe/'],
            ['name' => 'Página actual', 'url' => null],
        ]);

        $data = json_decode($json, true);

        $this->assertArrayHasKey('item', $data['itemListElement'][0]);
        $this->assertArrayNotHasKey('item', $data['itemListElement'][1]);
        $this->assertSame('Página actual', $data['itemListElement'][1]['name']);
    }

    public function test_faq_json_ld_builds_questions_from_faq_block(): void
    {
        $page = [
            'title' => 'Preguntas Frecuentes',
            'blocks' => [
                [
                    'type' => 'faq',
                    'items' => [
                        ['question' => '¿Cuál es el horario?', 'answer' => 'Abrimos todos los días.'],
                        ['question' => '¿Hay parqueo?', 'answer' => 'Sí, 3 horas gratis con consumo.'],
                    ],
                ],
            ],
        ];

        $json = $this->seo->faqJsonLd($page);

        $data = json_decode($json, true);

        $this->assertSame('FAQPage', $data['@type']);
        $this->assertCount(2, $data['mainEntity']);

        $this->assertSame('Question', $data['mainEntity'][0]['@type']);
        $this->assertSame('¿Cuál es el horario?', $data['mainEntity'][0]['name']);
        $this->assertSame('Answer', $data['mainEntity'][0]['acceptedAnswer']['@type']);
        $this->assertSame('Abrimos todos los días.', $data['mainEntity'][0]['acceptedAnswer']['text']);
    }

    public function test_faq_json_ld_ignores_non_faq_blocks(): void
    {
        $page = [
            'blocks' => [
                ['type' => 'highlight', 'title' => 'x', 'body' => 'y'],
                ['type' => 'list', 'items' => ['a', 'b']],
                ['type' => 'faq', 'items' => [['question' => 'Q1', 'answer' => 'A1']]],
            ],
        ];

        $data = json_decode($this->seo->faqJsonLd($page), true);

        $this->assertCount(1, $data['mainEntity']);
        $this->assertSame('Q1', $data['mainEntity'][0]['name']);
    }

    public function test_faq_json_ld_returns_empty_string_when_no_faq_items(): void
    {
        $page = ['blocks' => [['type' => 'highlight', 'title' => 'x', 'body' => 'y']]];

        $this->assertSame('', $this->seo->faqJsonLd($page));
    }

    public function test_faq_json_ld_skips_items_missing_question_or_answer(): void
    {
        $page = [
            'blocks' => [
                [
                    'type' => 'faq',
                    'items' => [
                        ['question' => 'Válida', 'answer' => 'Respuesta'],
                        ['question' => 'Sin respuesta'],
                        ['answer' => 'Sin pregunta'],
                        [],
                    ],
                ],
            ],
        ];

        $data = json_decode($this->seo->faqJsonLd($page), true);

        $this->assertCount(1, $data['mainEntity']);
        $this->assertSame('Válida', $data['mainEntity'][0]['name']);
    }

    public function test_json_ld_is_unicode_safe_and_unescaped_slashes(): void
    {
        $json = $this->seo->breadcrumbJsonLd([
            ['name' => 'Ñoño & café', 'url' => 'https://example.com/a/b'],
        ]);

        $this->assertStringContainsString('Ñoño', $json);
        $this->assertStringContainsString('&', $json);
        $this->assertStringNotContainsString('\\/', $json, 'Slashes should not be escaped');
        $this->assertStringContainsString('https://example.com/a/b', $json);
    }
}
