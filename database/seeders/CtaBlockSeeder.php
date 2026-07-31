<?php

namespace Database\Seeders;

use App\Models\CtaBlock;
use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Illuminate\Database\Seeder;

class CtaBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'type' => 'evento',
                'title' => '¿Quieres liderar',
                'highlighted_word' => 'un evento?',
                'description' => 'Estamos felices de ayudarte.',
                'link_url' => '/contacto',
                'link_text' => 'Más info',
            ],
            [
                'type' => 'renta',
                'title' => '¿Quieres rentar',
                'highlighted_word' => 'un local?',
                'description' => 'Estamos felices de ayudarte.',
                'link_url' => '/contacto',
                'link_text' => 'Más info',
            ],
            [
                'type' => 'contacto',
                'title' => '¿Quieres',
                'highlighted_word' => 'contactarnos?',
                'description' => 'Estamos felices de ayudarte.',
                'link_url' => '/contacto',
                'link_text' => 'Más info',
            ],
        ];

        foreach ($blocks as $block) {
            CtaBlock::query()->updateOrCreate(
                ['type' => $block['type']],
                array_merge($block, ['is_active' => true])
            );
        }

        SiteSetting::current();
        VisitInfo::current();
    }
}
