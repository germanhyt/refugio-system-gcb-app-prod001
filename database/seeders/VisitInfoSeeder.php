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
                'about_content' => "TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.\n\nRefugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.",
                'phone_reservations' => '991318720',
                'phone_events' => '994848723',
                'email' => 'hola@refugiogastronomico.pe',
            ]
        );
    }
}
