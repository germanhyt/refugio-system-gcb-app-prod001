<?php

namespace Database\Seeders;

use App\Models\ServiceItem;
use Illuminate\Database\Seeder;

class ServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Estacionamiento gratis', 'icon_key' => 'parking', 'show_on_home' => true],
            ['title' => 'Servicios Higiénicos', 'icon_key' => 'restrooms', 'show_on_home' => true],
            ['title' => 'Baños para niños', 'icon_key' => 'kids-restroom', 'show_on_home' => true],
            ['title' => 'Bosque Mágico: Zona infantil', 'icon_key' => 'kids-zone', 'show_on_home' => true],
            ['title' => 'Delivery', 'icon_key' => 'delivery', 'show_on_home' => true],
            ['title' => 'Reservas', 'icon_key' => 'reservations', 'show_on_home' => true],
            ['title' => 'Pet friendly', 'icon_key' => 'pet', 'show_on_home' => true],
            ['title' => 'WhatsApp Refugio', 'icon_key' => 'whatsapp', 'show_on_home' => true],
            ['title' => 'Shows en vivo', 'icon_key' => 'live-shows', 'show_on_home' => false],
            ['title' => 'Shows infantiles', 'icon_key' => 'kids-shows', 'show_on_home' => false],
            ['title' => 'Espacios para eventos', 'icon_key' => 'event-spaces', 'show_on_home' => false],
            ['title' => 'Objetos perdidos', 'icon_key' => 'lost-found', 'show_on_home' => false],
            ['title' => 'Alquiler de espacios publicitarios', 'icon_key' => 'ads', 'show_on_home' => false],
            ['title' => 'Alquiler de zonas para eventos', 'icon_key' => 'zones', 'show_on_home' => false],
            ['title' => 'Catering', 'icon_key' => 'catering', 'show_on_home' => false],
            ['title' => 'Lactancia', 'icon_key' => 'nursing', 'show_on_home' => false],
            ['title' => 'Kit de emergencia', 'icon_key' => 'emergency', 'show_on_home' => false],
        ];

        foreach ($items as $index => $item) {
            ServiceItem::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'icon_key' => $item['icon_key'],
                    'sort_order' => $index + 1,
                    'show_on_home' => $item['show_on_home'],
                    'is_active' => true,
                ]
            );
        }
    }
}
