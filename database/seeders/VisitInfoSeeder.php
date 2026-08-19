<?php

namespace Database\Seeders;

use App\Models\VisitInfo;
use Illuminate\Database\Seeder;

class VisitInfoSeeder extends Seeder
{
    public function run(): void
    {
        VisitInfo::query()->updateOrCreate(
            ['id' => 1],
            [
                'address' => 'Av. Javier Prado Este 4492',
                'schedule' => [
                    ['days' => 'Domingo a Miércoles', 'hours' => 'Hasta las 10 p.m.'],
                    ['days' => 'Jueves', 'hours' => 'Hasta la 1:00 p.m.'],
                    ['days' => 'Viernes y sábado', 'hours' => 'Hasta las 3:00 p.m.'],
                    ['days' => 'Música en vivo', 'hours' => 'Jueves a viernes, desde las 8:00 p.m.'],
                ],
                'about_content' => VisitInfo::composeAboutContent(
                    VisitInfo::DEFAULT_HOLA_HEADLINE,
                    VisitInfo::DEFAULT_HOLA_BODY
                ),
                'about_eyebrow' => VisitInfo::DEFAULT_HOLA_EYEBROW,
                'map_embed_url' => VisitInfo::DEFAULT_MAP_EMBED_URL,
                'phone_reservations' => '991318720',
                'phone_events' => '994848723',
                'email' => 'leilah@gcb.pe',
            ]
        );
    }
}
