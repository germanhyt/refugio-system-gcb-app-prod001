<?php

namespace Database\Seeders;

use App\Models\EventOffer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventOfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'title' => 'Shows Musicales',
                'summary' => 'Música en vivo y noches con la mejor vibra del Refugio.',
                'cta_text' => 'Ver cronograma',
                'cta_url' => 'https://www.instagram.com/p/DbecrIhgVxo/?img_index=1',
            ],
            [
                'title' => 'Show para niños',
                'summary' => 'Entretenimiento pensado para toda la familia.',
                'cta_text' => 'Ver cronograma',
                'cta_url' => 'https://www.instagram.com/p/DbecrIhgVxo/?img_index=1',
            ],
            [
                'title' => 'Organiza tu evento con nosotros',
                'summary' => 'Espacios y producción para tu celebración o activación.',
                'cta_text' => 'Cotizar',
                'cta_url' => 'https://wa.link/nxbse6',
            ],
            [
                'title' => 'Organiza tu fiesta infantil',
                'summary' => 'Fiestas infantiles con zona, shows y gastronomía en un solo lugar.',
                'cta_text' => 'Cotizar',
                'cta_url' => 'https://bosquemagico.gcbprojects.site',
            ],
        ];

        foreach ($offers as $index => $offer) {
            EventOffer::query()->updateOrCreate(
                ['slug' => Str::slug($offer['title'])],
                [
                    'title' => $offer['title'],
                    'summary' => $offer['summary'],
                    'cta_text' => $offer['cta_text'],
                    'cta_url' => $offer['cta_url'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
