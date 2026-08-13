<?php

namespace Database\Seeders;

use App\Models\ContactBlock;
use Illuminate\Database\Seeder;

class ContactBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'title' => 'Espacios publicitarios y comerciales',
                'body' => null,
                'emails' => ['mike@gcb.pe', 'leilah@gcb.pe'],
                'phones' => ['994 848 723'],
            ],
            [
                'title' => 'Servicio al cliente',
                'body' => 'Reservas',
                'emails' => [],
                'phones' => ['991 318 720'],
            ],
            [
                'title' => '¡Trabaja con nosotros!',
                'body' => null,
                'emails' => [],
                'phones' => ['991 318 720'],
            ],
        ];

        foreach ($blocks as $index => $block) {
            ContactBlock::query()->updateOrCreate(
                ['title' => $block['title']],
                [
                    'body' => $block['body'],
                    'emails' => $block['emails'],
                    'phones' => $block['phones'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        ContactBlock::query()
            ->whereIn('title', ['Espacios publicitarios', 'Espacios comerciales'])
            ->update(['is_active' => false]);
    }
}
